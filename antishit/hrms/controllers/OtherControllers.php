<?php
/** Attendance Controller */
class AttendanceController {
    private Attendance $model;
    public function __construct() { $this->model = new Attendance(); }

    public function index(): void {
        requireLogin();
        requirePermission('attendance', 'manage');
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
        $records  = $this->model->list($filters, 31, 0);
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
}

/** Leave Controller */
class LeaveController {
    private Leave $model;
    public function __construct() { $this->model = new Leave(); }

    public function index(): void {
        requirePermission('leaves', 'manage');
        $filters = ['status' => sanitizeInput(get('status'))];
        $me = (new Employee())->findById(currentUser()['employee_id']);
        if (currentRole() === ROLE_DEPT_MANAGER && $me) {
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
                redirect('index.php?module=leaves');
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
        redirect('index.php?module=leaves');
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
                'status'=>sanitizeInput(post('status','open')),'deadline'=>sanitizeInput(post('deadline'))?:null,
                'posted_by'=>currentUserId(),
            ];
            $errors = validateRequired(['title','department_id'], $data);
            if (empty($errors)) {
                $id = $this->jobModel->create($data);
                auditLog('create','recruitment',"Posted job: {$data['title']}",$id);
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
        $extra  = [
            'interview_date'  => sanitizeInput(post('interview_date')),
            'interviewed_by'  => currentUserId(),
            'interview_notes' => sanitizeInput(post('interview_notes')),
        ];
        $this->appModel->updateStatus($id, $status, $extra);
        auditLog('update','recruitment',"Updated applicant #{$id} status to $status",$id);

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
        requirePermission('recruitment', 'manage');
        $id = (int)get('id');
        $job = $this->jobModel->findById($id);
        if (!$job) { setFlash('error','Job not found.'); redirect('index.php?module=recruitment'); }
        $errors = []; $empModel = new Employee();
        $departments = $empModel->departments();
        $positions   = $empModel->positions();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $data = [
                'title'=>sanitizeInput(post('title')),'department_id'=>(int)post('department_id'),
                'position_id'=>(int)post('position_id') ?: null,
                'description'=>sanitizeInput(post('description')),'requirements'=>sanitizeInput(post('requirements')),
                'salary_min'=>post('salary_min')?:null,'salary_max'=>post('salary_max')?:null,
                'employment_type'=>sanitizeInput(post('employment_type','full_time')),'vacancies'=>(int)post('vacancies',1),
                'status'=>sanitizeInput(post('status','open')),'deadline'=>sanitizeInput(post('deadline'))?:null,
            ];
            $errors = validateRequired(['title','department_id'], $data);
            if (empty($errors)) {
                $this->jobModel->update($id, $data);
                auditLog('edit','recruitment',"Updated job: {$data['title']}",$id);
                setFlash('success','Job updated successfully!');
                redirect('index.php?module=recruitment&action=viewJob&id='.$id);
            }
        }
        include APP_ROOT . '/views/recruitment/edit_job.php';
    }

    public function deleteJob(): void {
        requirePermission('recruitment', 'manage');
        validateCsrf();
        $id = (int)post('id');
        $job = $this->jobModel->findById($id);
        if ($job) {
            $this->jobModel->delete($id);
            auditLog('delete','recruitment',"Deleted job: {$job['title']}",$id);
            setFlash('success','Job deleted successfully.');
        }
        redirect('index.php?module=recruitment');
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
        if (currentRole() === ROLE_EMPLOYEE) $filters['employee_id'] = currentUser()['employee_id'];
        $reviews = $this->model->all($filters);
        include APP_ROOT . '/views/performance/index.php';
    }

    public function create(): void {
        requirePermission('performance', 'manage');
        $errors=[]; $employees=(new Employee())->all(); $kpis=$this->model->kpis();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $data = [
                'employee_id'   => (int)post('employee_id'),
                'reviewer_id'   => currentUserId(),
                'review_period' => sanitizeInput(post('review_period')),
                'review_date'   => sanitizeInput(post('review_date', date('Y-m-d'))),
                'strengths'     => sanitizeInput(post('strengths')),
                'improvements'  => sanitizeInput(post('improvements')),
                'goals_next_period'=> sanitizeInput(post('goals_next_period')),
            ];
            $errors = validateRequired(['employee_id','review_period'], $data);
            if (empty($errors)) {
                $id = $this->model->create($data);
                $scores = $_POST['kpi_scores'] ?? [];
                if (!empty($scores)) $this->model->saveKpiScores($id, $scores);
                $this->model->update($id, ['status'=>'submitted']);
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
        include APP_ROOT . '/views/performance/view.php';
    }
}

/** Training Controller */
class TrainingController {
    private Training $model;
    public function __construct() { $this->model = new Training(); }

    public function index(): void {
        requireLogin();
        $filters = ['status'=>sanitizeInput(get('status'))];
        $trainings = $this->model->all($filters);
        $myEnrollments = currentUser()['employee_id'] ? $this->model->employeeTrainings(currentUser()['employee_id']) : [];
        include APP_ROOT . '/views/training/index.php';
    }

    public function create(): void {
        requirePermission('training', 'manage');
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $data = [
                'title'    => sanitizeInput(post('title')),
                'description'=>sanitizeInput(post('description')),
                'trainer'  => sanitizeInput(post('trainer')),
                'start_date'=> sanitizeInput(post('start_date')),
                'end_date'  => sanitizeInput(post('end_date')),
                'location'  => sanitizeInput(post('location')),
                'max_participants'=>post('max_participants')?:null,
                'cost'      => (float)post('cost',0),
                'created_by'=> currentUserId(),
            ];
            $errors = validateRequired(['title','start_date','end_date'], $data);
            if (empty($errors)) {
                $id = $this->model->create($data);
                auditLog('create','training',"Created training: {$data['title']}",$id);
                setFlash('success','Training created!');
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
        requirePermission('settings', 'view');
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
