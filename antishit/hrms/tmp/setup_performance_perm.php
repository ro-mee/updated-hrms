<?php
require_once dirname(__DIR__) . '/config/database.php';
$db = db();

try {
    // 1. Ensure permission exists
    $stmt = $db->prepare("SELECT id FROM permissions WHERE module='performance' AND action='self'");
    $stmt->execute();
    $permId = $stmt->fetchColumn();

    if (!$permId) {
        $db->prepare("INSERT INTO permissions (module, action, description) VALUES ('performance', 'self', 'View own performance reviews')")->execute();
        $permId = $db->lastInsertId();
        echo "Created performance.self permission (ID $permId)\n";
    }

    // 2. Grant to employee role
    $stmt = $db->prepare("SELECT id FROM roles WHERE slug='employee'");
    $stmt->execute();
    $roleId = $stmt->fetchColumn();

    if ($roleId) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM role_permissions WHERE role_id=? AND permission_id=?");
        $stmt->execute([$roleId, $permId]);
        if ($stmt->fetchColumn() == 0) {
            $db->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)")->execute([$roleId, $permId]);
            echo "Granted performance.self to employee role.\n";
        }
    }
    
    // 3. Grant to other roles (Manager, HR, etc.)
    foreach(['department_manager', 'hr_specialist', 'recruitment_officer', 'finance_manager', 'hr_director'] as $slug) {
        $stmt = $db->prepare("SELECT id FROM roles WHERE slug=?");
        $stmt->execute([$slug]);
        $rId = $stmt->fetchColumn();
        if ($rId) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM role_permissions WHERE role_id=? AND permission_id=?");
            $stmt->execute([$rId, $permId]);
            if ($stmt->fetchColumn() == 0) {
                $db->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)")->execute([$rId, $permId]);
                echo "Granted performance.self to $slug role.\n";
            }
        }
    }

    echo "Permission setup complete.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
