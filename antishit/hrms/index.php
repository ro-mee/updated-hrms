<?php
/**
 * Front Controller / Router
 * HRMS - Human Resource Management System
 *
 * URL pattern: index.php?module=MODULE&action=ACTION&id=X
 */

// ── Bootstrap ─────────────────────────────────────────────────
define('APP_ROOT', __DIR__);
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/mailer.php';

// ── Routing Map ───────────────────────────────────────────────
$routes = [
    'auth'        => 'AuthController',
    'dashboard'   => 'DashboardController',
    'employees'   => 'EmployeeController',
    'attendance'  => 'AttendanceController',
    'leaves'      => 'LeaveController',
    'payroll'     => 'PayrollController',
    'recruitment' => 'RecruitmentController',
    'performance' => 'PerformanceController',
    'training'    => 'TrainingController',
    'documents'   => 'DocumentController',
    'reports'     => 'ReportController',
    'notifications'=> 'NotificationController',
    'audit'       => 'AuditController',
    'settings'    => 'SettingsController',
    'profile'     => 'ProfileController',
    'roles'       => 'RoleController',
];

// ── Resolve Module & Action ────────────────────────────────────
$module = preg_replace('/[^a-z_]/', '', strtolower(get('module', 'auth')));
$action = preg_replace('/[^a-zA-Z0-9_]/', '', get('action', 'index'));

// Default redirect for logged-in root requests
if ($module === 'auth' && $action === 'index' && isLoggedIn()) {
    redirect('index.php?module=dashboard&action=index');
}

// ── Load Controller ────────────────────────────────────────────
if (!isset($routes[$module])) {
    http_response_code(404);
    include __DIR__ . '/views/errors/404.php';
    exit;
}

$controllerName = $routes[$module];
$controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';

// Load specific controller file if it exists
if (file_exists($controllerFile)) {
    // We will require it later
}

// Load all models first
foreach (glob(__DIR__ . '/models/*.php') as $model) {
    require_once $model;
}

// Always load shared controllers file (contains Attendance, Leave, Payroll, Recruitment,
// Performance, Training, Document, Notification, Audit, Settings, Report, Profile controllers)
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/OtherControllers.php';

// Load specific controller file (avoids reloading if already included above)
if (file_exists($controllerFile) && !in_array(realpath($controllerFile), get_included_files())) {
    require_once $controllerFile;
}

// Check if controller class exists
if (!class_exists($controllerName)) {
    http_response_code(500);
    include __DIR__ . '/views/errors/500.php';
    exit;
}

// ── Dispatch ──────────────────────────────────────────────────
$controller = new $controllerName();

// Check method exists
if (!method_exists($controller, $action)) {
    http_response_code(404);
    include __DIR__ . '/views/errors/404.php';
    exit;
}

// Call action
$controller->$action();
