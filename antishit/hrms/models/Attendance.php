<?php
/**
 * Attendance Model
 */
class Attendance {
    private PDO $db;
    public function __construct() { $this->db = db(); }

    public function todayRecord(int $employeeId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM attendance WHERE employee_id=? AND date=CURDATE()");
        $stmt->execute([$employeeId]);
        return $stmt->fetch() ?: null;
    }

    public function clockIn(int $employeeId): bool {
        // Determine status based on work_start_time setting
        $startTime = $this->db->query("SELECT value FROM settings WHERE key_name='work_start_time'")->fetchColumn() ?: '08:00';
        $threshold = (int)($this->db->query("SELECT value FROM settings WHERE key_name='late_threshold_min'")->fetchColumn() ?: 15);
        $now       = new DateTime();
        $workStart = new DateTime(date('Y-m-d') . ' ' . $startTime);
        $workStart->modify("+$threshold minutes");
        $status    = ($now > $workStart) ? 'late' : 'present';

        $stmt = $this->db->prepare("
            INSERT INTO attendance (employee_id, date, clock_in, status)
            VALUES (?, CURDATE(), NOW(), ?)
            ON DUPLICATE KEY UPDATE clock_in=NOW(), status=VALUES(status)
        ");
        return $stmt->execute([$employeeId, $status]);
    }

    public function clockOut(int $employeeId): bool {
        $today = $this->todayRecord($employeeId);
        if (!$today || empty($today['clock_in'])) return false;
        $in    = new DateTime($today['clock_in']);
        $out   = new DateTime();
        $hours = round($out->getTimestamp() - $in->getTimestamp()) / 3600;
        // Overtime if hours > 8
        $ot    = max(0, $hours - 8);

        $stmt = $this->db->prepare("
            UPDATE attendance SET clock_out=NOW(), hours_worked=?, overtime_hours=?
            WHERE employee_id=? AND date=CURDATE()
        ");
        return $stmt->execute([round($hours, 2), round($ot, 2), $employeeId]);
    }

    public function list(array $filters = [], int $limit = RECORDS_PER_PAGE, int $offset = 0): array {
        $where = ['1=1']; $params = [];
        if (!empty($filters['employee_id'])) { $where[] = "a.employee_id=?"; $params[] = $filters['employee_id']; }
        if (!empty($filters['department_id'])) { $where[] = "e.department_id=?"; $params[] = $filters['department_id']; }
        if (!empty($filters['date_from']))    { $where[] = "a.date>=?"; $params[] = $filters['date_from']; }
        if (!empty($filters['date_to']))      { $where[] = "a.date<=?"; $params[] = $filters['date_to']; }
        if (!empty($filters['status']))       { $where[] = "a.status=?"; $params[] = $filters['status']; }
        if (!empty($filters['month']))        { $where[] = "MONTH(a.date)=?"; $params[] = $filters['month']; }
        if (!empty($filters['year']))         { $where[] = "YEAR(a.date)=?"; $params[] = $filters['year']; }
        $whereStr = implode(' AND ', $where);
        $stmt = $this->db->prepare("
            SELECT a.*, CONCAT(u.first_name,' ',u.last_name) AS full_name,
                   d.name AS department_name, e.employee_number
            FROM attendance a
            JOIN employees e ON a.employee_id=e.id
            JOIN users u ON e.user_id=u.id
            JOIN departments d ON e.department_id=d.id
            WHERE $whereStr ORDER BY a.date DESC, a.clock_in DESC LIMIT ? OFFSET ?
        ");
        $stmt->execute([...$params, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int {
        $where = ['1=1']; $params = [];
        if (!empty($filters['employee_id'])) { $where[] = "a.employee_id=?"; $params[] = $filters['employee_id']; }
        if (!empty($filters['department_id'])) { $where[] = "e.department_id=?"; $params[] = $filters['department_id']; }
        if (!empty($filters['date_from']))    { $where[] = "a.date>=?"; $params[] = $filters['date_from']; }
        if (!empty($filters['date_to']))      { $where[] = "a.date<=?"; $params[] = $filters['date_to']; }
        if (!empty($filters['status']))       { $where[] = "a.status=?"; $params[] = $filters['status']; }
        $whereStr = implode(' AND ', $where);
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM attendance a JOIN employees e ON a.employee_id=e.id WHERE $whereStr
        ");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function markAbsent(): void {
        // Call nightly via cron/scheduler: mark active employees with no attendance today as absent
        $this->db->exec("
            INSERT IGNORE INTO attendance (employee_id, date, status)
            SELECT e.id, CURDATE(), 'absent'
            FROM employees e
            WHERE e.status='active' AND DAYOFWEEK(CURDATE()) NOT IN (1,7)
              AND e.id NOT IN (SELECT employee_id FROM attendance WHERE date=CURDATE())
        ");
    }

    public function monthlySummary(int $employeeId, int $year, int $month): array {
        $stmt = $this->db->prepare("
            SELECT status, COUNT(*) AS cnt, SUM(hours_worked) AS total_hours, SUM(overtime_hours) AS total_ot
            FROM attendance WHERE employee_id=? AND YEAR(date)=? AND MONTH(date)=?
            GROUP BY status
        ");
        $stmt->execute([$employeeId, $year, $month]);
        return $stmt->fetchAll();
    }

    public function todayStats(?int $departmentId = null): array {
        $query = "SELECT 
                    SUM(a.status='present') AS present,
                    SUM(a.status='late') AS late,
                    SUM(a.status='absent') AS absent,
                    SUM(a.status='on_leave') AS on_leave,
                    COUNT(*) AS total
                  FROM attendance a ";
        $params = [];
        if ($departmentId) {
            $query .= "JOIN employees e ON a.employee_id = e.id WHERE a.date=CURDATE() AND e.department_id = ?";
            $params[] = $departmentId;
        } else {
            $query .= "WHERE a.date=CURDATE()";
        }
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $res = $stmt->fetch();
        return [
            'present' => (int)($res['present']??0),
            'late'    => (int)($res['late']??0),
            'absent'  => (int)($res['absent']??0),
            'on_leave'=> (int)($res['on_leave']??0),
            'total'   => (int)($res['total']??0)
        ];
    }

    public function upsert(int $employeeId, string $date, array $data): bool {
        $fields = ['employee_id','date'];
        $vals   = [$employeeId, $date];
        $updates= [];
        $allowed= ['clock_in','clock_out','hours_worked','overtime_hours','status','remarks'];
        foreach ($allowed as $f) {
            if (array_key_exists($f,$data)) {
                $fields[] = $f; $vals[] = $data[$f];
                $updates[] = "$f=VALUES($f)";
            }
        }
        $placeholders = implode(',', array_fill(0, count($vals), '?'));
        $sql = "INSERT INTO attendance (" . implode(',', $fields) . ") VALUES ($placeholders)"
             . " ON DUPLICATE KEY UPDATE " . implode(', ', $updates);
        return $this->db->prepare($sql)->execute($vals);
    }
}
