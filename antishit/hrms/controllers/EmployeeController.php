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
        if (currentRole() === ROLE_DEPT_MANAGER) {
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
        $departments = $this->model->departments();
        $positions   = $this->model->positions();
        $roles       = $this->userModel->roles();
        $managers    = $this->model->managers();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $data = [
                'first_name'     => sanitizeInput(post('first_name')),
                'last_name'      => sanitizeInput(post('last_name')),
                'email'          => sanitizeInput(post('email')),
                'password'       => post('password'),
                'role_id'        => (int)post('role_id'),
                'department_id'  => (int)post('department_id'),
                'position_id'    => (int)post('position_id'),
                'manager_id'     => post('manager_id') ?: null,
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
            $reqFields = ['first_name','last_name','email','password','department_id','position_id','date_hired'];
            $errors = validateRequired($reqFields, $data);
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email.';
            if (strlen($data['password']) < 8) $errors['password'] = 'Password must be at least 8 characters.';

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
                    auditLog('create', 'employees', "Added employee {$data['first_name']} {$data['last_name']}", $empId);
                    setFlash('success', 'Employee added successfully!');
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
            $empData = [
                'department_id'  => (int)post('department_id'),
                'position_id'    => (int)post('position_id'),
                'manager_id'     => post('manager_id') ?: null,
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
        header('Content-Type: application/json');
        echo json_encode($this->model->positions($deptId));
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
