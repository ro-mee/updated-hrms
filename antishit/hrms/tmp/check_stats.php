<?php
require 'c:/xampp/htdocs/updated-hrms/antishit/hrms/config/database.php';
$db = db();

// 1. Open Jobs
$openJobs = $db->query("SELECT COUNT(*) FROM jobs WHERE status='open'")->fetchColumn();

// 2. New Hires this month
$newHires = $db->query("SELECT COUNT(*) FROM employees WHERE MONTH(date_hired) = MONTH(CURDATE()) AND YEAR(date_hired) = YEAR(CURDATE())")->fetchColumn();

// 3. Birthdays today
$birthdays = $db->query("SELECT COUNT(*) FROM employees WHERE MONTH(birth_date) = MONTH(CURDATE()) AND DAY(birth_date) = DAY(CURDATE())")->fetchColumn();

// 4. Pending Reviews
$pendingReviews = $db->query("SELECT COUNT(*) FROM performance_reviews WHERE status='draft'")->fetchColumn();

echo "Open Jobs: $openJobs\n";
echo "New Hires (Month): $newHires\n";
echo "Birthdays Today: $birthdays\n";
echo "Pending Reviews: $pendingReviews\n";
