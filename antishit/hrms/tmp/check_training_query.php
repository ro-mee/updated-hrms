<?php
require_once __DIR__ . '/../config/database.php';

$where = ['1=1']; 
$params = [];
$limit = 10;
$offset = 0;

$stmt = db()->prepare("
    SELECT t.*, CONCAT(u.first_name,' ',u.last_name) AS created_by_name,
           d.name AS department_name,
           COUNT(te.id) AS enrolled_count
    FROM trainings t LEFT JOIN users u ON t.created_by=u.id
    LEFT JOIN departments d ON t.department_id=d.id
    LEFT JOIN training_enrollments te ON te.training_id=t.id
    WHERE " . implode(' AND ', $where) . " GROUP BY t.id ORDER BY t.start_date DESC LIMIT ? OFFSET ?
");
$stmt->execute([...$params, $limit, $offset]);
$results = $stmt->fetchAll();
print_r($results);
