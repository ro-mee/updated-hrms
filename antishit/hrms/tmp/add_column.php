<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=hrms_db', 'root', '');
    $db->exec("ALTER TABLE applicants ADD COLUMN interview_location VARCHAR(255) DEFAULT NULL AFTER interview_date");
    echo "Column added successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
