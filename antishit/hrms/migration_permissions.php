<?php
/**
 * Migration Script: Update Permissions Table
 * Run this via command line: php migration.php
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$db = db();
$db->exec("SET FOREIGN_KEY_CHECKS = 0;");
$db->exec("TRUNCATE TABLE role_permissions;");
$db->exec("TRUNCATE TABLE permissions;");
$db->exec("SET FOREIGN_KEY_CHECKS = 1;");

$permissions = [
    // ATTENDANCE
    ['attendance', 'approve', 'Approve Attendance'],
    ['attendance', 'manage', 'Manage Attendance'],
    ['attendance', 'self', 'Self Attendance'],
    // AUDIT
    ['audit', 'view', 'View Audit Logs'],
    // DASHBOARD
    ['dashboard', 'view', 'View Dashboard'],
    // DOCUMENTS
    ['documents', 'manage', 'Manage Documents'],
    ['documents', 'self', 'Self Documents'],
    // EMPLOYEES
    ['employees', 'view_dept', 'Department Employees'],
    ['employees', 'manage', 'Manage Employees'],
    ['employees', 'view', 'View Employees'],
    // LEAVES
    ['leaves', 'approve', 'Approve Leave'],
    ['leaves', 'manage', 'Manage Leave'],
    ['leaves', 'self', 'Self Leave'],
    // NOTIFICATIONS
    ['notifications', 'manage', 'Manage Notifications'],
    ['notifications', 'self', 'Self Notifications'],
    // PAYROLL
    ['payroll', 'approve', 'Approve Payroll'],
    ['payroll', 'manage', 'Manage Payroll'],
    ['payroll', 'self', 'Self Payroll'],
    // PERFORMANCE
    ['performance', 'manage', 'Manage Performance'],
    ['performance', 'review', 'Review Performance'],
    // PROFILE
    ['profile', 'self', 'Self Profile'],
    // RECRUITMENT
    ['recruitment', 'manage_onboarding', 'Manage Onboarding'],
    ['recruitment', 'manage', 'Manage Recruitment'],
    // REPORTS
    ['reports', 'view_dept', 'Department Reports'],
    ['reports', 'view_finance', 'Finance Reports'],
    ['reports', 'view', 'View Reports'],
    // SETTINGS
    ['settings', 'manage_backups', 'Manage Backups'],
    ['settings', 'manage', 'Manage Settings'],
    ['settings', 'policy', 'Policy Settings'],
    ['settings', 'tax', 'Tax Settings'],
    // TRAINING
    ['training', 'manage', 'Manage Training'],
    // USERS
    ['users', 'manage_roles', 'Manage Roles'],
    ['users', 'manage_users', 'Manage Users'],
];

$stmt = $db->prepare("INSERT INTO permissions (module, action, description) VALUES (?, ?, ?)");
foreach ($permissions as $p) {
    $stmt->execute($p);
}

// Restore default mappings for Super Admin (all permissions)
$adminRoleId = $db->query("SELECT id FROM roles WHERE slug='super_admin'")->fetchColumn();
if ($adminRoleId) {
    $db->exec("INSERT INTO role_permissions (role_id, permission_id) SELECT $adminRoleId, id FROM permissions");
}

echo "Permissions table updated successfully.\n";
