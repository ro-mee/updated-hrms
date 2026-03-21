<?php
/** Performance, Training, Document, Notification, AuditLog, Setting Models */

class Performance {
    private PDO $db;
    public function __construct() { $this->db = db(); }

    public function all(array $filters = [], int $limit = RECORDS_PER_PAGE, int $offset = 0): array {
        $where = ['1=1']; $params = [];
        if (!empty($filters['employee_id'])) { $where[] = "pr.employee_id=?"; $params[] = $filters['employee_id']; }
        if (!empty($filters['reviewer_id'])) { $where[] = "pr.reviewer_id=?"; $params[] = $filters['reviewer_id']; }
        if (!empty($filters['status']))       { $where[] = "pr.status=?"; $params[] = $filters['status']; }
        $whereStr = implode(' AND ', $where);
        $stmt = $this->db->prepare("
            SELECT pr.*, CONCAT(u.first_name,' ',u.last_name) AS employee_name,
                   CONCAT(r.first_name,' ',r.last_name) AS reviewer_name,
                   e.employee_number, d.name AS department_name
            FROM performance_reviews pr
            JOIN employees e ON pr.employee_id=e.id JOIN users u ON e.user_id=u.id
            JOIN departments d ON e.department_id=d.id JOIN users r ON pr.reviewer_id=r.id
            WHERE $whereStr ORDER BY pr.review_date DESC LIMIT ? OFFSET ?
        ");
        $stmt->execute([...$params, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT pr.*, CONCAT(u.first_name,' ',u.last_name) AS employee_name,
                   CONCAT(r.first_name,' ',r.last_name) AS reviewer_name, e.employee_number
            FROM performance_reviews pr
            JOIN employees e ON pr.employee_id=e.id JOIN users u ON e.user_id=u.id
            JOIN users r ON pr.reviewer_id=r.id WHERE pr.id=?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $d): int {
        $stmt = $this->db->prepare("
            INSERT INTO performance_reviews (employee_id,reviewer_id,review_period,review_date,strengths,improvements,goals_next_period,status)
            VALUES (?,?,?,?,?,?,?,'draft')
        ");
        $stmt->execute([$d['employee_id'],$d['reviewer_id'],$d['review_period'],$d['review_date'],
            $d['strengths']??null,$d['improvements']??null,$d['goals_next_period']??null]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool {
        $stmt = $this->db->prepare("
            UPDATE performance_reviews SET overall_rating=?,strengths=?,improvements=?,goals_next_period=?,status=? WHERE id=?
        ");
        return $stmt->execute([$d['overall_rating']??null,$d['strengths']??null,$d['improvements']??null,
            $d['goals_next_period']??null,$d['status']??'draft',$id]);
    }

    public function kpis(): array {
        return $this->db->query("SELECT * FROM kpis WHERE is_active=1 ORDER BY name")->fetchAll();
    }

    public function saveKpiScores(int $reviewId, array $scores): void {
        $this->db->prepare("DELETE FROM performance_kpi_scores WHERE review_id=?")->execute([$reviewId]);
        foreach ($scores as $kpiId => $score) {
            $this->db->prepare("INSERT INTO performance_kpi_scores (review_id,kpi_id,score) VALUES (?,?,?)")
                     ->execute([$reviewId, $kpiId, $score]);
        }
        // Compute & save weighted overall rating
        $stmt = $this->db->prepare("
            SELECT AVG(pks.score * k.weight) / AVG(k.weight) AS weighted_avg
            FROM performance_kpi_scores pks JOIN kpis k ON pks.kpi_id=k.id WHERE pks.review_id=?
        ");
        $stmt->execute([$reviewId]);
        $avg = $stmt->fetchColumn();
        $this->db->prepare("UPDATE performance_reviews SET overall_rating=? WHERE id=?")->execute([$avg, $reviewId]);
    }
}

class Training {
    private PDO $db;
    public function __construct() { $this->db = db(); }

    public function all(array $filters = [], int $limit = RECORDS_PER_PAGE, int $offset = 0): array {
        $where = ['1=1']; $params = [];
        if (!empty($filters['status'])) { $where[] = "t.status=?"; $params[] = $filters['status']; }
        $stmt = $this->db->prepare("
            SELECT t.*, CONCAT(u.first_name,' ',u.last_name) AS created_by_name,
                   COUNT(te.id) AS enrolled_count
            FROM trainings t LEFT JOIN users u ON t.created_by=u.id
            LEFT JOIN training_enrollments te ON te.training_id=t.id
            WHERE " . implode(' AND ', $where) . " GROUP BY t.id ORDER BY t.start_date DESC LIMIT ? OFFSET ?
        ");
        $stmt->execute([...$params, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM trainings WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $d): int {
        $stmt = $this->db->prepare("INSERT INTO trainings (title,description,trainer,start_date,end_date,location,max_participants,cost,status,created_by) VALUES (?,?,?,?,?,?,?,?,'scheduled',?)");
        $stmt->execute([$d['title'],$d['description']??null,$d['trainer']??null,$d['start_date'],
            $d['end_date'],$d['location']??null,$d['max_participants']??null,$d['cost']??0,$d['created_by']]);
        return (int)$this->db->lastInsertId();
    }

    public function enroll(int $trainingId, int $employeeId): bool {
        $stmt = $this->db->prepare("INSERT IGNORE INTO training_enrollments (training_id,employee_id) VALUES (?,?)");
        return $stmt->execute([$trainingId, $employeeId]);
    }

    public function enrollments(int $trainingId): array {
        $stmt = $this->db->prepare("
            SELECT te.*, CONCAT(u.first_name,' ',u.last_name) AS full_name, e.employee_number, d.name AS dept
            FROM training_enrollments te
            JOIN employees e ON te.employee_id=e.id JOIN users u ON e.user_id=u.id JOIN departments d ON e.department_id=d.id
            WHERE te.training_id=?
        ");
        $stmt->execute([$trainingId]);
        return $stmt->fetchAll();
    }

    public function employeeTrainings(int $employeeId): array {
        $stmt = $this->db->prepare("
            SELECT t.*, te.status AS enroll_status, te.score, te.certificate
            FROM training_enrollments te JOIN trainings t ON te.training_id=t.id
            WHERE te.employee_id=? ORDER BY t.start_date DESC
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll();
    }
}

class Document {
    private PDO $db;
    public function __construct() { $this->db = db(); }

    public function all(array $filters = [], int $limit = RECORDS_PER_PAGE, int $offset = 0): array {
        $where = ['1=1']; $params = [];
        if (!empty($filters['employee_id'])) { $where[] = "(d.employee_id=? OR d.is_public=1)"; $params[] = $filters['employee_id']; }
        if (!empty($filters['category']))    { $where[] = "d.category=?"; $params[] = $filters['category']; }
        if (!empty($filters['admin_view']) && $filters['admin_view']) {
            if (!empty($filters['employee_id2'])) { $where[] = "d.employee_id=?"; $params[] = $filters['employee_id2']; }
        }
        $whereStr = implode(' AND ', $where);
        $stmt = $this->db->prepare("
            SELECT d.*, CONCAT(eu.first_name,' ',eu.last_name) AS employee_name,
                   CONCAT(uu.first_name,' ',uu.last_name) AS uploaded_by_name
            FROM documents d
            LEFT JOIN employees e ON d.employee_id=e.id LEFT JOIN users eu ON e.user_id=eu.id
            JOIN users uu ON d.uploaded_by=uu.id
            WHERE $whereStr ORDER BY d.created_at DESC LIMIT ? OFFSET ?
        ");
        $stmt->execute([...$params, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function create(array $d): int {
        $stmt = $this->db->prepare("INSERT INTO documents (employee_id,title,category,filename,file_type,file_size,uploaded_by,is_public) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$d['employee_id']??null,$d['title'],$d['category']??'other',
            $d['filename'],$d['file_type']??null,$d['file_size']??null,$d['uploaded_by'],$d['is_public']??0]);
        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM documents WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function delete(int $id): bool {
        return $this->db->prepare("DELETE FROM documents WHERE id=?")->execute([$id]);
    }
}

class Notification {
    private PDO $db;
    public function __construct() { $this->db = db(); }

    public function forUser(int $userId, bool $unreadOnly = false, int $limit = 20): array {
        $where = "user_id=?";
        if ($unreadOnly) $where .= " AND is_read=0";
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE $where ORDER BY created_at DESC LIMIT $limit");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function unreadCount(int $userId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    public function markRead(int $id, int $userId): bool {
        return $this->db->prepare("UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?")->execute([$id, $userId]);
    }

    public function markAllRead(int $userId): bool {
        return $this->db->prepare("UPDATE notifications SET is_read=1 WHERE user_id=?")->execute([$userId]);
    }

    public function create(int $userId, string $title, string $message, string $type = 'info', string $module = '', ?int $moduleId = null): int {
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id,title,message,type,module,module_id) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$userId, $title, $message, $type, $module, $moduleId]);
        return (int)$this->db->lastInsertId();
    }
}

class AuditLog {
    private PDO $db;
    public function __construct() { $this->db = db(); }

    public function all(array $filters = [], int $limit = RECORDS_PER_PAGE, int $offset = 0): array {
        $where = ['1=1']; $params = [];
        if (!empty($filters['user_id'])) { $where[] = "al.user_id=?"; $params[] = $filters['user_id']; }
        if (!empty($filters['module']))  { $where[] = "al.module=?";   $params[] = $filters['module']; }
        if (!empty($filters['date_from'])){ $where[] = "DATE(al.created_at)>=?"; $params[] = $filters['date_from']; }
        if (!empty($filters['date_to'])) { $where[] = "DATE(al.created_at)<=?"; $params[] = $filters['date_to']; }
        if (!empty($filters['search'])) {
            $where[] = "(CONCAT(u.first_name,' ',u.last_name) LIKE ? OR u.email LIKE ?)";
            $params[] = "%" . $filters['search'] . "%";
            $params[] = "%" . $filters['search'] . "%";
        }
        $whereStr = implode(' AND ', $where);
        $stmt = $this->db->prepare("
            SELECT al.*, CONCAT(u.first_name,' ',u.last_name) AS full_name, r.name AS role_name
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id=u.id LEFT JOIN roles r ON u.role_id=r.id
            WHERE $whereStr ORDER BY al.created_at DESC LIMIT ? OFFSET ?
        ");
        $stmt->execute([...$params, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int {
        $where = ['1=1']; $params = [];
        if (!empty($filters['module']))  { $where[] = "al.module=?";   $params[] = $filters['module']; }
        if (!empty($filters['date_from'])){ $where[] = "DATE(al.created_at)>=?"; $params[] = $filters['date_from']; }
        if (!empty($filters['date_to'])) { $where[] = "DATE(al.created_at)<=?"; $params[] = $filters['date_to']; }
        
        $join = "";
        if (!empty($filters['search'])) {
            $join = "LEFT JOIN users u ON al.user_id=u.id";
            $where[] = "(CONCAT(u.first_name,' ',u.last_name) LIKE ? OR u.email LIKE ?)";
            $params[] = "%" . $filters['search'] . "%";
            $params[] = "%" . $filters['search'] . "%";
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM audit_logs al $join WHERE " . implode(' AND ', $where));
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}

class Setting {
    private PDO $db;
    public function __construct() { $this->db = db(); }

    public function all(): array {
        $rows = $this->db->query("SELECT * FROM settings ORDER BY group_name, key_name")->fetchAll();
        $grouped = [];
        foreach ($rows as $r) { $grouped[$r['group_name']][] = $r; }
        return $grouped;
    }

    public function get(string $key, string $default = ''): string {
        $stmt = $this->db->prepare("SELECT value FROM settings WHERE key_name=?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return $val !== false ? $val : $default;
    }

    public function set(string $key, string $value): bool {
        $stmt = $this->db->prepare("INSERT INTO settings (key_name,value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=?");
        return $stmt->execute([$key, $value, $value]);
    }

    public function bulkUpdate(array $data): void {
        foreach ($data as $key => $value) {
            $this->set(sanitizeInput($key), sanitizeInput((string)$value));
        }
    }
}
