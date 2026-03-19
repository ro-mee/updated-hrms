<?php
/**
 * Employee Model
 */
class Employee {
    private PDO $db;
    public function __construct() { $this->db = db(); }

    public function all(array $filters = [], int $limit = RECORDS_PER_PAGE, int $offset = 0): array {
        $where = ['1=1']; $params = [];
        if (!empty($filters['search'])) {
            $where[] = "(first_name LIKE ? OR last_name LIKE ? OR employee_number LIKE ? OR email LIKE ?)";
            $s = "%{$filters['search']}%";
            $params = array_merge($params, [$s,$s,$s,$s]);
        }
        if (!empty($filters['department_id'])) { $where[] = "department_id = ?"; $params[] = $filters['department_id']; }
        if (!empty($filters['status']))         { $where[] = "status = ?";         $params[] = $filters['status']; }
        if (!empty($filters['employment_type']))  { $where[] = "employment_type = ?"; $params[] = $filters['employment_type']; }
        $whereStr = implode(' AND ', $where);
        $stmt = $this->db->prepare("SELECT * FROM v_employees WHERE $whereStr ORDER BY last_name, first_name LIMIT ? OFFSET ?");
        $stmt->execute([...$params, $limit, $offset]);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) $r = $this->decryptRecord($r);
        return $rows;
    }

    public function count(array $filters = []): int {
        $where = ['1=1']; $params = [];
        if (!empty($filters['search'])) {
            $where[] = "(first_name LIKE ? OR last_name LIKE ? OR employee_number LIKE ? OR email LIKE ?)";
            $s = "%{$filters['search']}%";
            $params = array_merge($params, [$s,$s,$s,$s]);
        }
        if (!empty($filters['department_id'])) { $where[] = "department_id = ?"; $params[] = $filters['department_id']; }
        if (!empty($filters['status']))         { $where[] = "status = ?";         $params[] = $filters['status']; }
        $whereStr = implode(' AND ', $where);
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM v_employees WHERE $whereStr");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM v_employees WHERE id = ?");
        $stmt->execute([$id]);
        return $this->decryptRecord($stmt->fetch() ?: null);
    }

    public function findByUserId(int $userId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM v_employees WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $this->decryptRecord($stmt->fetch() ?: null);
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO employees (user_id, employee_number, department_id, position_id, manager_id,
                employment_type, status, date_hired, date_regularized, basic_salary,
                phone, address, city, birth_date, gender, civil_status,
                sss_number, philhealth_number, pagibig_number, tin_number,
                emergency_contact_name, emergency_contact_phone, notes)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");
        $stmt->execute([
            $data['user_id'], $data['employee_number'], $data['department_id'], $data['position_id'],
            $data['manager_id'] ?? null, $data['employment_type'] ?? 'full_time', $data['status'] ?? 'active',
            $data['date_hired'], $data['date_regularized'] ?? null, encrypt_pii((string)($data['basic_salary'] ?? 0)),
            $data['phone'] ?? null, $data['address'] ?? null, $data['city'] ?? null,
            $data['birth_date'] ?? null, $data['gender'] ?? null, $data['civil_status'] ?? null,
            encrypt_pii($data['sss_number'] ?? null), encrypt_pii($data['philhealth_number'] ?? null),
            encrypt_pii($data['pagibig_number'] ?? null), encrypt_pii($data['tin_number'] ?? null),
            $data['emergency_contact_name'] ?? null, $data['emergency_contact_phone'] ?? null,
            $data['notes'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $allowed = ['department_id','position_id','manager_id','employment_type','status',
                    'date_hired','date_regularized','date_separated','basic_salary','phone',
                    'address','city','birth_date','gender','civil_status','sss_number',
                    'philhealth_number','pagibig_number','tin_number',
                    'emergency_contact_name','emergency_contact_phone','notes'];
        $piiFields = ['basic_salary', 'sss_number', 'philhealth_number', 'pagibig_number', 'tin_number'];
        $fields = []; $params = [];
        foreach ($allowed as $f) {
            if (array_key_exists($f, $data)) { 
                $fields[] = "$f = ?"; 
                $params[] = in_array($f, $piiFields) ? encrypt_pii((string)$data[$f]) : $data[$f]; 
            }
        }
        if (empty($fields)) return false;
        $params[] = $id;
        return $this->db->prepare("UPDATE employees SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
    }

    public function generateEmployeeNumber(): string {
        $last = $this->db->query("SELECT employee_number FROM employees ORDER BY id DESC LIMIT 1")->fetchColumn();
        $num  = $last ? (int)substr($last, 4) + 1 : 1;
        return 'EMP-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function departments(): array {
        return $this->db->query("SELECT * FROM departments WHERE is_active=1 ORDER BY name")->fetchAll();
    }

    public function positions(int $deptId = 0): array {
        if ($deptId) {
            $stmt = $this->db->prepare("SELECT * FROM positions WHERE department_id = ? AND is_active=1 ORDER BY title");
            $stmt->execute([$deptId]);
        } else {
            $stmt = $this->db->query("SELECT * FROM positions WHERE is_active=1 ORDER BY title");
        }
        return $stmt->fetchAll();
    }

    public function managers(): array {
        return $this->db->query("
            SELECT e.id, CONCAT(u.first_name,' ',u.last_name) AS full_name, d.name AS dept
            FROM employees e JOIN users u ON e.user_id=u.id JOIN departments d ON e.department_id=d.id
            WHERE e.status='active' ORDER BY full_name
        ")->fetchAll();
    }

    public function countByStatus(): array {
        $rows = $this->db->query("SELECT status, COUNT(*) AS cnt FROM employees GROUP BY status")->fetchAll();
        $result = [];
        foreach ($rows as $r) $result[$r['status']] = $r['cnt'];
        return $result;
    }

    public function countByDepartment(): array {
        return $this->db->query("
            SELECT d.name, COUNT(e.id) AS cnt
            FROM departments d LEFT JOIN employees e ON e.department_id=d.id AND e.status='active'
            GROUP BY d.id ORDER BY cnt DESC
        ")->fetchAll();
    }

    public function softDelete(int $id): bool {
        return $this->db->prepare("UPDATE employees SET status='terminated' WHERE id=?")->execute([$id]);
    }

    public function search(string $q): array {
        $stmt = $this->db->prepare("
            SELECT id, employee_number, full_name, email, department_name, position_title, avatar
            FROM v_employees WHERE full_name LIKE ? OR employee_number LIKE ? LIMIT 10
        ");
        $s = "%$q%";
        $stmt->execute([$s, $s]);
        return $stmt->fetchAll();
    }

    private function decryptRecord(?array $data): ?array {
        if (!$data) return null;
        $piiFields = ['basic_salary', 'sss_number', 'philhealth_number', 'pagibig_number', 'tin_number'];
        foreach ($piiFields as $f) {
            if (isset($data[$f])) {
                $data[$f] = decrypt_pii($data[$f]);
            }
        }
        return $data;
    }
}
