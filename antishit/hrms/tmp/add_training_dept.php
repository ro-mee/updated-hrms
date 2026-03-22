<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = db();
    // Drop column if exists in case previous query partially succeeded
    try {
        $db->exec("ALTER TABLE trainings DROP FOREIGN KEY fk_trainings_dept");
    } catch(Exception $e) {}
    try {
        $db->exec("ALTER TABLE trainings DROP COLUMN department_id");
    } catch(Exception $e) {}
    
    $db->exec("ALTER TABLE trainings ADD COLUMN department_id INT UNSIGNED NULL AFTER max_participants");
    $db->exec("ALTER TABLE trainings ADD CONSTRAINT fk_trainings_dept FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL");
    echo "Columns added successfully\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
