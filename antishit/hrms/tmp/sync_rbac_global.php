<?php
require_once dirname(__DIR__) . '/config/database.php';
$db = db();

$matrix = [
    'hr_director' => [
        'employees.*', 'attendance.*', 'leaves.*', 'payroll.*', 'recruitment.*', 
        'performance.*', 'training.*', 'documents.*', 'reports.*', 'audit.*', 'settings.*'
    ],
    'department_manager' => [
        'employees.view', 'attendance.manage', 'leaves.manage', 'performance.review', 
        'training.manage', 'documents.self', 'attendance.self', 'leaves.self', 'performance.self'
    ],
    'finance_manager' => [
        'payroll.*', 'employees.view', 'attendance.view', 'leaves.view', 'training.view', 
        'documents.self', 'attendance.self', 'leaves.self', 'payroll.self'
    ],
    'hr_specialist' => [
        'employees.manage', 'attendance.manage', 'leaves.manage', 'recruitment.view', 
        'performance.review', 'training.manage', 'documents.manage', 'attendance.self', 
        'leaves.self', 'performance.self'
    ],
    'recruitment_officer' => [
        'recruitment.manage', 'employees.view', 'training.view', 'documents.manage', 
        'attendance.self', 'leaves.self', 'performance.self', 'documents.self'
    ],
    'employee' => [
        'attendance.self', 'leaves.self', 'payroll.self', 'performance.self', 'documents.self'
    ]
];

try {
    $db->beginTransaction();

    foreach ($matrix as $roleSlug => $perms) {
        // Get Role ID
        $stmt = $db->prepare("SELECT id FROM roles WHERE slug=?");
        $stmt->execute([$roleSlug]);
        $roleId = $stmt->fetchColumn();
        if (!$roleId) continue;

        // Clear existing permissions for this role (except for Super Admin who has everything usually)
        $db->prepare("DELETE FROM role_permissions WHERE role_id=?")->execute([$roleId]);

        foreach ($perms as $pString) {
            if (strpos($pString, '.*') !== false) {
                // Grant all actions for that module
                $module = str_replace('.*', '', $pString);
                $stmt = $db->prepare("SELECT id FROM permissions WHERE module=?");
                $stmt->execute([$module]);
                $allPerms = $stmt->fetchAll(PDO::FETCH_COLUMN);
                foreach ($allPerms as $pId) {
                    $db->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?,?)")->execute([$roleId, $pId]);
                }
            } else {
                // Grant specific action
                list($module, $action) = explode('.', $pString);
                $stmt = $db->prepare("SELECT id FROM permissions WHERE module=? AND action=?");
                $stmt->execute([$module, $action]);
                $pId = $stmt->fetchColumn();
                if ($pId) {
                    $db->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?,?)")->execute([$roleId, $pId]);
                }
            }
        }
        echo "Synced permissions for $roleSlug\n";
    }

    $db->commit();
    echo "RBAC Global Sync Complete.\n";
} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
