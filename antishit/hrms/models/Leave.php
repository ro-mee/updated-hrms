<?php
/**
 * Leave Model
 */
class Leave {
    private PDO $db;
    public function __construct() { $this->db = db(); }

    public function all(array $filters = [], int $limit = RECORDS_PER_PAGE, int $offset = 0): array {
        $where = ['1=1']; $params = [];
        if (!empty($filters['employee_id'])) { $where[] = "l.employee_id=?"; $params[] = $filters['employee_id']; }
        if (!empty($filters['department_id'])){$where[] = "e.department_id=?"; $params[] = $filters['department_id']; }
        if (!empty($filters['status']))       { $where[] = "l.status=?";       $params[] = $filters['status']; }
        if (!empty($filters['leave_type_id'])){ $where[] = "l.leave_type_id=?";$params[] = $filters['leave_type_id']; }
        if (!empty($filters['year']))         { $where[] = "YEAR(l.start_date)=?"; $params[] = $filters['year']; }
        $whereStr = implode(' AND ', $where);
        $stmt = $this->db->prepare("
            SELECT l.*, CONCAT(u.first_name,' ',u.last_name) AS full_name,
                   u.email, u.avatar, e.employee_number, d.name AS department_name,
                   lt.name AS leave_type_name, lt.is_paid,
                   CONCAT(ru.first_name,' ',ru.last_name) AS reviewed_by_name
            FROM leaves l
            JOIN employees e ON l.employee_id=e.id
            JOIN users u ON e.user_id=u.id
            JOIN departments d ON e.department_id=d.id
            JOIN leave_types lt ON l.leave_type_id=lt.id
            LEFT JOIN users ru ON l.reviewed_by=ru.id
            WHERE $whereStr ORDER BY l.created_at DESC LIMIT ? OFFSET ?
        ");
        $stmt->execute([...$params, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int {
        $where = ['1=1']; $params = [];
        if (!empty($filters['employee_id'])) { $where[] = "l.employee_id=?"; $params[] = $filters['employee_id']; }
        if (!empty($filters['department_id'])){$where[] = "e.department_id=?"; $params[] = $filters['department_id']; }
        if (!empty($filters['status']))       { $where[] = "l.status=?";       $params[] = $filters['status']; }
        $whereStr = implode(' AND ', $where);
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM leaves l JOIN employees e ON l.employee_id=e.id WHERE $whereStr
        ");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT l.*, CONCAT(u.first_name,' ',u.last_name) AS full_name,
                   u.email, e.employee_number, d.name AS department_name,
                   lt.name AS leave_type_name, lt.is_paid, lt.code AS leave_type_code
            FROM leaves l
            JOIN employees e ON l.employee_id=e.id
            JOIN users u ON e.user_id=u.id
            JOIN departments d ON e.department_id=d.id
            JOIN leave_types lt ON l.leave_type_id=lt.id
            WHERE l.id=?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO leaves (employee_id, leave_type_id, start_date, end_date, days_requested, reason, attachment)
            VALUES (?,?,?,?,?,?,?)
        ");
        $stmt->execute([
            $data['employee_id'], $data['leave_type_id'], $data['start_date'],
            $data['end_date'], $data['days_requested'], $data['reason'],
            $data['attachment'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function approve(int $id, int $reviewerId, string $remarks = ''): bool {
        $leave = $this->findById($id);
        if (!$leave) return false;
        $this->db->prepare("UPDATE leaves SET status='approved', reviewed_by=?, reviewed_at=NOW(), remarks=? WHERE id=?")
                  ->execute([$reviewerId, $remarks, $id]);
        // Debit leave balance
        $this->db->prepare("
            UPDATE leave_balances SET used=used+?, remaining=remaining-?
            WHERE employee_id=? AND leave_type_id=? AND year=YEAR(?)
        ")->execute([$leave['days_requested'], $leave['days_requested'], $leave['employee_id'], $leave['leave_type_id'], $leave['start_date']]);
        // Mark attendance as on_leave for the period
        $start = new DateTime($leave['start_date']);
        $end   = new DateTime($leave['end_date']);
        while ($start <= $end) {
            $dow = (int)$start->format('N');
            if ($dow < 6) {
                $this->db->prepare("
                    INSERT IGNORE INTO attendance (employee_id, date, status)
                    VALUES (?,?,'on_leave')
                ")->execute([$leave['employee_id'], $start->format('Y-m-d')]);
            }
            $start->modify('+1 day');
        }
        return true;
    }

    public function reject(int $id, int $reviewerId, string $remarks = ''): bool {
        $stmt = $this->db->prepare("UPDATE leaves SET status='rejected', reviewed_by=?, reviewed_at=NOW(), remarks=? WHERE id=?");
        return $stmt->execute([$reviewerId, $remarks, $id]);
    }

    public function cancel(int $id, int $employeeId): bool {
        $stmt = $this->db->prepare("UPDATE leaves SET status='cancelled' WHERE id=? AND employee_id=? AND status='pending'");
        return $stmt->execute([$id, $employeeId]);
    }

    public function getBalance(int $employeeId, int $year): array {
        $stmt = $this->db->prepare("
            SELECT lb.*, lt.name AS leave_type_name, lt.code, lt.is_paid
            FROM leave_balances lb JOIN leave_types lt ON lb.leave_type_id=lt.id
            WHERE lb.employee_id=? AND lb.year=? ORDER BY lt.name
        ");
        $stmt->execute([$employeeId, $year]);
        return $stmt->fetchAll();
    }

    public function leaveTypes(): array {
        return $this->db->query("SELECT * FROM leave_types WHERE is_active=1 ORDER BY name")->fetchAll();
    }

    public function pendingCount(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM leaves WHERE status='pending'")->fetchColumn();
    }

    public function hasOverlap(int $employeeId, string $start, string $end, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM leaves WHERE employee_id=? AND status NOT IN ('rejected','cancelled')
                AND start_date<=? AND end_date>=?";
        $params = [$employeeId, $end, $start];
        if ($excludeId) { $sql .= " AND id!=?"; $params[] = $excludeId; }
        return (int)$this->db->prepare($sql)->execute($params) && (int)$this->db->query("SELECT FOUND_ROWS()")->fetchColumn();
    }
}
