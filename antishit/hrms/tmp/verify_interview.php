<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=hrms_db', 'root', '');
    $stmt = $db->query("SELECT status, interview_date, interview_location FROM applicants WHERE id=15");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($row, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
