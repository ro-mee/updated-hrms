<?php
/**
 * Jobs Controller - Public Facing
 */
class JobsController {
    private Job $jobModel;
    private Applicant $appModel;

    public function __construct() {
        $this->jobModel = new Job();
        $this->appModel = new Applicant();
    }

    /** List all open jobs */
    public function index(): void {
        $filters = ['status' => 'open'];
        $jobs = $this->jobModel->all($filters, 100, 0);
        include APP_ROOT . '/views/public_jobs/index.php';
    }

    /** View specific job details */
    public function view(): void {
        $id = (int)get('id');
        $job = $this->jobModel->findById($id);
        if (!$job || $job['status'] !== 'open') {
            http_response_code(404);
            include APP_ROOT . '/views/errors/404.php';
            exit;
        }
        include APP_ROOT . '/views/public_jobs/view.php';
    }

    /** Show application form */
    public function apply(): void {
        $id = (int)get('id');
        $job = $this->jobModel->findById($id);
        if (!$job || $job['status'] !== 'open') {
            redirect('index.php?module=jobs');
        }
        include APP_ROOT . '/views/public_jobs/apply.php';
    }

    /** Handle application submission */
    public function submit(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?module=jobs');
        }

        // Note: No CSRF validation for public forms unless a generic token is used.
        // For simplicity, we'll skip validateCsrf() here or use a session-based one.
        
        $jobId = (int)post('job_id');
        $data = [
            'job_id'       => $jobId,
            'first_name'   => sanitizeInput(post('first_name')),
            'last_name'    => sanitizeInput(post('last_name')),
            'email'        => sanitizeInput(post('email')),
            'phone'        => sanitizeInput(post('phone')),
            'birth_date'   => sanitizeInput(post('birth_date')),
            'gender'       => sanitizeInput(post('gender')),
            'civil_status' => sanitizeInput(post('civil_status')),
            'address'      => sanitizeInput(post('address')),
            'city'         => sanitizeInput(post('city')),
            'sss_number'   => sanitizeInput(post('sss_number')),
            'philhealth_number' => sanitizeInput(post('philhealth_number')),
            'pagibig_number'    => sanitizeInput(post('pagibig_number')),
            'tin_number'        => sanitizeInput(post('tin_number')),
            'emergency_contact_name'  => sanitizeInput(post('emergency_contact_name')),
            'emergency_contact_phone' => sanitizeInput(post('emergency_contact_phone')),
            'cover_letter' => sanitizeInput(post('cover_letter')),
            'source'       => 'Public Portal',
        ];

        $errors = validateRequired(['first_name', 'last_name', 'email'], $data);
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email address.';
        }

        if (empty($errors)) {
            // Handle Resume Upload
            if (!empty($_FILES['resume']['name'])) {
                $up = uploadFile($_FILES['resume'], UPLOAD_RESUME_PATH, ALLOWED_DOC_TYPES);
                if ($up['success']) {
                    $data['resume'] = $up['filename'];
                } else {
                    $errors['resume'] = $up['message'];
                }
            } else {
                $errors['resume'] = 'Please upload your resume.';
            }
        }

        if (empty($errors)) {
            $appId = $this->appModel->create($data);
            if (isset($data['resume'])) {
                $this->appModel->uploadResume($appId, $data['resume']);
            }
            $job = $this->jobModel->findById($jobId);
            include APP_ROOT . '/views/public_jobs/success.php';
        } else {
            $job = $this->jobModel->findById($jobId);
            include APP_ROOT . '/views/public_jobs/apply.php';
        }
    }
}
