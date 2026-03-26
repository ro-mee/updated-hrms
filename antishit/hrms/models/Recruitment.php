<?php
/** Recruitment / ATS Models */

class Job {
    private PDO $db;
    public function __construct() { $this->db = db(); }

    public function all(array $filters = [], int $limit = RECORDS_PER_PAGE, int $offset = 0): array {
        $where = ['1=1']; $params = [];
        if (!empty($filters['status']))       { $where[] = "j.status=?"; $params[] = $filters['status']; }
        if (!empty($filters['department_id'])){ $where[] = "j.department_id=?"; $params[] = $filters['department_id']; }
        if (!empty($filters['search']))       { $where[] = "j.title LIKE ?"; $params[] = "%{$filters['search']}%"; }
        $whereStr = implode(' AND ', $where);
        $stmt = $this->db->prepare("
            SELECT j.*, d.name AS department_name, u.first_name, u.last_name,
                   COUNT(a.id) AS applicant_count
            FROM jobs j LEFT JOIN departments d ON j.department_id=d.id
            LEFT JOIN users u ON j.posted_by=u.id
            LEFT JOIN applicants a ON a.job_id=j.id
            WHERE $whereStr GROUP BY j.id 
            ORDER BY (j.status IN ('closed', 'filled')) ASC, j.created_at DESC LIMIT ? OFFSET ?
        ");
        $stmt->execute([...$params, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int {
        $where = ['1=1']; $params = [];
        if (!empty($filters['status'])) { $where[] = "status=?"; $params[] = $filters['status']; }
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM jobs WHERE " . implode(' AND ', $where));
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT j.*, d.name AS department_name FROM jobs j
            LEFT JOIN departments d ON j.department_id=d.id WHERE j.id=?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $d): int {
        $stmt = $this->db->prepare("
            INSERT INTO jobs (title,department_id,position_id,description,requirements,
                salary_min,salary_max,employment_type,vacancies,status,posted_by,deadline)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
        ");
        $stmt->execute([$d['title'],$d['department_id'],$d['position_id']??null,$d['description']??null,
            $d['requirements']??null,$d['salary_min']??null,$d['salary_max']??null,
            $d['employment_type']??'full_time',$d['vacancies']??1,$d['status']??'open',
            $d['posted_by'],$d['deadline']??null]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool {
        $stmt = $this->db->prepare("
            UPDATE jobs SET title=?,department_id=?,position_id=?,description=?,requirements=?,
                salary_min=?,salary_max=?,employment_type=?,vacancies=?,status=?,deadline=?
            WHERE id=?
        ");
        return $stmt->execute([$d['title'],$d['department_id'],$d['position_id']??null,$d['description']??null,
            $d['requirements']??null,$d['salary_min']??null,$d['salary_max']??null,
            $d['employment_type']??'full_time',$d['vacancies']??1,$d['status']??'open',
            $d['deadline']??null,$id]);
    }

    public function delete(int $id): bool {
        return $this->db->prepare("DELETE FROM jobs WHERE id=?")->execute([$id]);
    }

    public function openCount(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM jobs WHERE status='open'")->fetchColumn();
    }

    /** Automatically close job if vacancies are filled */
    public function checkAndAutoClose(int $id): bool {
        $job = $this->findById($id);
        if (!$job || $job['status'] !== 'open') return false;
        
        // Count hired applicants
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM applicants WHERE job_id=? AND status='hired'");
        $stmt->execute([$id]);
        $hired = (int)$stmt->fetchColumn();
        
        return false;
    }

    public function countByStatus(): array {
        $rows = $this->db->query("SELECT status, COUNT(*) AS cnt FROM jobs GROUP BY status")->fetchAll();
        $r = [];
        foreach ($rows as $row) $r[$row['status']] = $row['cnt'];
        return $r;
    }
}

class Applicant {
    private PDO $db;
    public function __construct() { $this->db = db(); }

    public function all(array $filters = [], int $limit = RECORDS_PER_PAGE, int $offset = 0): array {
        $where = ['is_archived=0']; $params = [];
        if (!empty($filters['job_id']))   { $where[] = "a.job_id=?";   $params[] = $filters['job_id']; }
        if (!empty($filters['status']))   { $where[] = "a.status=?";   $params[] = $filters['status']; }
        if (!empty($filters['search']))   { $where[] = "(a.first_name LIKE ? OR a.last_name LIKE ? OR a.email LIKE ?)";
                                            $s="%{$filters['search']}%"; $params=array_merge($params,[$s,$s,$s]); }
        $whereStr = implode(' AND ', $where);
        $stmt = $this->db->prepare("
            SELECT a.*, j.title AS job_title, d.name AS department_name, u.id AS user_id
            FROM applicants a 
            LEFT JOIN jobs j ON a.job_id=j.id 
            LEFT JOIN departments d ON j.department_id=d.id
            LEFT JOIN users u ON a.email = u.email
            WHERE $whereStr ORDER BY a.created_at DESC LIMIT ? OFFSET ?
        ");
        $stmt->execute([...$params, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int {
        $where = ['is_archived=0']; $params = [];
        if (!empty($filters['job_id'])) { $where[] = "job_id=?"; $params[] = $filters['job_id']; }
        if (!empty($filters['status'])) { $where[] = "status=?"; $params[] = $filters['status']; }
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM applicants WHERE " . implode(' AND ', $where));
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT a.*, j.title AS job_title, j.department_id, j.position_id, 
                   j.employment_type, j.salary_min, u.id AS user_id
            FROM applicants a 
            LEFT JOIN jobs j ON a.job_id = j.id 
            LEFT JOIN users u ON a.email = u.email
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $d): int {
        $stmt = $this->db->prepare("
            INSERT INTO applicants (job_id,first_name,last_name,email,phone,cover_letter,source,
                birth_date,gender,civil_status,address,city,sss_number,philhealth_number,
                pagibig_number,tin_number,emergency_contact_name,emergency_contact_phone)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");
        $stmt->execute([
            $d['job_id'],$d['first_name'],$d['last_name'],$d['email'],$d['phone']??null,
            $d['cover_letter']??null,$d['source']??null,$d['birth_date']??null,
            $d['gender']??null,$d['civil_status']??null,$d['address']??null,
            $d['city']??null,$d['sss_number']??null,$d['philhealth_number']??null,
            $d['pagibig_number']??null,$d['tin_number']??null,
            $d['emergency_contact_name']??null,$d['emergency_contact_phone']??null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function archive(int $id): bool {
        return $this->db->prepare("UPDATE applicants SET is_archived=1 WHERE id=?")->execute([$id]);
    }

    public function delete(int $id): bool {
        return $this->db->prepare("DELETE FROM applicants WHERE id=?")->execute([$id]);
    }

    public function updateStatus(int $id, string $status, array $extra = []): bool {
        $fields = 'status=?'; $params = [$status];
        if (!empty($extra['interview_date']))  { $fields .= ',interview_date=?'; $params[] = $extra['interview_date']; }
        if (!empty($extra['interviewed_by']))  { $fields .= ',interviewed_by=?'; $params[] = $extra['interviewed_by']; }
        if (!empty($extra['interview_notes'])) { $fields .= ',interview_notes=?'; $params[] = $extra['interview_notes']; }
        if (!empty($extra['interview_location'])){ $fields .= ',interview_location=?'; $params[] = $extra['interview_location']; }
        $params[] = $id;
        return $this->db->prepare("UPDATE applicants SET $fields WHERE id=?")->execute($params);
    }

    public function uploadResume(int $id, string $filename): bool {
        return $this->db->prepare("UPDATE applicants SET resume=? WHERE id=?")->execute([$filename, $id]);
    }

    public function totalCount(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM applicants")->fetchColumn();
    }

    public function countByStatus(): array {
        $rows = $this->db->query("SELECT status, COUNT(*) AS cnt FROM applicants GROUP BY status")->fetchAll();
        $r = [];
        foreach ($rows as $row) $r[$row['status']] = $row['cnt'];
        return $r;
    }

    public function hiredThisMonth(): int {
        return (int)$this->db->query("
            SELECT COUNT(*) FROM applicants 
            WHERE status='hired' 
            AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
            AND YEAR(created_at) = YEAR(CURRENT_DATE())
        ")->fetchColumn();
    }

    public function upcomingInterviews(int $limit = 5): array {
        $stmt = $this->db->prepare("
            SELECT a.*, j.title AS job_title 
            FROM applicants a 
            JOIN jobs j ON a.job_id = j.id 
            WHERE a.status = 'interview' 
            AND a.interview_date >= CURRENT_DATE() 
            ORDER BY a.interview_date ASC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function countBySource(): array {
        $rows = $this->db->query("SELECT source, COUNT(*) AS cnt FROM applicants WHERE source IS NOT NULL GROUP BY source")->fetchAll();
        $r = [];
        foreach ($rows as $row) $r[$row['source']] = $row['cnt'];
        return $r;
    }
}
