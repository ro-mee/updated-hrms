<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=hrms_db', 'root', '');
    $stmt = $db->query("DESCRIBE applicants");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($columns, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
