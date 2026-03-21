<?php
require 'c:/xampp/htdocs/updated-hrms/antishit/hrms/config/database.php';
$db = db();

// 1. Next Pay Date
$nextPay = $db->query("SELECT MIN(pay_date) FROM payroll_periods WHERE pay_date >= CURDATE() AND status != 'paid'")->fetchColumn();

// 2. Anniversaries this month
$anniversaries = $db->query("SELECT COUNT(*) FROM employees WHERE MONTH(date_hired) = MONTH(CURDATE()) AND YEAR(date_hired) < YEAR(CURDATE())")->fetchColumn();

// 3. Hired this week
$hiredThisWeek = $db->query("SELECT COUNT(*) FROM employees WHERE date_hired >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetchColumn();

// 4. Active Trainings
$activeTrainings = $db->query("SELECT COUNT(*) FROM trainings WHERE status='scheduled' OR status='ongoing'")->fetchColumn();

// 5. Avg Performance
$avgPerformance = $db->query("SELECT AVG(overall_rating) FROM performance_reviews WHERE status='completed'")->fetchColumn();

echo "Next Pay Date: " . ($nextPay ?: "None") . "\n";
echo "Anniversaries (Month): $anniversaries\n";
echo "Hired (Week): $hiredThisWeek\n";
echo "Active Trainings: $activeTrainings\n";
echo "Avg Performance: " . round($avgPerformance, 2) . "\n";
