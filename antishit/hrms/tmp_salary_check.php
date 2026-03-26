<?php
define('APP_ROOT', __DIR__);
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

foreach (glob(__DIR__ . '/models/*.php') as $model) {
    require_once $model;
}

$activeEmployees = (new Employee())->all(['status'=>'active'], 5000);
$totalSalary = 0; $salaryCount = 0;
echo "Active Count: " . count($activeEmployees) . "\n";
foreach ($activeEmployees as $emp) {
    $sal = isset($emp['basic_salary']) ? (float)$emp['basic_salary'] : 0;
    echo "Employee {$emp['id']} - raw: " . ($emp['basic_salary'] ?? 'NULL') . " - parsed: {$sal}\n";
    if ($sal > 0) {
        $totalSalary += $sal;
        $salaryCount++;
    }
}
$avg = $salaryCount > 0 ? ($totalSalary / $salaryCount) : 0;
echo "Computed Avg: $avg\n";
