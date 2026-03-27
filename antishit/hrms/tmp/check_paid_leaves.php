<?php
require 'config/database.php';
$stmt = db()->query('SELECT name, is_paid FROM leave_types');
while($row = $stmt->fetch()){
    echo $row['name'] . ': ' . ($row['is_paid'] ? 'PAID' : 'UNPAID') . PHP_EOL;
}
