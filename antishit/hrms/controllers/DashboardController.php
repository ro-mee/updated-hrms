<?php
/** Dashboard Controller */
class DashboardController {
    public function index(): void {
        requireLogin();
        $role = currentRole();
        $empModel  = new Employee();
        $attModel  = new Attendance();
        $leaveModel= new Leave();
        $notifModel= new Notification();

        // Common stats
        $stats = [
            'total_employees' => $empModel->count(),
            'active_employees'=> $empModel->count(['status'=>'active']),
            'pending_leaves'  => $leaveModel->pendingCount(),
            'today_attendance'=> $attModel->todayStats(),
            'unread_notifs'   => $notifModel->unreadCount(currentUserId()),
        ];

        // Role-specific data
        $roleData = [];
        if (in_array($role, [ROLE_SUPER_ADMIN, ROLE_HR_DIRECTOR, ROLE_HR_SPECIALIST])) {
            $roleData['by_department'] = $empModel->countByDepartment();
            $roleData['by_status']     = $empModel->countByStatus();
            $roleData['recent_employees'] = $empModel->all([], 5, 0, 'date_hired DESC');
            $roleData['recent_leaves']    = $leaveModel->all(['status'=>'pending'], 5, 0);
        }
        if (in_array($role, [ROLE_FINANCE_MANAGER, ROLE_HR_DIRECTOR, ROLE_SUPER_ADMIN])) {
            $payrollModel = new Payroll();
            $roleData['payroll_summary'] = $payrollModel->summary();
        }
        if (in_array($role, [ROLE_RECRUITMENT_OFFICER, ROLE_HR_DIRECTOR, ROLE_SUPER_ADMIN])) {
            $jobModel = new Job();
            $appModel = new Applicant();
            $roleData['open_jobs']     = $jobModel->openCount();
            $roleData['total_applicants']= $appModel->totalCount();
            $roleData['applicants_by_status']= $appModel->countByStatus();
        }
        if ($role === ROLE_DEPT_MANAGER) {
            $empId = currentUser()['employee_id'] ?? 0;
            if ($empId) {
                $emp = $empModel->findById($empId);
                if ($emp) {
                    $deptId = $emp['department_id'];
                    $roleData['dept_name'] = $emp['department_name'];
                    $roleData['dept_employees_count'] = $empModel->count(['department_id' => $deptId]);
                    $roleData['dept_attendance'] = $attModel->todayStats($deptId);
                    $roleData['dept_pending_leaves'] = $leaveModel->count(['department_id' => $deptId, 'status' => 'pending']);
                    $roleData['recent_dept_leaves'] = $leaveModel->all(['department_id' => $deptId], 5, 0);
                }
            }
        }
        if ($role === ROLE_EMPLOYEE) {
            $empId = currentUser()['employee_id'];
            if ($empId) {
                $roleData['today_record']   = $attModel->todayRecord($empId);
                $roleData['my_leaves']       = $leaveModel->all(['employee_id'=>$empId], 5, 0);
                $roleData['leave_balance']   = $leaveModel->getBalance($empId, date('Y'));
                $payrollModel = new Payroll();
                $roleData['my_payslips'] = $payrollModel->employeePayslips($empId);
            }
        }

        $notifications = $notifModel->forUser(currentUserId(), false, 5);
        include APP_ROOT . '/views/dashboard/index.php';
    }
}

/** Profile Controller */
class ProfileController {
    public function index(): void {
        requireLogin();
        $userModel    = new User();
        $empModel     = new Employee();
        $user         = $userModel->findById(currentUserId());
        $employee     = currentUser()['employee_id'] ? $empModel->findById(currentUser()['employee_id']) : null;
        $total        = $userModel->getLoginHistoryCount(currentUserId());
        $pg           = paginate($total, (int)get('page', 1), 10);
        $loginHistory = $userModel->getLoginHistory(currentUserId(), $pg['per_page'], $pg['offset']);
        $lastLogin    = $userModel->getLastLogin(currentUserId());

        // Load active sessions
        $sessionModel = new UserSession();
        $activeSessions = $sessionModel->getByUserId(currentUserId());

        include APP_ROOT . '/views/employees/profile.php';
    }

    public function update(): void {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('index.php?module=profile&action=index'); }
        validateCsrf();
        $userModel = new User();
        $data = [
            'first_name' => sanitizeInput(post('first_name')),
            'last_name'  => sanitizeInput(post('last_name')),
            'email'      => sanitizeInput(post('email')),
        ];
        $errors = validateRequired(['first_name','last_name','email'], $data);
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email.';

        // Handle avatar upload
        if (!empty($_FILES['avatar']['name'])) {
            $up = uploadFile($_FILES['avatar'], UPLOAD_AVATAR_PATH, ALLOWED_IMAGE_TYPES);
            if ($up['success']) {
                $userModel->updateAvatar(currentUserId(), $up['filename']);
                $_SESSION[SESSION_USER]['avatar'] = $up['filename'];
            } else {
                $errors['avatar'] = $up['message'];
            }
        }

        if (empty($errors)) {
            $userModel->update(currentUserId(), $data);
            $_SESSION[SESSION_USER]['first_name'] = $data['first_name'];
            $_SESSION[SESSION_USER]['last_name']  = $data['last_name'];
            $_SESSION[SESSION_USER]['full_name']  = $data['first_name'] . ' ' . $data['last_name'];
            auditLog('update_profile', 'profile', 'User updated their profile');
            setFlash('success', 'Profile updated successfully.');
        } else {
            setFlash('error', implode(' ', $errors));
        }
        redirect('index.php?module=profile&action=index');
    }

    public function revokeSession(): void {
        requireLogin();
        validateCsrf();
        $sessionId = (int)post('session_db_id');
        
        $sessionModel = new UserSession();
        if ($sessionModel->revoke($sessionId, currentUserId())) {
            auditLog('revoke_session', 'profile', "User revoked their own session ID #$sessionId");
            jsonResponse(true, 'Session revoked successfully.');
        }
        jsonResponse(false, 'Failed to revoke session.');
    }
}
