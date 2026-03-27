<?php
require 'config/database.php';
$stmt = db()->query('DESCRIBE attendance');
while($row = $stmt->fetch()){
    print_r($row);
}
