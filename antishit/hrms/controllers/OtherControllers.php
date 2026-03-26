<?php
/** Attendance Controller */
class AttendanceController {
    private Attendance $model;
    public function __construct() { $this->model = new Attendance(); }

    public function index(): void {
        requireLogin();
        requirePermission('attendance', 'manage');
        
        // Auto-check yesterday's absences
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $this->model->markAbsent($yesterday);

        $filters = [
            'date_from'     => sanitizeInput(get('date_from', date('Y-m-01'))),
            'date_to'       => sanitizeInput(get('date_to',   date('Y-m-d'))),
            'department_id' => (int)get('department_id'),
            'status'        => sanitizeInput(get('status')),
        ];
        if (currentRole() === ROLE_DEPT_MANAGER) {
            $me = (new Employee())->findById(currentUser()['employee_id']);
            if ($me) $filters['department_id'] = $me['department_id'];
        }
        $total = $this->model->count($filters);
        $pg    = paginate($total, (int)get('page', 1));
        $records   = $this->model->list($filters, $pg['per_page'], $pg['offset']);
        $departments = (new Employee())->departments();
        include APP_ROOT . '/views/attendance/index.php';
    }

    public function my(): void {
        requirePermission('attendance', 'self');
        $empId    = currentUser()['employee_id'];
        if (!$empId) { setFlash('error','Employee profile not found.'); redirect('index.php?module=dashboard'); }
        $today    = $this->model->todayRecord($empId);
        $filters  = ['employee_id'=>$empId, 'date_from'=>date('Y-m-01'), 'date_to'=>date('Y-m-d')];
        $total    = $this->model->count($filters);
        $pg       = paginate($total, (int)get('page', 1), 10);
        $records  = $this->model->list($filters, $pg['per_page'], $pg['offset']);
        $summary  = $this->model->monthlySummary($empId, date('Y'), date('n'));
        include APP_ROOT . '/views/attendance/my.php';
    }

    public function clockIn(): void {
        requirePermission('attendance', 'self');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('index.php?module=attendance&action=my'); }
        validateCsrf();
        $empId = currentUser()['employee_id'];
        if (!$empId) { jsonResponse(false, 'Employee profile not found.'); }
        $existing = $this->model->todayRecord($empId);
        if ($existing && !empty($existing['clock_in'])) {
            jsonResponse(false, 'You have already clocked in today.');
        }
        $this->model->clockIn($empId);
        auditLog('clock_in','attendance','Employee clocked in',$empId);
        jsonResponse(true, 'Clocked in at ' . date('h:i A'));
    }

    public function clockOut(): void {
        requirePermission('attendance', 'self');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('index.php?module=attendance&action=my'); }
        validateCsrf();
        $empId = currentUser()['employee_id'];
        if (!$empId) { jsonResponse(false,'Employee profile not found.'); }
        $existing = $this->model->todayRecord($empId);
        if (!$existing || empty($existing['clock_in'])) {
            jsonResponse(false,'You have not clocked in yet.');
        }
        if (!empty($existing['clock_out'])) {
            jsonResponse(false,'You have already clocked out today.');
        }
        $this->model->clockOut($empId);
        auditLog('clock_out','attendance','Employee clocked out',$empId);
        jsonResponse(true,'Clocked out at ' . date('h:i A'));
    }

    public function edit(): void {
        requirePermission('attendance', 'manage');
        $employeeId = (int)get('employee_id');
        $date       = sanitizeInput(get('date', date('Y-m-d')));
        $existing   = [];
        // Pre-load
        $stmt = db()->prepare("SELECT * FROM attendance WHERE employee_id=? AND date=?");
        $stmt->execute([$employeeId, $date]);
        $existing = $stmt->fetch() ?: [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $data = [
                'clock_in'    => sanitizeInput(post('clock_in')) ?: null,
                'clock_out'   => sanitizeInput(post('clock_out')) ?: null,
                'status'      => sanitizeInput(post('status')),
                'remarks'     => sanitizeInput(post('remarks')),
            ];
            if ($data['clock_in'] && $data['clock_out']) {
                $in  = new DateTime($date . ' ' . $data['clock_in']);
                $out = new DateTime($date . ' ' . $data['clock_out']);
                $data['hours_worked']   = round(($out->getTimestamp()-$in->getTimestamp())/3600, 2);
                $data['overtime_hours'] = max(0, $data['hours_worked']-8);
            }
            $this->model->upsert($employeeId, $date, $data);
            auditLog('edit','attendance',"Edited attendance for emp #{$employeeId} on $date",$employeeId);
            setFlash('success','Attendance record updated.');
            redirect('index.php?module=attendance');
        }
        $employee = (new Employee())->findById($employeeId);
        include APP_ROOT . '/views/attendance/edit.php';
    }
    public function syncAbsences(): void {
        requirePermission('attendance', 'manage');
        validateCsrf();
        $year  = (int)date('Y');
        $month = (int)date('m');
        $count = $this->model->syncMonthAbsences($year, $month);
        auditLog('sync_absences', 'attendance', "Synced absences for $year-$month, marked $count records");
        setFlash('success', "Absence sync completed. $count new absence records created.");
        redirect('index.php?module=attendance');
    }
}

/** Leave Controller */
class LeaveController {
    private Leave $model;
    public function __construct() { $this->model = new Leave(); }

    public function index(): void {
        if (!can('leaves', 'manage')) {
            redirect('index.php?module=leaves&action=my');
        }
        $filters = ['status' => sanitizeInput(get('status'))];
        $me = (new Employee())->findById(currentUser()['employee_id']);
        if (currentRole() === ROLE_DEPT_MANAGER && $me && !hasRole(ROLE_SUPER_ADMIN, ROLE_HR_DIRECTOR)) {
            $filters['department_id'] = $me['department_id'];
        }
        $total  = $this->model->count($filters);
        $pg     = paginate($total, (int)get('page', 1));
        $leaves = $this->model->all($filters, $pg['per_page'], $pg['offset']);
        
        $leaveTypes = $this->model->leaveTypes();
        include APP_ROOT . '/views/leaves/index.php';
    }

    public function my(): void {
        requirePermission('leaves', 'self');
        $empId = currentUser()['employee_id'];
        if (!$empId) { setFlash('error','Employee profile not found.'); redirect('index.php?module=dashboard'); }
        $filters = ['employee_id' => $empId, 'status' => sanitizeInput(get('status'))];
        $total  = $this->model->count($filters);
        $pg     = paginate($total, (int)get('page', 1));
        $leaves = $this->model->all($filters, $pg['per_page'], $pg['offset']);
        $leaveTypes = $this->model->leaveTypes();
        include APP_ROOT . '/views/leaves/index.php'; // Uses same view but filtered
    }

    public function view(): void {
        requireLogin();
        $id = (int)get('id');
        $leave = $this->model->findById($id);
        if (!$leave) {
            setFlash('error', 'Leave request not found.');
            redirect('index.php?module=leaves');
        }
        
        // Security: employee can only view own leave unless they are a manager/HR
        if (currentRole() === ROLE_EMPLOYEE && $leave['employee_id'] !== currentUser()['employee_id']) {
            setFlash('error', 'Unauthorized access.');
            redirect('index.php?module=leaves');
        }

        include APP_ROOT . '/views/leaves/view.php';
    }

    public function request(): void {
        requirePermission('leaves', 'self');
        $empId = currentUser()['employee_id'];
        if (!$empId) { setFlash('error','Employee profile not found.'); redirect('index.php?module=dashboard'); }
        $errors     = [];
        $leaveTypes = $this->model->leaveTypes();
        $balance    = $this->model->getBalance($empId, date('Y'));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $data = [
                'employee_id'   => $empId,
                'leave_type_id' => (int)post('leave_type_id'),
                'start_date'    => sanitizeInput(post('start_date')),
                'end_date'      => sanitizeInput(post('end_date')),
                'reason'        => sanitizeInput(post('reason')),
            ];
            $reqFields = ['leave_type_id','start_date','end_date','reason'];
            $errors = validateRequired($reqFields, $data);
            if (empty($errors)) {
                if ($data['end_date'] < $data['start_date']) {
                    $errors['end_date'] = 'End date must be on or after start date.';
                } else {
                    $days = workingDaysBetween($data['start_date'], $data['end_date']);
                    $data['days_requested'] = $days;
                    // Check leave attachment
                    if (!empty($_FILES['attachment']['name'])) {
                        $up = uploadFile($_FILES['attachment'], UPLOAD_DOCUMENT_PATH, ALLOWED_DOC_TYPES);
                        if ($up['success']) { $data['attachment'] = $up['filename']; }
                        else { $errors['attachment'] = $up['message']; }
                    }
                }
            }
            if (empty($errors)) {
                $id = $this->model->create($data);
                // Notify HR
                $notif = new Notification();
                $notif->create(currentUserId(), 'Leave Request Submitted', 'Your leave request is pending review.', 'info', 'leaves');
                auditLog('request_leave','leaves','Submitted leave request',$id);
                setFlash('success','Leave request submitted successfully.');
                redirect('index.php?module=leaves&action=my');
            }
        }
        include APP_ROOT . '/views/leaves/request.php';
    }

    public function approve(): void {
        requirePermission('leaves', 'approve');
        $id = (int)post('id');
        validateCsrf();
        $leave = $this->model->findById($id);
        if (!$leave) { jsonResponse(false,'Leave request not found.'); }
        
        if (currentRole() === ROLE_DEPT_MANAGER && !hasRole(ROLE_SUPER_ADMIN, ROLE_HR_DIRECTOR)) {
            $me = (new Employee())->findById(currentUser()['employee_id']);
            if ($me && $leave['department_id'] != $me['department_id']) {
                jsonResponse(false, 'Unauthorized: You can only approve leaves for your own department.');
            }
        }

        $this->model->approve($id, currentUserId(), sanitizeInput(post('remarks','')));
        auditLog('approve','leaves',"Approved leave request #{$id}",$id);
        // Notify employee
        $notif = new Notification();
        $empUserId = db()->query("SELECT user_id FROM employees WHERE id={$leave['employee_id']}")->fetchColumn();
        if ($empUserId) $notif->create((int)$empUserId,'Leave Approved',"Your leave request has been approved.",'success','leaves');
        setFlash('success', 'Leave request approved successfully.');
        jsonResponse(true,'Leave approved successfully.');
    }

    public function reject(): void {
        requirePermission('leaves', 'approve');
        validateCsrf();
        $id = (int)post('id');
        $leave = $this->model->findById($id);
        if (!$leave) { jsonResponse(false,'Leave request not found.'); }
        
        if (currentRole() === ROLE_DEPT_MANAGER && !hasRole(ROLE_SUPER_ADMIN, ROLE_HR_DIRECTOR)) {
            $me = (new Employee())->findById(currentUser()['employee_id']);
            if ($me && $leave['department_id'] != $me['department_id']) {
                jsonResponse(false, 'Unauthorized: You can only reject leaves for your own department.');
            }
        }

        $this->model->reject($id, currentUserId(), sanitizeInput(post('remarks','')));
        
        $leave = $this->model->findById($id);
        $notif = new Notification();
        $empUserId = db()->query("SELECT user_id FROM employees WHERE id={$leave['employee_id']}")->fetchColumn();
        if ($empUserId) {
            $notif->create((int)$empUserId, 'Leave Rejected', "Your leave request has been rejected.", 'danger', 'leaves');
        }

        auditLog('reject','leaves',"Rejected leave request #{$id}",$id);
        setFlash('success', 'Leave request rejected successfully.');
        jsonResponse(true,'Leave rejected.');
    }

    public function cancel(): void {
        requireLogin();
        validateCsrf();
        $id    = (int)post('id');
        $empId = currentUser()['employee_id'];
        $this->model->cancel($id, $empId);
        setFlash('info','Leave request cancelled.');
        redirect('index.php?module=leaves&action=my');
    }

    public function balance(): void {
        requireLogin();
        $empId = currentUser()['employee_id'] ?: (int)get('employee_id');
        if (!$empId) { jsonResponse(false,'No employee.'); }
        $balance = $this->model->getBalance($empId, date('Y'));
        jsonResponse(true,'', $balance);
    }
}

/** Payroll Controller */
class PayrollController {
    private Payroll $model;
    public function __construct() { $this->model = new Payroll(); }

    public function index(): void {
        if (!hasRole(ROLE_SUPER_ADMIN, ROLE_FINANCE_MANAGER, ROLE_HR_DIRECTOR)) {
            redirect('index.php?module=payroll&action=myPayslips');
            return;
        }
        requirePermission('payroll', 'manage');
        $periods = $this->model->periods();
        include APP_ROOT . '/views/payroll/index.php';
    }

    public function create(): void {
        requirePermission('payroll', 'manage');
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $data = [
                'period_name' => sanitizeInput(post('period_name')),
                'start_date'  => sanitizeInput(post('start_date')),
                'end_date'    => sanitizeInput(post('end_date')),
                'pay_date'    => sanitizeInput(post('pay_date')),
                'created_by'  => currentUserId(),
            ];
            $errors = validateRequired(['period_name','start_date','end_date','pay_date'], $data);
            if (empty($errors)) {
                $id = $this->model->createPeriod($data);
                auditLog('create','payroll',"Created payroll period: {$data['period_name']}",$id);
                setFlash('success','Payroll period created. Now generate payslips.');
                redirect('index.php?module=payroll&action=generate&id=' . $id);
            }
        }
        include APP_ROOT . '/views/payroll/create.php';
    }

    public function generate(): void {
        requirePermission('payroll', 'manage');
        $id     = (int)get('id');
        $period = $this->model->findPeriod($id);
        if (!$period) { setFlash('error','Period not found.'); redirect('index.php?module=payroll'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $count = $this->model->generateForPeriod($id);
            auditLog('generate','payroll',"Generated payroll for period #{$id}: $count records",$id);
            setFlash('success',"Generated payroll for $count employees.");
            redirect('index.php?module=payroll&action=view&id=' . $id);
        }
        include APP_ROOT . '/views/payroll/generate.php';
    }

    public function view(): void {
        requirePermission('payroll', 'manage');
        $id     = (int)get('id');
        $period = $this->model->findPeriod($id);
        if (!$period) { setFlash('error','Period not found.'); redirect('index.php?module=payroll'); }
        $total  = db()->prepare("SELECT COUNT(*) FROM payroll WHERE period_id=?")->execute([$id]) ? (int)db()->query("SELECT COUNT(*) FROM payroll WHERE period_id=$id")->fetchColumn() : 0;
        $pg     = paginate($total, (int)get('page',1));
        $payslips = $this->model->payslips($id, $pg['per_page'], $pg['offset']);
        include APP_ROOT . '/views/payroll/view.php';
    }

    public function payslip(): void {
        requireLogin();
        $periodId = (int)get('period_id');
        $empId = (int)get('employee_id');
        // Employee can only see own
        if (currentRole() === ROLE_EMPLOYEE) {
            $empId = (int)(currentUser()['employee_id']);
        }
        $payslip = $this->model->findPayslip($periodId, $empId);
        if (!$payslip) { setFlash('error','Payslip not found.'); redirect('index.php?module=payroll'); }
        $settings = new Setting();
        include APP_ROOT . '/views/payroll/payslip.php';
    }

    public function myPayslips(): void {
        requirePermission('payroll', 'self');
        $empId = (int)currentUser()['employee_id'];
        $payslips = $this->model->employeePayslips($empId);
        include APP_ROOT . '/views/payroll/my_payslips.php';
    }

    public function approve(): void {
        requirePermission('payroll', 'approve');
        validateCsrf();
        $id = (int)post('period_id');
        $this->model->approvePeriod($id, currentUserId());
        auditLog('approve','payroll',"Approved payroll period #{$id}",$id);
        jsonResponse(true,'Payroll approved.');
    }

    public function markPaid(): void {
        requirePermission('payroll', 'manage');
        validateCsrf();
        $id = (int)post('period_id');
        $this->model->markPaid($id);
        auditLog('mark_paid','payroll',"Marked payroll #{$id} as paid",$id);
        jsonResponse(true,'Payroll marked as paid.');
    }
}

/** Recruitment Controller */
class RecruitmentController {
    private Job $jobModel;
    private Applicant $appModel;
    public function __construct() { $this->jobModel = new Job(); $this->appModel = new Applicant(); }

    public function index(): void {
        requirePermission('recruitment', 'manage');
        $filters = ['status'=>sanitizeInput(get('status')),'search'=>sanitizeInput(get('search'))];
        $total   = $this->jobModel->count($filters);
        $pg      = paginate($total, (int)get('page',1));
        $jobs    = $this->jobModel->all($filters, $pg['per_page'], $pg['offset']);
        $departments = (new Employee())->departments();
        include APP_ROOT . '/views/recruitment/index.php';
    }

    public function addJob(): void {
        requirePermission('recruitment', 'manage');
        $errors = []; $empModel = new Employee();
        $departments = $empModel->departments();
        $positions   = $empModel->positions();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $data = [
                'title'=>sanitizeInput(post('title')),'department_id'=>(int)post('department_id'),
                'position_id'=>(int)post('position_id') ?: null,
                'description'=>sanitizeInput(post('description')),'requirements'=>sanitizeInput(post('requirements')),
                'salary_min'=>post('salary_min')?:(null),'salary_max'=>post('salary_max')?:null,
                'employment_type'=>sanitizeInput(post('employment_type','full_time')),'vacancies'=>(int)post('vacancies',1),
                'status' => (hasRole(ROLE_SUPER_ADMIN, ROLE_HR_DIRECTOR) ? sanitizeInput(post('status', 'open')) : 'pending_approval'),
                'posted_by'=>currentUserId(),
                'deadline'=>sanitizeInput(post('deadline'))?:null,
            ];
            $errors = validateRequired(['title','department_id'], $data);
            if (empty($errors)) {
                $id = $this->jobModel->create($data);
                auditLog('create','recruitment',"Posted job: {$data['title']}",$id);

                if ($data['status'] === 'pending_approval') {
                    $userModel = new User();
                    $notifModel = new Notification();
                    $hrDirectors = $userModel->findByRole(ROLE_HR_DIRECTOR);
                    foreach ($hrDirectors as $hr) {
                        $notifModel->create(
                            (int)$hr['id'],
                            'New Job Posting Request',
                            "A new job posting '{$data['title']}' has been submitted for approval.",
                            'info',
                            'recruitment',
                            $id
                        );
                    }
                }

                setFlash('success','Job posted successfully!');
                redirect('index.php?module=recruitment&action=viewJob&id='.$id);
            }
        }
        include APP_ROOT . '/views/recruitment/add_job.php';
    }

    public function viewJob(): void {
        requirePermission('recruitment', 'manage');
        $job = $this->jobModel->findById((int)get('id'));
        if (!$job) { setFlash('error','Job not found.'); redirect('index.php?module=recruitment'); }
        $filters = ['job_id'=>$job['id'],'status'=>sanitizeInput(get('status'))];
        $total = $this->appModel->count($filters);
        $pg    = paginate($total, (int)get('page',1));
        $applicants = $this->appModel->all($filters, $pg['per_page'], $pg['offset']);
        include APP_ROOT . '/views/recruitment/view_job.php';
    }

    public function updateApplicant(): void {
        requirePermission('recruitment', 'manage');
        validateCsrf();
        $id     = (int)post('id');
        $status = sanitizeInput(post('status'));
        
        if ($status === 'hired' && !hasRole(ROLE_SUPER_ADMIN, ROLE_HR_DIRECTOR)) {
            jsonResponse(false, 'Unauthorized: Only HR Management can finalize a hire.');
        }
        
        $interviewDate = sanitizeInput(post('interview_date'));
        $interviewTime = sanitizeInput(post('interview_time'));
        $interviewDateTime = null;
        if (!empty($interviewDate) && !empty($interviewTime)) {
            $formatted = strtotime($interviewDate . ' ' . $interviewTime);
            if ($formatted) {
                $interviewDateTime = date('Y-m-d H:i:s', $formatted);
            }
        } elseif (!empty($interviewDate)) {
            $formatted = strtotime($interviewDate);
            if ($formatted) {
                $interviewDateTime = date('Y-m-d 00:00:00', $formatted);
            }
        }

        $extra  = [
            'interview_date'     => $interviewDateTime,
            'interviewed_by'     => currentUserId(),
            'interview_notes'    => sanitizeInput(post('interview_notes')),
            'interview_location' => sanitizeInput(post('interview_location')),
        ];
        $this->appModel->updateStatus($id, $status, $extra);
        auditLog('update','recruitment',"Updated applicant #{$id} status to $status",$id);

        if ($status === 'interview') {
            $app = $this->appModel->findById($id);
            if ($app && !empty($interviewDate)) {
                require_once APP_ROOT . '/includes/mailer.php';
                mailInterviewSchedule($app, [
                    'date'     => $interviewDate,
                    'time'     => $interviewTime,
                    'location' => $extra['interview_location']
                ]);
            }
        }

        if ($status === 'offered') {
            $app = $this->appModel->findById($id);
            if ($app) {
                $userModel = new User();
                $notifModel = new Notification();
                $hrDirectors = $userModel->findByRole(ROLE_HR_DIRECTOR);
                foreach ($hrDirectors as $hr) {
                    $notifModel->create(
                        (int)$hr['id'],
                        'Applicant Offered',
                        "Applicant {$app['first_name']} {$app['last_name']} has been offered. Please finalize the hiring process.",
                        'info',
                        'recruitment',
                        $id
                    );
                }
            }
        }

        if ($status === 'hired') {
            $app = $this->appModel->findById($id);
            if ($app) {
                $this->jobModel->checkAndAutoClose((int)$app['job_id']);
            }
        }
        
        if (isAjax()) {
            jsonResponse(true,"Applicant status updated to $status.");
        } else {
            setFlash('success', "Applicant status updated to ".ucfirst($status).".");
            redirect('index.php?module=recruitment&action=viewApplicant&id='.$id);
        }
    }

    public function editJob(): void {
        requirePermission('recruitment.edit');
        if (currentRole() === ROLE_RECRUITMENT_OFFICER) {
            setFlash('error', 'You do not have permission to edit jobs.');
            redirect('index.php?module=recruitment');
        }
        $id = (int)get('id');
        $job = $this->jobModel->findById($id);
        if (!$job) { setFlash('error','Job not found.'); redirect('index.php?module=recruitment'); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $data = [
                'title' => sanitizeInput(post('title')),
                'department_id' => (int)post('department_id'),
                'job_type' => sanitizeInput(post('job_type')),
                'location' => sanitizeInput(post('location')),
                'experience_level' => sanitizeInput(post('experience_level')),
                'salary_range' => sanitizeInput(post('salary_range')),
                'description' => post('description'), // Keep HTML
                'requirements' => post('requirements'),
                'status' => sanitizeInput(post('status'))
            ];
            $this->jobModel->update($id, $data);
            auditLog('update','recruitment',"Updated job: {$data['title']}",$id);
            setFlash('success','Job updated successfully.');
            redirect('index.php?module=recruitment&action=viewJob&id='.$id);
        }

        $empModel = new Employee();
        $departments = $empModel->departments();
        include APP_ROOT . '/views/recruitment/edit_job.php';
    }

    public function deleteJob(): void {
        requirePermission('recruitment.delete');
        if (currentRole() === ROLE_RECRUITMENT_OFFICER) {
            setFlash('error', 'You do not have permission to delete jobs.');
            redirect('index.php?module=recruitment');
        }
        $id = (int)post('id');
        $this->jobModel->delete($id);
        auditLog('delete','recruitment',"Deleted job #{$id}",$id);
        setFlash('success','Job deleted successfully.');
        redirect('index.php?module=recruitment');
    }

    public function approveJob(): void {
        requirePermission('recruitment', 'manage');
        if (!hasRole(ROLE_SUPER_ADMIN, ROLE_HR_DIRECTOR)) {
            setFlash('error', 'Unauthorized: Only HR Management can approve job postings.');
            redirect('index.php?module=recruitment');
        }
        validateCsrf();
        $id = (int)post('id');
        if ($this->jobModel->update($id, ['status' => 'open'])) {
            auditLog('update', 'recruitment', "Approved job posting #$id", $id);
            setFlash('success', 'Job posting approved and published.');
        } else {
            setFlash('error', 'Failed to approve job posting.');
        }
        redirect('index.php?module=recruitment&action=viewJob&id=' . $id);
    }


    public function viewApplicant(): void {
        requirePermission('recruitment', 'manage');
        $id = (int)get('id');
        $applicant = $this->appModel->findById($id);
        if (!$applicant) redirect('index.php?module=recruitment');
        include APP_ROOT . '/views/recruitment/view_applicant.php';
    }

    public function archiveApplicant(): void {
        requirePermission('recruitment', 'manage');
        validateCsrf();
        $id = (int)post('id');
        $jobId = (int)post('job_id');
        if ($this->appModel->archive($id)) {
            auditLog('archive','recruitment',"Archived applicant #{$id}",$id);
            setFlash('success','Applicant archived successfully!');
        }
        redirect('index.php?module=recruitment&action=viewJob&id='.$jobId);
    }
}

/** Performance Controller */
class PerformanceController {
    private Performance $model;
    public function __construct() { $this->model = new Performance(); }

    public function index(): void {
        requireLogin();
        $filters = [];
        if (hasRole(ROLE_EMPLOYEE)) {
            // If they are strictly employee, redirect to 'my'
            redirect('index.php?module=performance&action=my');
            return;
        } elseif (hasRole(ROLE_DEPT_MANAGER)) {
        $me = (new Employee())->findById(currentUser()['employee_id']);
        if (currentRole() === ROLE_DEPT_MANAGER && $me && !hasRole(ROLE_SUPER_ADMIN, ROLE_HR_DIRECTOR)) {
            $filters['department_id'] = $me['department_id'];
        }
        }
        $reviews = $this->model->all($filters);
        include APP_ROOT . '/views/performance/index.php';
    }

    public function my(): void {
        requirePermission('performance', 'self');
        $empId = currentUser()['employee_id'];
        if (!$empId) { setFlash('error','Employee profile not found.'); redirect('index.php?module=dashboard'); }
        // For employees, we usually only show 'published' or 'completed' reviews
        $reviews = $this->model->all(['employee_id' => $empId]);
        include APP_ROOT . '/views/performance/my.php';
    }

    public function create(): void {
        requirePermission('performance', 'review');
        $errors=[]; 
        $empModel = new Employee();
        $empFilters = [];
        $isHR = hasRole(ROLE_HR_DIRECTOR, ROLE_HR_SPECIALIST);
        $isManager = hasRole(ROLE_DEPT_MANAGER);
        $me = $empModel->findById(currentUser()['employee_id']);

        if (hasRole(ROLE_SUPER_ADMIN)) {
            // Super Admin sees everyone
            $empFilters = [];
        } elseif ($isHR) {
            // HR Directors/Specialists see all supervisors/managers but not regular employees
            $empFilters['exclude_role_slug'] = [ROLE_EMPLOYEE, ROLE_SUPER_ADMIN];
        } elseif ($isManager && $me) {
            // Managers only see regular employees in their own department
            $empFilters['department_id'] = $me['department_id'];
            $empFilters['role_slug'] = ROLE_EMPLOYEE;
        }
        
        $employees = $empModel->all($empFilters, 1000); 
        
        $kpis = $this->model->kpis();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $empId = (int)post('employee_id');
            
            // Security: Enforce same visibility rules on POST
            $targetEmp = $empModel->findById($empId);
            if (!$targetEmp) { jsonResponse(false, 'Employee not found.'); }

            if (hasRole(ROLE_SUPER_ADMIN)) {
                // No additional checks for Super Admin
            } elseif ($isHR) {
                if ($targetEmp['role_slug'] === ROLE_EMPLOYEE || $targetEmp['role_slug'] === ROLE_SUPER_ADMIN) {
                    jsonResponse(false, 'Unauthorized: You can only review management-level positions.');
                }
            } elseif ($isManager && $me) {
                if ($targetEmp['department_id'] != $me['department_id'] || $targetEmp['role_slug'] !== ROLE_EMPLOYEE) {
                    jsonResponse(false, 'Unauthorized: You can only review employees in your department.');
                }
            }

            $data = [
                'employee_id'   => $empId,
                'reviewer_id'   => currentUserId(),
                'review_period' => sanitizeInput(post('review_period')),
                'review_date'   => sanitizeInput(post('review_date', date('Y-m-d'))),
                'strengths'     => sanitizeInput(post('strengths')),
                'improvements'  => sanitizeInput(post('improvements')),
                'goals_next_period'=> sanitizeInput(post('goals_next_period')),
            ];
            $errors = validateRequired(['employee_id','review_period'], $data);
            if (empty($errors)) {
                $data['status'] = 'submitted';
                $id = $this->model->create($data);
                $scores = $_POST['kpi_scores'] ?? [];
                if (!empty($scores)) $this->model->saveKpiScores($id, $scores);
                
                // Notify Employee
                (new Notification())->create(
                    $targetEmp['user_id'],
                    'New Performance Review',
                    'A new performance review has been created for you. Please check "My Performance" to view your feedback.',
                    'info',
                    'performance',
                    $id
                );

                auditLog('create','performance',"Created performance review #{$id}",$id);
                setFlash('success','Performance review saved!');
                redirect('index.php?module=performance');
            }
        }
        include APP_ROOT . '/views/performance/create.php';
    }

    public function view(): void {
        requireLogin();
        $review = $this->model->findById((int)get('id'));
        if (!$review) { setFlash('error','Review not found.'); redirect('index.php?module=performance'); }
        // Security: Employee only own, Manager only dept
        if (currentRole() === ROLE_EMPLOYEE && $review['employee_id'] != currentUser()['employee_id']) {
            http_response_code(403); include APP_ROOT.'/views/errors/403.php'; exit;
        }
        if (currentRole() === ROLE_DEPT_MANAGER && !hasRole(ROLE_SUPER_ADMIN, ROLE_HR_DIRECTOR)) {
            $me = (new Employee())->findById(currentUser()['employee_id']);
            if ($me && $review['department_id'] !== $me['department_id'] && $review['employee_id'] !== $me['id']) {
                http_response_code(403); include APP_ROOT.'/views/errors/403.php'; exit;
            }
        }
        $scores = $this->model->getScores($review['id']);
        include APP_ROOT . '/views/performance/view.php';
    }

    public function kpis(): void {
        requirePermission('performance', 'manage');
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $data = [
                'name'        => sanitizeInput(post('name')),
                'description' => sanitizeInput(post('description')),
                'weight'      => (float)post('weight', 1.0),
                'department_id'=> post('department_id') ?: null,
            ];
            $errors = validateRequired(['name'], $data);
            if (empty($errors)) {
                $this->model->createKpi($data);
                auditLog('create_kpi','performance',"Created KPI: {$data['name']}");
                setFlash('success','KPI created successfully!');
                redirect('index.php?module=performance&action=kpis');
            }
        }
        $kpis = $this->model->kpis();
        $departments = (new Employee())->departments();
        include APP_ROOT . '/views/performance/kpis.php';
    }

    public function deleteKpi(): void {
        requirePermission('performance', 'manage');
        validateCsrf();
        $id = (int)post('id');
        if ($this->model->deleteKpi($id)) {
            auditLog('delete_kpi','performance',"Deleted KPI #{$id}",$id);
            setFlash('success','KPI deleted.');
        }
        redirect('index.php?module=performance&action=kpis');
    }

    public function acknowledge(): void {
        requirePermission('performance', 'self');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('index.php?module=performance&action=my'); }
        validateCsrf();
        $id = (int)post('id');
        $empId = currentUser()['employee_id'];
        $review = $this->model->findById($id);
        if (!$review || $review['employee_id'] != $empId) { jsonResponse(false, 'Unauthorized.'); }
        
        $this->model->update($id, ['employee_ack' => 1]);
        auditLog('acknowledge', 'performance', "Employee acknowledged review #$id", $id);
        jsonResponse(true, 'Review acknowledged.');
    }
}

/** Training Controller */
class TrainingController {
    private Training $model;
    public function __construct() { $this->model = new Training(); }

    public function index(): void {
        requirePermission('training', 'view');
        $filters = ['status'=>sanitizeInput(get('status'))];
        
        if (!hasRole('super_admin') && !hasRole('hr_director') && !hasRole('hr_specialist')) {
            $empId = currentUser()['employee_id'];
            if ($empId) {
                $stmt = db()->prepare("SELECT department_id FROM employees WHERE id=?");
                $stmt->execute([$empId]);
                $deptId = $stmt->fetchColumn();
                $filters['department_id'] = $deptId ?: -1;
            } else {
                $filters['department_id'] = -1;
            }
        }

        $trainings = $this->model->all($filters);
        $myEnrollments = currentUser()['employee_id'] ? $this->model->employeeTrainings(currentUser()['employee_id']) : [];
        include APP_ROOT . '/views/training/index.php';
    }

    public function create(): void {
        requirePermission('training', 'manage');
        $errors = [];
        $isManager = hasRole(ROLE_DEPT_MANAGER);
        $me = $isManager ? (new Employee())->findById(currentUser()['employee_id']) : null;

        if ($isManager && $me) {
            $departments = db()->query("SELECT id, name FROM departments WHERE id = {$me['department_id']} AND is_active=1")->fetchAll();
        } else {
            $departments = db()->query("SELECT id, name FROM departments WHERE is_active=1 ORDER BY name ASC")->fetchAll();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $deptId = post('department_id') ?: null;
            
            // Security: Enforce manager's department
            if ($isManager && $me) {
                $deptId = $me['department_id'];
            }

            $data = [
                'title'    => sanitizeInput(post('title')),
                'description'=>sanitizeInput(post('description')),
                'trainer'  => sanitizeInput(post('trainer')),
                'start_date'=> sanitizeInput(post('start_date')),
                'start_time'=> sanitizeInput(post('start_time')),
                'end_time'  => sanitizeInput(post('end_time')),
                'end_date'  => sanitizeInput(post('end_date')) ?: null,
                'location'  => sanitizeInput(post('location')),
                'max_participants'=>post('max_participants')?:null,
                'department_id'=>$deptId,
                'is_required'  => post('is_required') ? 1 : 0,
                'cost'      => (float)post('cost',0),
                'created_by'=> currentUserId(),
            ];
            $errors = validateRequired(['title','start_date','start_time','end_time'], $data);
            if (empty($errors)) {
                $id = $this->model->create($data);
                // Auto-enroll all eligible employees if training is required
                if ($data['is_required']) {
                    $this->model->autoEnrollDepartment($id, $data['department_id'] ? (int)$data['department_id'] : null);
                }
                auditLog('create','training',"Created training: {$data['title']}",$id);
                setFlash('success', $data['is_required'] ? 'Required training created and employees auto-enrolled!' : 'Training created!');
                redirect('index.php?module=training');
            }
        }
        include APP_ROOT . '/views/training/create.php';
    }

    public function enroll(): void {
        requireLogin();
        validateCsrf();
        $trainingId = (int)post('training_id');
        $empId      = currentUser()['employee_id'];
        if (!$empId) { jsonResponse(false,'No employee profile.'); }
        $this->model->enroll($trainingId, $empId);
        auditLog('enroll','training',"Enrolled in training #{$trainingId}",$empId);
        jsonResponse(true,'Enrolled successfully!');
    }

    public function feedback(): void {
        requireLogin();
        validateCsrf();
        $trainingId = (int)post('training_id');
        $empId      = currentUser()['employee_id'];
        $rating     = (int)post('rating');
        $feedback   = sanitizeInput(post('feedback'));
        if (!$empId) { jsonResponse(false,'No employee profile.'); }
        
        // One-time feedback constraint
        $existing = db()->prepare("SELECT rating FROM training_enrollments WHERE training_id=? AND employee_id=?");
        $existing->execute([$trainingId, $empId]);
        if ($existing->fetchColumn()) {
            jsonResponse(false, 'You have already submitted feedback for this training.');
        }

        if ($rating < 1 || $rating > 5) { jsonResponse(false,'Rating must be between 1 and 5.'); }
        $stmt = db()->prepare("UPDATE training_enrollments SET rating=?, feedback=? WHERE training_id=? AND employee_id=?");
        $stmt->execute([$rating, $feedback, $trainingId, $empId]);
        auditLog('feedback','training',"Submitted feedback for training #{$trainingId}",$empId);
        jsonResponse(true,'Feedback submitted! Thank you.');
    }

    public function viewFeedback(): void {
        requireLogin();
        if (!hasRole('super_admin') && !hasRole('hr_director') && !hasRole('hr_specialist') &&
            !hasRole('department_manager') && !hasRole('finance_manager') && !hasRole('recruitment_officer')) {
            http_response_code(403); include APP_ROOT.'/views/errors/403.php'; exit;
        }
        $trainingId = (int)get('id');
        $training   = $this->model->findById($trainingId);
        if (!$training) { setFlash('error','Training not found.'); redirect('index.php?module=training'); }
        $feedbacks  = $this->model->getFeedback($trainingId);
        $avgRating  = count($feedbacks) ? round(array_sum(array_column($feedbacks,'rating')) / count($feedbacks), 1) : null;
        include APP_ROOT . '/views/training/feedback.php';
    }
}

/** Document Controller */
class DocumentController {
    private Document $model;
    public function __construct() { $this->model = new Document(); }

    public function index(): void {
        requirePermission('documents', 'manage');
        $filters = [];
        if (!empty(get('employee_id'))) { $filters['admin_view']=true; $filters['employee_id2']=(int)get('employee_id'); }
        $documents = $this->model->all($filters);
        include APP_ROOT . '/views/documents/index.php';
    }

    public function my(): void {
        requirePermission('documents', 'self');
        $empId = currentUser()['employee_id'];
        if (!$empId) { setFlash('error','Employee profile not found.'); redirect('index.php?module=dashboard'); }
        $filters = ['employee_id' => $empId];
        $documents = $this->model->all($filters);
        include APP_ROOT . '/views/documents/index.php';
    }

    public function upload(): void {
        requireLogin();
        // Additional check: if they are uploader for self or manage
        if (!can('documents', 'self') && !can('documents', 'manage')) {
            requirePermission('documents', 'manage'); // Will trigger 403 correctly
        }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $empId = currentRole() === ROLE_EMPLOYEE ? currentUser()['employee_id'] : (int)post('employee_id');
            if (empty($_FILES['file']['name'])) { setFlash('error','No file selected.'); redirect('index.php?module=documents'); }
            $up = uploadFile($_FILES['file'], UPLOAD_DOCUMENT_PATH, ALLOWED_DOC_TYPES);
            if ($up['success']) {
                $this->model->create([
                    'employee_id'=> $empId ?: null,
                    'title'      => sanitizeInput(post('title', $_FILES['file']['name'])),
                    'category'   => sanitizeInput(post('category','other')),
                    'filename'   => $up['filename'],
                    'file_type'  => $_FILES['file']['type'],
                    'file_size'  => $_FILES['file']['size'],
                    'uploaded_by'=> currentUserId(),
                    'is_public'  => (int)(bool)post('is_public'),
                ]);
                auditLog('upload','documents',"Uploaded document: ".$_FILES['file']['name']);
                setFlash('success','Document uploaded.');
            } else { setFlash('error',$up['message']); }
        }
        redirect('index.php?module=documents');
    }

    public function delete(): void {
        requirePermission('documents', 'manage');
        validateCsrf();
        $id  = (int)post('id');
        $doc = $this->model->findById($id);
        if ($doc) {
            $path = UPLOAD_DOCUMENT_PATH . $doc['filename'];
            if (file_exists($path)) unlink($path);
            $this->model->delete($id);
            auditLog('delete','documents',"Deleted document #{$id}",$id);
        }
        setFlash('success','Document deleted.');
        redirect('index.php?module=documents');
    }
}

/** Notification Controller */
class NotificationController {
    private Notification $model;
    public function __construct() { $this->model = new Notification(); }

    public function index(): void {
        requireLogin();
        $notifications = $this->model->forUser(currentUserId(), false, 50);
        $this->model->markAllRead(currentUserId());
        include APP_ROOT . '/views/notifications/index.php';
    }

    public function read(): void {
        requireLogin();
        $id = (int)get('id');
        $n = $this->model->findById($id);
        if (!$n || (int)$n['user_id'] !== currentUserId()) {
            redirect('index.php?module=notifications');
        }

        $this->model->markRead($id, currentUserId());

        $url = 'index.php?module=notifications';
        if ($n['module'] === 'payroll' && !empty($n['module_id'])) {
            $user = currentUser();
            $empId = $user['employee_id'] ?? null;
            if ($empId) {
                $url = "index.php?module=payroll&action=payslip&period_id={$n['module_id']}&employee_id={$empId}";
            }
        } elseif ($n['module'] === 'leaves' && !empty($n['module_id'])) {
            $url = "index.php?module=leaves&action=view&id={$n['module_id']}";
        }

        redirect($url);
    }

    public function markRead(): void {
        requireLogin();
        $id = (int)post('id');
        $this->model->markRead($id, currentUserId());
        jsonResponse(true,'Marked as read.');
    }

    public function unreadCount(): void {
        requireLogin();
        jsonResponse(true,'', $this->model->unreadCount(currentUserId()));
    }
}

/** Audit Log Controller */
class AuditController {
    public function index(): void {
        requirePermission('audit', 'view');
        $model = new AuditLog();
        $filters = [
            'module'    => sanitizeInput(get('log_module')),
            'date_from' => sanitizeInput(get('date_from', date('Y-m-01'))),
            'date_to'   => sanitizeInput(get('date_to', date('Y-m-d'))),
            'search'    => sanitizeInput(get('search')),
        ];
        $total = $model->count($filters);
        $pg    = paginate($total, (int)get('page',1));
        $logs  = $model->all($filters, $pg['per_page'], $pg['offset']);
        include APP_ROOT . '/views/audit/index.php';
    }
}

/** Settings Controller */
class SettingsController {
    public function index(): void {
        requirePermission('settings', 'manage');
        $model    = new Setting();
        $settings = $model->all();
        $backups  = glob(APP_ROOT . '/uploads/backups/*.sql');
        rsort($backups);
        include APP_ROOT . '/views/settings/index.php';
    }

    public function update(): void {
        requirePermission('settings', 'manage');
        validateCsrf();
        $model = new Setting();
        $data  = $_POST;
        unset($data['csrf_token']);
        $model->bulkUpdate($data);
        auditLog('update','settings','Updated system settings');
        setFlash('success','Settings saved successfully.');
        redirect('index.php?module=settings');
    }

    public function backup(): void {
        requirePermission('settings', 'manage_backups');
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $path = APP_ROOT . '/uploads/backups/' . $filename;
        
        $mysqlPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        $pass = DB_PASS ? '-p' . DB_PASS : '';
        $cmd = "\"$mysqlPath\" --user=" . DB_USER . " $pass " . DB_NAME . " > \"$path\" 2>&1";
        
        exec($cmd, $output, $returnVar);
        
        if ($returnVar === 0) {
            auditLog('backup','settings',"Created database backup: $filename");
            setFlash('success', 'Database backup created successfully.');
        } else {
            error_log("Backup failed: " . implode("\n", $output));
            setFlash('error', 'Database backup failed. Check logs.');
        }
        redirect('index.php?module=settings');
    }

    public function restore(): void {
        requirePermission('settings', 'manage');
        validateCsrf();

        if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
            setFlash('error', 'Please upload a valid SQL file.');
            redirect('index.php?module=settings');
        }

        $file = $_FILES['backup_file']['tmp_name'];
        $mysqlPath = 'C:\\xampp\\mysql\\bin\\mysql.exe';
        $pass = DB_PASS ? '-p' . DB_PASS : '';
        $cmd = "\"$mysqlPath\" --user=" . DB_USER . " $pass " . DB_NAME . " < \"$file\" 2>&1";

        exec($cmd, $output, $returnVar);

        if ($returnVar === 0) {
            auditLog('restore','settings','Restored database from backup file');
            setFlash('success', 'Database restored successfully.');
        } else {
            error_log("Restore failed: " . implode("\n", $output));
            setFlash('error', 'Database restore failed. Check logs.');
        }
        redirect('index.php?module=settings');
    }

    public function downloadBackup(): void {
        requirePermission('settings', 'manage_backups');
        $file = get('file');
        $path = APP_ROOT . '/uploads/backups/' . basename($file);
        
        if (file_exists($path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($path).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;
        }
        setFlash('error', 'Backup file not found.');
        redirect('index.php?module=settings');
    }

    public function deleteBackup(): void {
        requirePermission('settings', 'manage_backups');
        $file = get('file');
        $path = APP_ROOT . '/uploads/backups/' . basename($file);
        
        if (file_exists($path)) {
            unlink($path);
            setFlash('success', 'Backup deleted.');
        } else {
            setFlash('error', 'File not found.');
        }
        redirect('index.php?module=settings');
    }
}

/** Report Controller */
class ReportController {
    public function index(): void {
        requirePermission('reports', 'view');
        
        // --- Average Salary ---
        $activeEmployees = (new Employee())->all(['status'=>'active'], 5000);
        $totalSalary = 0; 
        $salaryCount = count($activeEmployees); // Counting all 16 active employees
        
        foreach ($activeEmployees as $emp) {
            if (!empty($emp['basic_salary'])) {
                $totalSalary += (float)$emp['basic_salary'];
            }
        }
        $avgSalaryRaw = $salaryCount > 0 ? ($totalSalary / $salaryCount) : 0;

        $startOfLastMonth = date('Y-m-01', strtotime('first day of last month'));
        $endOfLastMonth = date('Y-m-t', strtotime('last day of last month'));
        $startOfThisMonth = date('Y-m-01');

        // Total Employees Trend
        $totalEmployees = db()->query("SELECT COUNT(*) FROM employees WHERE status='active'")->fetchColumn() ?: 0;
        $totalEmployeesLastMonth = db()->query("SELECT COUNT(*) FROM employees WHERE status='active' AND date_hired <= '$endOfLastMonth'")->fetchColumn() ?: 0;
        $empTrendVal = $totalEmployeesLastMonth > 0 ? (($totalEmployees - $totalEmployeesLastMonth) / $totalEmployeesLastMonth) * 100 : 0;
        
        // New Hires
        $newHiresMTD = db()->query("SELECT COUNT(*) FROM employees WHERE date_hired >= '$startOfThisMonth'")->fetchColumn() ?: 0;
        $newHiresLastMTD = db()->query("SELECT COUNT(*) FROM employees WHERE date_hired >= '$startOfLastMonth' AND date_hired <= '$endOfLastMonth'")->fetchColumn() ?: 0;
        $hireTrendVal = $newHiresLastMTD > 0 ? (($newHiresMTD - $newHiresLastMTD) / $newHiresLastMTD) * 100 : ($newHiresMTD > 0 ? 100 : 0);

        // Attendance
        $thisMonthAtt = db()->query("SELECT COUNT(*) as t, SUM(CASE WHEN status IN ('present', 'late') THEN 1 ELSE 0 END) as p FROM attendance WHERE date >= '$startOfThisMonth'")->fetch();
        $avgAttThisMonth = $thisMonthAtt['t'] > 0 ? ($thisMonthAtt['p'] / $thisMonthAtt['t']) * 100 : 0;
        $lastMonthAtt = db()->query("SELECT COUNT(*) as t, SUM(CASE WHEN status IN ('present', 'late') THEN 1 ELSE 0 END) as p FROM attendance WHERE date >= '$startOfLastMonth' AND date <= '$endOfLastMonth'")->fetch();
        $avgAttLastMonth = $lastMonthAtt['t'] > 0 ? ($lastMonthAtt['p'] / $lastMonthAtt['t']) * 100 : 0;
        $attTrendVal = $avgAttThisMonth - $avgAttLastMonth;

        $stats = [
            'total_employees' => $totalEmployees,
            'emp_trend' => ($empTrendVal >= 0 ? '+' : '') . number_format($empTrendVal, 1) . '%',
            'emp_trend_color' => $empTrendVal >= 0 ? 'text-success' : 'text-danger',
            
            'new_hires_mtd' => $newHiresMTD,
            'new_hires_trend' => ($hireTrendVal >= 0 ? '+' : '') . number_format($hireTrendVal, 1) . '%',
            'hire_trend_color' => $hireTrendVal >= 0 ? 'text-success' : 'text-danger',
            
            'avg_attendance' => number_format($avgAttThisMonth, 1) . '%',
            'att_trend' => ($attTrendVal >= 0 ? '+' : '') . number_format($attTrendVal, 1) . '%',
            'att_trend_color' => $attTrendVal >= 0 ? 'text-success' : 'text-danger',
            
            'avg_salary' => '₱' . number_format($avgSalaryRaw, 2),
        ];

        // --- Employee Growth Trend (Last 6 Months) ---
        $growthMonths = [];
        $growthSeries = [];
        for ($i = 5; $i >= 0; $i--) {
            // Last day of that specific month
            $mDate = date('Y-m-t', strtotime("-$i months"));
            $growthMonths[] = date('M', strtotime("-$i months"));
            // Count employees who were hired on or before that month end
            // And who hadn't been terminated yet by that month end
            $c = db()->query("
                SELECT COUNT(*) FROM employees 
                WHERE date_hired <= '$mDate' 
                AND (date_separated IS NULL OR date_separated > '$mDate')
            ")->fetchColumn();
            $growthSeries[] = (int)$c;
        }

        $deptDist = db()->query("SELECT d.name, COUNT(e.id) as count FROM departments d LEFT JOIN employees e ON e.department_id = d.id AND e.status='active' GROUP BY d.name")->fetchAll();
        $deptLabels = array_column($deptDist, 'name');
        $deptSeries = array_map('intval', array_column($deptDist, 'count'));
        
        if (empty($deptLabels) || array_sum($deptSeries) == 0) {
            $deptLabels = ['Sales', 'HR', 'Finance', 'Engineering', 'Operations'];
            $deptSeries = [22, 9, 11, 30, 14];
        }

        // --- Leave Request Status by Month (Last 6 Months) ---
        $leaveMonths = [];
        $leaveSeriesDB = [
            'approved' => [0,0,0,0,0,0],
            'pending' => [0,0,0,0,0,0],
            'rejected' => [0,0,0,0,0,0]
        ];
        
        for ($i = 5; $i >= 0; $i--) {
            $leaveMonths[] = date('M', strtotime("-$i months"));
        }

        $sixMonthsAgo = date('Y-m-01', strtotime('-5 months'));
        $leavesQuery = db()->query("
            SELECT 
                status, 
                MONTH(start_date) as m, 
                YEAR(start_date) as y,
                COUNT(*) as count 
            FROM leaves 
            WHERE start_date >= '$sixMonthsAgo' 
            AND status IN ('approved', 'pending', 'rejected')
            GROUP BY status, y, m
        ")->fetchAll();

        foreach ($leavesQuery as $row) {
            $monthName = date('M', mktime(0, 0, 0, $row['m'], 1, $row['y']));
            $index = array_search($monthName, $leaveMonths);
            if ($index !== false && isset($leaveSeriesDB[$row['status']])) {
                $leaveSeriesDB[$row['status']][$index] = (int)$row['count'];
            }
        }

        // --- Weekly Attendance Overview ---
        $mon = date('Y-m-d', strtotime('monday this week'));
        $fri = date('Y-m-d', strtotime('friday this week'));
        $attSeriesDB = ['present'=>[0,0,0,0,0], 'on_leave'=>[0,0,0,0,0], 'absent'=>[0,0,0,0,0]];
        $attDates = [
            $mon,
            date('Y-m-d', strtotime('tuesday this week')),
            date('Y-m-d', strtotime('wednesday this week')),
            date('Y-m-d', strtotime('thursday this week')),
            $fri,
        ];

        $attQuery = db()->query("
            SELECT DATE(`date`) as d, status, COUNT(*) as c 
            FROM attendance 
            WHERE `date` >= '$mon' AND `date` <= '$fri' 
            GROUP BY d, status
        ")->fetchAll();

        foreach ($attQuery as $r) {
            $i = array_search($r['d'], $attDates);
            if ($i !== false) {
                if ($r['status'] === 'present' || $r['status'] === 'late') $attSeriesDB['present'][$i] += (int)$r['c'];
                elseif ($r['status'] === 'on_leave') $attSeriesDB['on_leave'][$i] += (int)$r['c'];
                elseif ($r['status'] === 'absent') $attSeriesDB['absent'][$i] += (int)$r['c'];
            }
        }

        // --- Monthly Payroll Trend (Last 6 Months) ---
        $payrollMonths = $leaveMonths; // Reuse the same 6 months
        $payrollGross = [0,0,0,0,0,0];
        $payrollNet = [0,0,0,0,0,0];
        $payrollDeductions = [0,0,0,0,0,0];
        
        $payrollQuery = db()->query("
            SELECT 
                MONTH(pp.pay_date) as m, 
                YEAR(pp.pay_date) as y,
                SUM(p.gross_pay) as gross,
                SUM(p.net_pay) as net,
                SUM(p.total_deductions) as deductions
            FROM payroll p
            JOIN payroll_periods pp ON p.period_id = pp.id
            WHERE pp.pay_date >= '$sixMonthsAgo'
            AND pp.status IN ('paid', 'approved')
            GROUP BY y, m
            ORDER BY y ASC, m ASC
        ")->fetchAll();

        foreach ($payrollQuery as $row) {
            $monthName = date('M', mktime(0, 0, 0, $row['m'], 1, $row['y']));
            $index = array_search($monthName, $payrollMonths);
            if ($index !== false) {
                $payrollGross[$index] = (float)$row['gross'];
                $payrollNet[$index] = (float)$row['net'];
                $payrollDeductions[$index] = (float)$row['deductions'];
            }
        }

        // --- Department Payroll Cost (Latest Period) ---
        $latestPeriodId = db()->query("SELECT id FROM payroll_periods ORDER BY pay_date DESC LIMIT 1")->fetchColumn();
        $deptPayrollLabels = [];
        $deptTotalCost = [];
        $deptAvgSalary = [];

        if ($latestPeriodId) {
            $deptPayQuery = db()->query("
                SELECT d.name, SUM(p.gross_pay) as total_cost, AVG(p.basic_salary) as avg_sal
                FROM payroll p
                JOIN employees e ON p.employee_id = e.id
                JOIN departments d ON e.department_id = d.id
                WHERE p.period_id = $latestPeriodId
                GROUP BY d.id
            ")->fetchAll();

            foreach ($deptPayQuery as $row) {
                $deptPayrollLabels[] = $row['name'];
                $deptTotalCost[] = (float)$row['total_cost'];
                $deptAvgSalary[] = (float)$row['avg_sal'];
            }
        }

        // Mock data if empty
        if (empty($deptPayrollLabels)) {
            $deptPayrollLabels = ['Engineering', 'Sales', 'Marketing', 'HR', 'Finance', 'Operations'];
            $deptTotalCost = [2850000, 1920000, 1450000, 750000, 980000, 1680000];
            $deptAvgSalary = [95000, 64000, 72500, 50000, 65000, 56000];
        }
        if (array_sum($payrollGross) == 0) {
            $payrollGross = [820000, 850000, 880000, 895000, 910000, 925000];
            $payrollDeductions = [150000, 160000, 165000, 170000, 175000, 180000];
            $payrollNet = array_map(function($g, $d) { return $g - $d; }, $payrollGross, $payrollDeductions);
        }
        
        include APP_ROOT . '/views/reports/index.php';
    }

    public function attendance(): void {
        requirePermission('reports', 'view');
        $filters = [
            'date_from'     => sanitizeInput(get('date_from', date('Y-m-01'))),
            'date_to'       => sanitizeInput(get('date_to', date('Y-m-d'))),
            'department_id' => (int)get('department_id'),
        ];
        $records = (new Attendance())->list($filters, 500, 0);
        $departments = (new Employee())->departments();
        $export = get('export');
        if ($export === 'csv') { $this->exportCsv($records, 'attendance_report'); }
        include APP_ROOT . '/views/reports/attendance.php';
    }

    public function payroll(): void {
        requirePermission('reports', 'view');
        $periods = (new Payroll())->summary();
        include APP_ROOT . '/views/reports/payroll.php';
    }

    public function employees(): void {
        requirePermission('reports', 'view');
        $employees   = (new Employee())->all([], 500, 0);
        $departments = (new Employee())->departments();
        $export = get('export');
        if ($export === 'csv') { $this->exportCsv($employees, 'employee_report'); }
        include APP_ROOT . '/views/reports/employees.php';
    }

    private function exportCsv(array $data, string $filename): void {
        if (empty($data)) { setFlash('error','No data to export.'); redirect($_SERVER['HTTP_REFERER']??'index.php?module=reports'); }
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '_' . date('Ymd') . '.csv"');
        $out = fopen('php://output','w');
        fputcsv($out, array_keys($data[0]));
        foreach ($data as $row) fputcsv($out, $row);
        fclose($out);
        exit;
    }
}
