<?php
require 'config/database.php';
$stmt = db()->query('SELECT name, code FROM leave_types');
while($row = $stmt->fetch()){
    echo $row['name'] . ' (' . $row['code'] . ')' . PHP_EOL;
}
