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
            
            // Performance Distribution
            $roleData['perf_dist'] = db()->query("
                SELECT ROUND(overall_rating) as rating, COUNT(*) as cnt
                FROM performance_reviews
                WHERE status = 'approved' AND overall_rating IS NOT NULL
                GROUP BY rating
                ORDER BY rating DESC
            ")->fetchAll();

            // Leave Type Distribution
            $roleData['leave_type_dist'] = db()->query("
                SELECT lt.name, COUNT(l.id) as cnt
                FROM leaves l
                JOIN leave_types lt ON l.leave_type_id = lt.id
                WHERE l.status = 'approved'
                GROUP BY lt.id
            ")->fetchAll();
        }
        if (in_array($role, [ROLE_RECRUITMENT_OFFICER, ROLE_HR_DIRECTOR, ROLE_SUPER_ADMIN])) {
            $jobModel = new Job();
            $appModel = new Applicant();
            $roleData['open_jobs']     = $jobModel->openCount();
            $roleData['total_applicants']= $appModel->totalCount();
            $roleData['applicants_by_status']= $appModel->countByStatus();
            $roleData['hired_this_month'] = $appModel->hiredThisMonth();
            $roleData['upcoming_interviews'] = $appModel->upcomingInterviews(5);
            $roleData['recent_applicants'] = $appModel->all([], 5, 0);
            $roleData['job_status_dist'] = $jobModel->countByStatus();
            $roleData['applicant_source_dist'] = $appModel->countBySource();
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

                    // --- New Manager Analytics ---
                    // 1. Attendance Trend (Last 7 Days)
                    $roleData['dept_attendance_trend'] = db()->query("
                        SELECT a.date, 
                               SUM(CASE WHEN a.status IN ('present', 'late', 'half_day') THEN 1 ELSE 0 END) as present,
                               SUM(CASE WHEN a.status='absent' THEN 1 ELSE 0 END) as absent
                        FROM attendance a JOIN employees e ON a.employee_id = e.id
                        WHERE e.department_id = $deptId AND a.date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                        GROUP BY a.date ORDER BY a.date ASC
                    ")->fetchAll();

                    // 2. Performance Distribution (Dept)
                    $roleData['dept_perf_dist'] = db()->query("
                        SELECT ROUND(overall_rating) as rating, COUNT(*) as cnt
                        FROM performance_reviews pr JOIN employees e ON pr.employee_id = e.id
                        WHERE e.department_id = $deptId AND pr.status = 'approved' AND overall_rating IS NOT NULL
                        GROUP BY rating ORDER BY rating DESC
                    ")->fetchAll();

                    // 3. Upcoming Trainings for Dept
                    $trainModel = new Training();
                    $roleData['dept_trainings'] = $trainModel->all(['department_id' => $deptId, 'status' => 'scheduled'], 5, 0);

                    // 4. Attendance Rate (Today)
                    $todayStats = $roleData['dept_attendance'];
                    $totalEmp = $roleData['dept_employees_count'];
                    $presentCount = ($todayStats['present']??0) + ($todayStats['late']??0);
                    $roleData['dept_attendance_rate'] = ($totalEmp > 0) ? round(($presentCount / $totalEmp) * 100) : 0;
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
                
                // Attendance Analytics (Monthly)
                $monthStats = $attModel->monthlySummary($empId, (int)date('Y'), (int)date('n'));
                $roleData['attendance_summary'] = $monthStats;
                
                // Attendance Rate %
                $presentCount = 0;
                foreach ($monthStats as $ms) {
                    if (in_array($ms['status'], ['present', 'late', 'half_day'])) $presentCount += $ms['cnt'];
                }
                $workDaysSoFar = workingDaysBetween(date('Y-m-01'), date('Y-m-d'));
                $roleData['attendance_rate'] = ($workDaysSoFar > 0) ? round(($presentCount / $workDaysSoFar) * 100) : 100;

                // Training Progress
                $trainModel = new Training();
                $myTrainings = $trainModel->employeeTrainings($empId);
                $roleData['training_stats'] = [
                    'total'     => count($myTrainings),
                    'completed' => count(array_filter($myTrainings, fn($t) => $t['enroll_status'] === 'completed')),
                    'enrolled'  => count(array_filter($myTrainings, fn($t) => $t['enroll_status'] !== 'completed'))
                ];

                // Latest Performance
                $perfModel = new Performance();
                $latestPerf = $perfModel->all(['employee_id' => $empId, 'status' => 'approved'], 1, 0);
                $roleData['latest_performance'] = $latestPerf[0] ?? null;

                // Next Payday
                $roleData['next_payday'] = db()->query("SELECT pay_date FROM payroll_periods WHERE pay_date >= CURDATE() AND status != 'cancelled' ORDER BY pay_date ASC LIMIT 1")->fetchColumn();
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
