<?php
require_once dirname(__DIR__) . '/config/database.php';
$db = db();
$res = $db->query("SELECT * FROM v_employees LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if ($res) {
    print_r(array_keys($res));
} else {
    echo "No employees found in v_employees.\n";
}
