<?php
/**
 * Payroll Model
 */
class Payroll {
    private PDO $db;
    public function __construct() { $this->db = db(); }

    public function createPeriod(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO payroll_periods (period_name, start_date, end_date, pay_date, status, created_by)
            VALUES (?,?,?,?,'draft',?)
        ");
        $stmt->execute([$data['period_name'],$data['start_date'],$data['end_date'],$data['pay_date'],$data['created_by']]);
        return (int)$this->db->lastInsertId();
    }

    public function periods(int $limit = RECORDS_PER_PAGE, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT pp.*, CONCAT(u.first_name,' ',u.last_name) AS created_by_name,
                   COUNT(p.id) AS employee_count, SUM(p.net_pay) AS total_net
            FROM payroll_periods pp JOIN users u ON pp.created_by=u.id
            LEFT JOIN payroll p ON p.period_id=pp.id
            GROUP BY pp.id ORDER BY pp.created_at DESC LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function findPeriod(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM payroll_periods WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function generateForPeriod(int $periodId): int {
        try {
            $period = $this->findPeriod($periodId);
            if (!$period) return 0;

            // Get settings
            $settings = $this->db->query("SELECT key_name, value FROM settings WHERE group_name='payroll'")->fetchAll(PDO::FETCH_KEY_PAIR);
            $otRate   = (float)($settings['overtime_rate'] ?? 1.25);
            $sssRate  = (float)($settings['sss_employee_rate'] ?? 0.045);
            $phRate   = (float)($settings['philhealth_rate'] ?? 0.05);
            $piRate   = (float)($settings['pagibig_rate'] ?? 0.02);

            $totalWorkDays = workingDaysBetween($period['start_date'], $period['end_date']);
            
            // Use direct JOIN to avoid view dependency issues
            $employees = db()->query("
                SELECT e.*, u.first_name, u.last_name 
                FROM employees e 
                JOIN users u ON e.user_id = u.id 
                WHERE e.status='active'
            ")->fetchAll();
            
            $count = 0;
            foreach ($employees as $emp) {
                // Count actual days worked
                // Count actual days worked (present, late, half_day) + Approved Paid Leaves
                $stmt = db()->prepare("
                    SELECT COUNT(a.id) FROM attendance a
                    LEFT JOIN leaves l ON a.employee_id = l.employee_id 
                        AND a.date BETWEEN l.start_date AND l.end_date 
                        AND l.status = 'approved'
                    LEFT JOIN leave_types lt ON l.leave_type_id = lt.id
                    WHERE a.employee_id=? AND a.date BETWEEN ? AND ? 
                      AND (a.status IN ('present','late','half_day') 
                           OR (a.status = 'on_leave' AND lt.is_paid = 1))
                ");
                $stmt->execute([$emp['id'], $period['start_date'], $period['end_date']]);
                $daysWorked = (int)$stmt->fetchColumn();

                // Count overtime hours
                $stmt2 = db()->prepare("
                    SELECT COALESCE(SUM(overtime_hours),0) FROM attendance
                    WHERE employee_id=? AND date BETWEEN ? AND ?
                ");
                $stmt2->execute([$emp['id'], $period['start_date'], $period['end_date']]);
                $otHours = (float)$stmt2->fetchColumn();

                $sal = (float)($emp['basic_salary'] ?? 0);
                $dailyRate  = $sal / max(1, $totalWorkDays);
                $hourlyRate = $sal / (max(1,$totalWorkDays) * 8);
                $otPay      = round($hourlyRate * $otHours * $otRate, 2);
                $grossPay   = ($dailyRate * $daysWorked) + $otPay;

                $sssDed  = min($grossPay * $sssRate, 900);
                $phDed   = $grossPay * $phRate;
                $piDed   = min($grossPay * $piRate, 100);
                $taxDed  = $this->computeWithholdingTax($grossPay);
                $totalDed= $sssDed + $phDed + $piDed + $taxDed;
                $netPay  = max(0, $grossPay - $totalDed);

                $ins = db()->prepare("
                    INSERT INTO payroll (period_id, employee_id, basic_salary, days_worked, overtime_hours, overtime_pay,
                        gross_pay, sss_deduction, philhealth_deduction, pagibig_deduction, tax_deduction, total_deductions, net_pay, status)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,'draft')
                    ON DUPLICATE KEY UPDATE basic_salary=VALUES(basic_salary), days_worked=VALUES(days_worked),
                        overtime_hours=VALUES(overtime_hours), overtime_pay=VALUES(overtime_pay),
                        gross_pay=VALUES(gross_pay), sss_deduction=VALUES(sss_deduction),
                        philhealth_deduction=VALUES(philhealth_deduction), pagibig_deduction=VALUES(pagibig_deduction),
                        tax_deduction=VALUES(tax_deduction), total_deductions=VALUES(total_deductions),
                        net_pay=VALUES(net_pay)
                ");
                $ins->execute([
                    $periodId, $emp['id'], $sal, $daysWorked, $otHours, $otPay,
                    round($grossPay, 2), round($sssDed, 2), round($phDed, 2), round($piDed, 2),
                    round($taxDed, 2), round($totalDed, 2), round($netPay, 2),
                ]);
                $count++;
            }
            // Update period status to processing so Approve button shows up
            $this->db->prepare("UPDATE payroll_periods SET status='processing' WHERE id=?")->execute([$periodId]);
            return $count;
        } catch (Exception $e) {
            error_log("Payroll Error: " . $e->getMessage());
            return -1; // Indicate error
        }
    }

    private function computeWithholdingTax(float $monthly): float {
        // Simplified Philippine withholding tax (monthly)
        if ($monthly <= 20833) return 0;
        if ($monthly <= 33333) return ($monthly - 20833) * 0.20;
        if ($monthly <= 66667) return 2500  + ($monthly - 33333) * 0.25;
        if ($monthly <= 166667) return 10833 + ($monthly - 66667) * 0.30;
        if ($monthly <= 666667) return 40833 + ($monthly - 166667) * 0.32;
        return 200833 + ($monthly - 666667) * 0.35;
    }

    public function payslips(int $periodId, int $limit = RECORDS_PER_PAGE, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT p.*, CONCAT(u.first_name,' ',u.last_name) AS full_name,
                   u.email, e.employee_number, d.name AS department_name, pos.title AS position_title,
                   u.avatar
            FROM payroll p
            JOIN employees e ON p.employee_id=e.id
            JOIN users u ON e.user_id=u.id
            JOIN departments d ON e.department_id=d.id
            JOIN positions pos ON e.position_id=pos.id
            WHERE p.period_id=? ORDER BY d.name, u.last_name LIMIT ? OFFSET ?
        ");
        $stmt->execute([$periodId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function findPayslip(int $periodId, int $employeeId): ?array {
        $stmt = $this->db->prepare("
            SELECT p.*, pp.period_name, pp.pay_date, pp.start_date, pp.end_date,
                   CONCAT(u.first_name,' ',u.last_name) AS full_name,
                   e.employee_number, d.name AS department_name, pos.title AS position_title, u.avatar
            FROM payroll p
            JOIN payroll_periods pp ON p.period_id=pp.id
            JOIN employees e ON p.employee_id=e.id
            JOIN users u ON e.user_id=u.id
            JOIN departments d ON e.department_id=d.id
            JOIN positions pos ON e.position_id=pos.id
            WHERE p.period_id=? AND p.employee_id=?
        ");
        $stmt->execute([$periodId, $employeeId]);
        return $stmt->fetch() ?: null;
    }

    public function employeePayslips(int $employeeId): array {
        $stmt = $this->db->prepare("
            SELECT p.*, pp.period_name, pp.pay_date, pp.status AS period_status
            FROM payroll p JOIN payroll_periods pp ON p.period_id=pp.id
            WHERE p.employee_id=? ORDER BY pp.pay_date DESC
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll();
    }

    public function approvePeriod(int $periodId, int $approverId): bool {
        try {
            $this->db->prepare("UPDATE payroll SET status='approved' WHERE period_id=?")->execute([$periodId]);
            $res = $this->db->prepare("UPDATE payroll_periods SET status='approved', approved_by=?, approved_at=NOW() WHERE id=?")
                             ->execute([$approverId, $periodId]);
            if (!$res) {
                error_log("Failed to update payroll_periods status to approved for ID: " . $periodId);
                return false;
            }

            // Batch notify all employees in this period
            $stmt = $this->db->prepare("
                INSERT INTO notifications (user_id, title, message, type, module, module_id)
                SELECT e.user_id, 'Payslip Available', CONCAT('Your payslip for ', pp.period_name, ' is now available.'), 'success', 'payroll', pp.id
                FROM payroll p
                JOIN employees e ON p.employee_id = e.id
                JOIN payroll_periods pp ON p.period_id = pp.id
                WHERE p.period_id = ? AND e.user_id IS NOT NULL
            ");
            $stmt->execute([$periodId]);

            return true;
        } catch (Exception $e) {
            error_log("Approve Period Error: " . $e->getMessage());
            return false;
        }
    }

    public function markPaid(int $periodId): bool {
        try {
            $this->db->prepare("UPDATE payroll SET status='paid' WHERE period_id=?")->execute([$periodId]);
            $res = $this->db->prepare("UPDATE payroll_periods SET status='paid' WHERE id=?")->execute([$periodId]);
            if (!$res) {
                error_log("Failed to update payroll_periods status to paid for ID: " . $periodId);
            }
            return $res;
        } catch (Exception $e) {
            error_log("Mark Paid Error: " . $e->getMessage());
            return false;
        }
    }

    public function summary(): array {
        return $this->db->query("
            SELECT pp.id, pp.period_name, pp.start_date, pp.end_date, pp.pay_date, pp.status,
                   COUNT(p.id) AS employees, 
                   SUM(p.gross_pay) AS total_gross, 
                   SUM(p.total_deductions) AS total_deductions,
                   SUM(p.net_pay) AS total_net
            FROM payroll_periods pp 
            LEFT JOIN payroll p ON p.period_id = pp.id
            GROUP BY pp.id 
            ORDER BY pp.pay_date DESC 
            LIMIT 12
        ")->fetchAll();
    }
}
