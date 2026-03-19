<?php
/**
 * Role & Permission Controller
 */
class RoleController {
    private Role $roleModel;
    private Permission $permModel;

    public function __construct() {
        $this->roleModel = new Role();
        $this->permModel = new Permission();
    }

    public function index(): void {
        requireRole(ROLE_SUPER_ADMIN);
        
        // Bootstrap permissions if table is empty or missing granular ones
        $this->bootstrapPermissions();

        $roles = $this->roleModel->all();
        $selectedRoleId = (int)get('role_id', $roles[0]['id'] ?? 0);
        
        $selectedRole = null;
        if ($selectedRoleId) {
            $selectedRole = $this->roleModel->findById($selectedRoleId);
        }

        $allPermissions = $this->permModel->allGroupedByModule();
        $rolePermissions = $selectedRoleId ? $this->roleModel->permissions($selectedRoleId) : [];

        include APP_ROOT . '/views/roles/matrix.php';
    }

    public function update(): void {
        requireRole(ROLE_SUPER_ADMIN);
        validateCsrf();

        $roleId = (int)post('role_id');
        $permissionIds = post('permissions', []);

        if (!$roleId) {
            setFlash('error', 'Invalid role.');
            redirect('index.php?module=roles');
        }

        try {
            $this->roleModel->syncPermissions($roleId, $permissionIds);
            
            // Immediately refresh current user's permissions if their role was updated
            // This ensures the admin sees changes immediately if testing their own role
            if ($roleId == currentUser()['role_id']) {
                $_SESSION[SESSION_USER]['permissions'] = loadUserPermissions(currentUserId(), currentRole());
            }
            
            auditLog('update_permissions', 'roles', "Updated permissions for role ID: $roleId");
            setFlash('success', 'Permissions updated successfully.');
        } catch (Exception $e) {
            setFlash('error', 'Failed to update permissions: ' . $e->getMessage());
        }

        redirect('index.php?module=roles&role_id=' . $roleId);
    }

    private function bootstrapPermissions(): void {
        $db = db();
        
        $permissions = [
            ['attendance', 'approve', 'Approve Attendance'],
            ['attendance', 'manage', 'Manage Attendance'],
            ['attendance', 'self', 'My Attendance'],
            ['audit', 'view', 'View Audit Logs'],
            ['dashboard', 'view', 'View Dashboard'],
            ['documents', 'manage', 'Manage Documents'],
            ['documents', 'self', 'My Documents'],
            ['employees', 'view_dept', 'Department Employees'],
            ['employees', 'manage', 'Manage Employees'],
            ['employees', 'view', 'View Employees'],
            ['leaves', 'approve', 'Approve Leave'],
            ['leaves', 'manage', 'Manage Leave'],
            ['leaves', 'self', 'My Leaves'],
            ['notifications', 'manage', 'Manage Notifications'],
            ['notifications', 'self', 'My Notifications'],
            ['payroll', 'approve', 'Approve Payroll'],
            ['payroll', 'manage', 'Manage Payroll'],
            ['payroll', 'self', 'My Payslips'],
            ['performance', 'manage', 'Manage Performance'],
            ['performance', 'review', 'Review Performance'],
            ['profile', 'self', 'My Profile'],
            ['recruitment', 'manage_onboarding', 'Manage Onboarding'],
            ['recruitment', 'manage', 'Manage Recruitment'],
            ['reports', 'view_dept', 'Department Reports'],
            ['reports', 'view_finance', 'Finance Reports'],
            ['reports', 'view', 'View Reports'],
            ['settings', 'manage_backups', 'Manage Backups'],
            ['settings', 'manage', 'Manage Settings'],
            ['settings', 'policy', 'Policy Settings'],
            ['settings', 'tax', 'Tax Settings'],
            ['training', 'manage', 'Manage Training'],
            ['users', 'manage_roles', 'Manage Roles'],
            ['users', 'manage_users', 'Manage Users'],
        ];

        $stmt = $db->prepare("INSERT IGNORE INTO permissions (module, action, description) VALUES (?, ?, ?)");
        $updateStmt = $db->prepare("UPDATE permissions SET description = ? WHERE module = ? AND action = ?");
        
        foreach ($permissions as $p) {
            $stmt->execute($p);
            // Also update description in case it was different before
            $updateStmt->execute([$p[2], $p[0], $p[1]]);
        }
    }
}
