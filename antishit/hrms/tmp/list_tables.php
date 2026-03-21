<?php
require 'c:/xampp/htdocs/updated-hrms/antishit/hrms/config/database.php';
$db = db();
$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);
