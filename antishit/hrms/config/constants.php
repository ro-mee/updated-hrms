<?php
/**
 * Application Constants
 * HRMS - Human Resource Management System
 */

// ── Application ──────────────────────────────────────────────────
define('APP_NAME',    'NexaHR');
define('APP_VERSION', '1.0.0');
define('APP_URL',     'http://localhost/updated-hrms/antishit/hrms');
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));   // /hrms directory
}

// ── Timezone ──────────────────────────────────────────────────────
define('APP_TIMEZONE', 'Asia/Manila');

// ── Roles (must match `roles`.name in DB) ─────────────────────────
define('ROLE_SUPER_ADMIN',       'super_admin');
define('ROLE_HR_DIRECTOR',       'hr_director');
define('ROLE_DEPT_MANAGER',      'department_manager');
define('ROLE_FINANCE_MANAGER',   'finance_manager');
define('ROLE_HR_SPECIALIST',     'hr_specialist');
define('ROLE_RECRUITMENT_OFFICER','recruitment_officer');
define('ROLE_EMPLOYEE',          'employee');

// ── Permissions (module.action format) ────────────────────────────
// Defined in DB; these are PHP-side constants for quick reference
define('PERM_ALL', '*');

// ── Pagination ──────────────────────────────────────────────────
define('RECORDS_PER_PAGE', 10);

// ── Upload Limits ────────────────────────────────────────────────
define('MAX_FILE_SIZE',  5 * 1024 * 1024); // 5 MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('ALLOWED_DOC_TYPES',   ['application/pdf', 'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-word', 'application/vnd.ms-office',
                                'application/octet-stream', 'application/zip',
                                'image/jpeg', 'image/png']);
define('UPLOAD_AVATAR_PATH',   APP_ROOT . '/uploads/avatars/');
define('UPLOAD_DOCUMENT_PATH', APP_ROOT . '/uploads/documents/');
define('UPLOAD_RESUME_PATH',   APP_ROOT . '/uploads/resumes/');

// ── Session names ────────────────────────────────────────────────
define('SESSION_USER',  'hrms_user');
define('SESSION_FLASH', 'hrms_flash');
define('CSRF_TOKEN_KEY','hrms_csrf');

// ── Leave Types ──────────────────────────────────────────────────
define('LEAVE_PENDING',  'pending');
define('LEAVE_APPROVED', 'approved');
define('LEAVE_REJECTED', 'rejected');
define('LEAVE_CANCELLED','cancelled');

// ── Attendance Statuses ──────────────────────────────────────────
define('ATT_PRESENT', 'present');
define('ATT_ABSENT',  'absent');
define('ATT_LATE',    'late');
define('ATT_HALF_DAY','half_day');
define('ATT_ON_LEAVE','on_leave');

// ── Payroll Statuses ─────────────────────────────────────────────
define('PAYROLL_DRAFT',     'draft');
define('PAYROLL_PROCESSING','processing');
define('PAYROLL_APPROVED',  'approved');
define('PAYROLL_PAID',      'paid');

// ── Applicant Statuses ───────────────────────────────────────────
define('APPLICANT_NEW',        'new');
define('APPLICANT_REVIEWING',  'reviewing');
define('APPLICANT_INTERVIEW',  'interview');
define('APPLICANT_OFFERED',    'offered');
define('APPLICANT_HIRED',      'hired');
define('APPLICANT_REJECTED',   'rejected');

// ── Performance Ratings ──────────────────────────────────────────
define('RATING_EXCELLENT',    5);
define('RATING_GOOD',         4);
define('RATING_SATISFACTORY', 3);
define('RATING_NEEDS_IMPROVE',2);
define('RATING_UNSATISFACTORY',1);

// ── Security Settings ────────────────────────────────────────────
define('LOGIN_MAX_ATTEMPTS',   5);          // Failed attempts before lockout
define('LOGIN_LOCKOUT_MINUTES', 15);        // Lockout duration in minutes
define('SESSION_TIMEOUT_SECONDS', 900);     // 15-minute session timeout
define('GEOLOCATION_API',  'http://ip-api.com/json/'); // Free IP geolocation API
define('APP_ENCRYPTION_KEY', 'xK9$mN2@pL5*vJ8!qZ3#cR7^tW4%bY6&'); // 32-char key for AES-256-CBC
