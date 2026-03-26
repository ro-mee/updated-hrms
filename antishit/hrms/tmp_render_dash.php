<?php
define('APP_ROOT', __DIR__);
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';

$_SESSION[SESSION_USER] = ['id'=>1, 'role_id'=>ROLE_SUPER_ADMIN, 'first_name'=>'Test'];

ob_start();
require_once __DIR__ . '/controllers/DashboardController.php';
$c = new DashboardController();
$c->index();
$html = ob_get_clean();

// Extract the script tag at the bottom
preg_match('/<script src="https:\/\/cdn\.jsdelivr\.net\/npm\/apexcharts"><\/script>\s*<script>(.*?)<\/script>/s', $html, $matches);
if (!empty($matches[1])) {
    echo "JS SCRIPT FOUND:\n";
    echo $matches[1];
} else {
    echo "COULD NOT FIND APEXCHARTS SCRIPT TAG!\n";
    // echo substr($html, -2000); // output bottom of html
}
