<?php
require_once dirname(__DIR__) . '/config/database.php';
$db = db();
$res = $db->query("SELECT l.*, e.id AS emp_id, e.employee_number, e.department_id, e.user_id 
                 FROM leaves l 
                 LEFT JOIN employees e ON l.employee_id = e.id 
                 WHERE l.status='pending'")->fetchAll(PDO::FETCH_ASSOC);

$filters = []; // Simulating 'All' view
$where = ['1=1']; $params = [];
if (!empty($filters['status'])) { $where[] = "l.status=?"; $params[] = $filters['status']; }
$whereStr = implode(' AND ', $where);

$sql = "SELECT l.*, CONCAT(u.first_name,' ',u.last_name) AS full_name,
               u.email, u.avatar, e.employee_number, d.name AS department_name,
               lt.name AS leave_type_name, lt.is_paid,
               CONCAT(ru.first_name,' ',ru.last_name) AS reviewed_by_name
        FROM leaves l
        JOIN employees e ON l.employee_id=e.id
        JOIN users u ON e.user_id=u.id
        JOIN departments d ON e.department_id=d.id
        JOIN leave_types lt ON l.leave_type_id=lt.id
        LEFT JOIN users ru ON l.reviewed_by=ru.id
        WHERE $whereStr ORDER BY l.created_at DESC LIMIT 10 OFFSET 0";

echo "Executing SQL: $sql\n";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Results count: " . count($results) . "\n";
print_r($results);
