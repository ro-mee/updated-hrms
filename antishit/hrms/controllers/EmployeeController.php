<?php
/** Employee Controller */
class EmployeeController {
    private Employee $model;
    private User $userModel;

    public function __construct() {
        $this->model     = new Employee();
        $this->userModel = new User();
    }

    public function index(): void {
        requireLogin();
        requirePermission('employees', 'view');
        $filters = [
            'search'        => sanitizeInput(get('search')),
            'department_id' => (int)get('department_id'),
            'status'        => sanitizeInput(get('status')),
        ];
        // Dept manager sees only their dept
        if (currentRole() === ROLE_DEPT_MANAGER && !hasRole(ROLE_SUPER_ADMIN, ROLE_HR_DIRECTOR)) {
            $me = $this->model->findById(currentUser()['employee_id']);
            if ($me) $filters['department_id'] = $me['department_id'];
        }
        $total = $this->model->count($filters);
        $pg    = paginate($total, (int)get('page', 1));
        $employees   = $this->model->all($filters, $pg['per_page'], $pg['offset']);
        $departments = $this->model->departments();
        include APP_ROOT . '/views/employees/index.php';
    }

    public function view(): void {
        requireLogin();
        $id = (int)get('id');
        $employee = $this->model->findById($id);
        if (!$employee) { setFlash('error','Employee not found.'); redirect('index.php?module=employees'); }
        // Employee can only view own profile
        if (currentRole() === ROLE_EMPLOYEE && $employee['user_id'] !== currentUserId()) {
            http_response_code(403); include APP_ROOT.'/views/errors/403.php'; exit;
        }
        // Dept manager can only view staff in their department
        if (currentRole() === ROLE_DEPT_MANAGER && !hasRole(ROLE_SUPER_ADMIN, ROLE_HR_DIRECTOR)) {
            $me = $this->model->findById(currentUser()['employee_id']);
            if ($me && $employee['department_id'] !== $me['department_id'] && $employee['id'] !== $me['id']) {
                http_response_code(403); include APP_ROOT.'/views/errors/403.php'; exit;
            }
        }
        $leaveModel  = new Leave();
        $attModel    = new Attendance();
        $docModel    = new Document();
        $leaves      = $leaveModel->all(['employee_id'=>$id], 5, 0);
        $leaveBalance= $leaveModel->getBalance($id, date('Y'));
        $documents   = $docModel->all(['admin_view'=>true,'employee_id2'=>$id], 10, 0);
        $attSummary  = $attModel->monthlySummary($id, date('Y'), date('n'));
        
        // Load active sessions
        $sessionModel = new UserSession();
        $activeSessions = $sessionModel->getByUserId($employee['user_id']);
        
        include APP_ROOT . '/views/employees/view.php';
    }

    public function add(): void {
        requirePermission('employees', 'manage');
        $errors = [];
        $fromApplicantId = (int)get('from_applicant');
        $applicantData = [];
        if ($fromApplicantId) {
            $applicantData = (new Applicant())->findById($fromApplicantId);
        }

        // Auto-find employee role
        $roleId = 0;
        $roles = $this->userModel->roles();
        foreach($roles as $r) {
            if ($r['slug'] === 'employee') { $roleId = $r['id']; break; }
        }

        $deptId = (int)get('department_id', post('department_id', $applicantData['department_id'] ?? 0));
        $hiredDate = get('date_hired', post('date_hired', $applicantData['date_hired'] ?? date('Y-m-d')));
        
        $departments = $this->model->departments();
        $positions   = $this->model->positions($deptId);
        $roles       = $this->userModel->roles();
        $managers    = $this->model->managers();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $deptIdPost = (int)post('department_id');
            $deptData   = $this->model->getDepartment($deptIdPost);
            
            $data = [
                'first_name'     => sanitizeInput(post('first_name')),
                'last_name'      => sanitizeInput(post('last_name')),
                'email'          => sanitizeInput(post('email')),
                'password'       => post('password') ?: generateRandomPassword(),
                'role_id'        => (int)post('role_id'),
                'department_id'  => $deptIdPost,
                'position_id'    => (int)post('position_id'),
                'manager_id'     => $deptData['manager_id'] ?? null,
                'employment_type'=> sanitizeInput(post('employment_type', 'full_time')),
                'date_hired'     => sanitizeInput(post('date_hired')),
                'basic_salary'   => (float)post('basic_salary'),
                'phone'          => sanitizeInput(post('phone')),
                'birth_date'     => sanitizeInput(post('birth_date')),
                'gender'         => sanitizeInput(post('gender')),
                'civil_status'   => sanitizeInput(post('civil_status')),
                'address'        => sanitizeInput(post('address')),
                'city'           => sanitizeInput(post('city')),
                'sss_number'     => sanitizeInput(post('sss_number')),
                'philhealth_number'=> sanitizeInput(post('philhealth_number')),
                'pagibig_number' => sanitizeInput(post('pagibig_number')),
                'tin_number'     => sanitizeInput(post('tin_number')),
                'emergency_contact_name' => sanitizeInput(post('emergency_contact_name')),
                'emergency_contact_phone'=> sanitizeInput(post('emergency_contact_phone')),
            ];
            $reqFields = ['first_name','last_name','email','department_id','position_id','date_hired'];
            $errors = validateRequired($reqFields, $data);
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email.';
            // Only validate password length if it was manually entered
            if (!empty(post('password')) && strlen(post('password')) < 8) {
                $errors['password'] = 'Password must be at least 8 characters.';
            }

            if (empty($errors)) {
                try {
                    db()->beginTransaction();
                    $userId = $this->userModel->create($data);
                    $data['user_id']         = $userId;
                    $data['employee_number'] = $this->model->generateEmployeeNumber();
                    $empId = $this->model->create($data);
                    // Seed leave balances for current year
                    $leaveTypes = (new Leave())->leaveTypes();
                    foreach ($leaveTypes as $lt) {
                        db()->prepare("INSERT IGNORE INTO leave_balances (employee_id,leave_type_id,year,allocated,used,remaining) VALUES (?,?,YEAR(CURDATE()),?,0,?)")
                            ->execute([$empId,$lt['id'],$lt['days_allowed'],$lt['days_allowed']]);
                    }
                    db()->commit();

                    // Send Welcome Email if requested
                    if (post('send_welcome_email') === '1') {
                        $companyName = (new Setting())->get('company_name', APP_NAME);
                        $subject = "Welcome to $companyName - Your Account Credentials";
                        $loginUrl = APP_URL . "/index.php?module=auth&action=login";
                        $body = "
                            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;'>
                                <div style='background: #0d6efd; color: white; padding: 20px; text-align: center;'>
                                    <h2 style='margin: 0;'>Welcome to the Team!</h2>
                                </div>
                                <div style='padding: 20px; line-height: 1.6; color: #333;'>
                                    <p>Hello <strong>{$data['first_name']}</strong>,</p>
                                    <p>Your employee account at <strong>$companyName</strong> has been successfully created. You can now log in to the HRMS portal using the credentials below:</p>
                                    <div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #0d6efd; margin: 20px 0;'>
                                        <p style='margin: 5px 0;'><strong>Login Link:</strong> <a href='$loginUrl' style='color: #0d6efd;'>$loginUrl</a></p>
                                        <p style='margin: 5px 0;'><strong>Username/Email:</strong> {$data['email']}</p>
                                        <p style='margin: 5px 0;'><strong>Temporary Password:</strong> <span style='background: #fff; padding: 2px 5px; border: 1px dashed #ccc;'>{$data['password']}</span></p>
                                    </div>
                                    <p style='font-size: 0.9em; color: #666;'>For security reasons, please change your password immediately after your first login.</p>
                                    <p>Welcome aboard! We are excited to have you with us.</p>
                                    <br>
                                    <p style='margin-bottom: 0;'>Best Regards,</p>
                                    <p style='margin-top: 0;'><strong>HR Department</strong><br>$companyName</p>
                                </div>
                                <div style='background: #f1f1f1; color: #777; padding: 10px; text-align: center; font-size: 0.8em;'>
                                    This is an automated message. Please do not reply directly to this email.
                                </div>
                            </div>
                        ";
                        sendMail($data['email'], $subject, $body);
                    }

                    // Update applicant status if coming from recruitment
                    if ($fromApplicantId) {
                        $appModel = new Applicant();
                        $appModel->updateStatus($fromApplicantId, 'hired');
                        $app = $appModel->findById($fromApplicantId);
                        if ($app) {
                            (new Job())->checkAndAutoClose($app['job_id']);
                        }
                    }

                    auditLog('create', 'employees', "Added employee {$data['first_name']} {$data['last_name']}", $empId);
                    setFlash('success', 'Employee added successfully!' . (post('send_welcome_email') === '1' ? ' Credentials sent to email.' : ''));
                    redirect('index.php?module=employees&action=view&id=' . $empId);
                } catch (Exception $e) {
                    db()->rollBack();
                    $errors['general'] = 'Error creating employee: ' . $e->getMessage();
                }
            }
        }
        include APP_ROOT . '/views/employees/add.php';
    }

    public function edit(): void {
        requirePermission('employees', 'manage');
        $id = (int)get('id');
        $employee = $this->model->findById($id);
        if (!$employee) { setFlash('error','Employee not found.'); redirect('index.php?module=employees'); }
        $errors = [];
        $departments = $this->model->departments();
        $positions   = $this->model->positions();
        $roles       = $this->userModel->roles();
        $managers    = $this->model->managers();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $deptIdPost = (int)post('department_id');
            $deptData   = $this->model->getDepartment($deptIdPost);

            $empData = [
                'department_id'  => $deptIdPost,
                'position_id'    => (int)post('position_id'),
                'manager_id'     => $deptData['manager_id'] ?? null,
                'employment_type'=> sanitizeInput(post('employment_type')),
                'status'         => sanitizeInput(post('status')),
                'date_hired'     => sanitizeInput(post('date_hired')),
                'basic_salary'   => (float)post('basic_salary'),
                'phone'          => sanitizeInput(post('phone')),
                'birth_date'     => sanitizeInput(post('birth_date')),
                'gender'         => sanitizeInput(post('gender')),
                'civil_status'   => sanitizeInput(post('civil_status')),
                'address'        => sanitizeInput(post('address')),
                'city'           => sanitizeInput(post('city')),
                'sss_number'     => sanitizeInput(post('sss_number')),
                'philhealth_number' => sanitizeInput(post('philhealth_number')),
                'pagibig_number' => sanitizeInput(post('pagibig_number')),
                'tin_number'     => sanitizeInput(post('tin_number')),
                'emergency_contact_name'  => sanitizeInput(post('emergency_contact_name')),
                'emergency_contact_phone' => sanitizeInput(post('emergency_contact_phone')),
                'notes'          => sanitizeInput(post('notes')),
            ];
            $userData = [
                'first_name' => sanitizeInput(post('first_name')),
                'last_name'  => sanitizeInput(post('last_name')),
                'email'      => sanitizeInput(post('email')),
                'role_id'    => (int)post('role_id'),
            ];
            $errors = validateRequired(['first_name','last_name','email'], $userData);

            if (empty($errors)) {
                $this->model->update($id, $empData);
                $this->userModel->update($employee['user_id'], $userData);
                auditLog('update', 'employees', "Updated employee #{$id}", $id);
                setFlash('success', 'Employee updated successfully!');
                redirect('index.php?module=employees&action=view&id=' . $id);
            }
            $employee = array_merge($employee, $empData, $userData);
        }
        include APP_ROOT . '/views/employees/edit.php';
    }

    public function delete(): void {
        requirePermission('employees', 'manage');
        validateCsrf();
        $id = (int)post('id');
        if ($id) {
            $this->model->softDelete($id);
            auditLog('delete', 'employees', "Soft-deleted employee #{$id}", $id);
            setFlash('success', 'Employee record deactivated.');
        }
        redirect('index.php?module=employees');
    }

    public function search(): void {
        requireLogin();
        header('Content-Type: application/json');
        $q = sanitizeInput(get('q'));
        echo json_encode($this->model->search($q));
        exit;
    }

    public function positions(): void {
        requireLogin();
        $deptId = (int)get('department_id');
        $positions = $this->model->positions($deptId);
        $dept = $this->model->getDepartment($deptId);
        
        header('Content-Type: application/json');
        echo json_encode([
            'positions' => $positions,
            'manager_id' => $dept['manager_id'] ?? null
        ]);
        exit;
    }

    public function revokeSession(): void {
        requireLogin();
        validateCsrf();
        $sessionId = (int)post('session_db_id');
        $employeeId = (int)post('employee_id');
        
        $employee = $this->model->findById($employeeId);
        if (!$employee) jsonResponse(false, 'Employee not found.');

        // Verify ownership/permission
        if (currentRole() === ROLE_EMPLOYEE && $employee['user_id'] !== currentUserId()) {
            jsonResponse(false, 'Unauthorized.');
        }

        $sessionModel = new UserSession();
        if ($sessionModel->revoke($sessionId, $employee['user_id'])) {
            auditLog('revoke_session', 'auth', "Revoked session ID #$sessionId for user #{$employee['user_id']}", $employeeId);
            jsonResponse(true, 'Session revoked successfully.');
        }
        jsonResponse(false, 'Failed to revoke session.');
    }
}
