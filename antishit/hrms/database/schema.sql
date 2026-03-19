-- ============================================================
-- HRMS - Human Resource Management System
-- Complete Database Schema
-- Compatible: MySQL 8.0+ / MariaDB 10.4+ (XAMPP)
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+08:00';

-- DROP DATABASE IF EXISTS hrms_db;
-- CREATE DATABASE hrms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE hrms_db;

DROP VIEW IF EXISTS v_employees;
DROP VIEW IF EXISTS v_attendance_summary;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS documents;
DROP TABLE IF EXISTS training_enrollments;
DROP TABLE IF EXISTS trainings;
DROP TABLE IF EXISTS performance_kpi_scores;
DROP TABLE IF EXISTS performance_reviews;
DROP TABLE IF EXISTS kpis;
DROP TABLE IF EXISTS applicants;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS payroll;
DROP TABLE IF EXISTS payroll_periods;
DROP TABLE IF EXISTS salary_grades;
DROP TABLE IF EXISTS leaves;
DROP TABLE IF EXISTS leave_balances;
DROP TABLE IF EXISTS leave_types;
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS employees;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS positions;
DROP TABLE IF EXISTS departments;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS settings;

-- ============================================================
-- 1. ROLES & PERMISSIONS (RBAC)
-- ============================================================

CREATE TABLE roles (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    slug       VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE permissions (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    module      VARCHAR(100) NOT NULL,
    action      VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    UNIQUE KEY uq_perm (module, action)
) ENGINE=InnoDB;

CREATE TABLE role_permissions (
    role_id       INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id)       REFERENCES roles(id)       ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 2. DEPARTMENTS & POSITIONS
-- ============================================================

CREATE TABLE departments (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150) NOT NULL,
    code        VARCHAR(20)  NOT NULL UNIQUE,
    description TEXT,
    manager_id  INT UNSIGNED DEFAULT NULL,
    is_active   TINYINT(1) DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE positions (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(150) NOT NULL,
    department_id INT UNSIGNED NOT NULL,
    salary_min    DECIMAL(12,2) DEFAULT 0.00,
    salary_max    DECIMAL(12,2) DEFAULT 0.00,
    is_active     TINYINT(1) DEFAULT 1,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id)
) ENGINE=InnoDB;

-- ============================================================
-- 3. USERS
-- ============================================================

CREATE TABLE users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id         INT UNSIGNED NOT NULL,
    email           VARCHAR(191) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    first_name      VARCHAR(100) NOT NULL,
    last_name       VARCHAR(100) NOT NULL,
    avatar          VARCHAR(255) DEFAULT NULL,
    is_active       TINYINT(1) DEFAULT 1,
    last_login      TIMESTAMP   NULL DEFAULT NULL,
    password_reset_token  VARCHAR(100) DEFAULT NULL,
    password_reset_expiry TIMESTAMP NULL DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    INDEX idx_email (email),
    INDEX idx_role  (role_id)
) ENGINE=InnoDB;

-- ============================================================
-- 4. EMPLOYEES
-- ============================================================

CREATE TABLE employees (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id           INT UNSIGNED NOT NULL UNIQUE,
    employee_number   VARCHAR(30)  NOT NULL UNIQUE,
    department_id     INT UNSIGNED NOT NULL,
    position_id       INT UNSIGNED NOT NULL,
    manager_id        INT UNSIGNED DEFAULT NULL,
    employment_type   ENUM('full_time','part_time','contract','intern') DEFAULT 'full_time',
    status            ENUM('active','inactive','resigned','terminated','on_leave') DEFAULT 'active',
    date_hired        DATE NOT NULL,
    date_regularized  DATE DEFAULT NULL,
    date_separated    DATE DEFAULT NULL,
    basic_salary      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    phone             VARCHAR(30)  DEFAULT NULL,
    address           TEXT DEFAULT NULL,
    city              VARCHAR(100) DEFAULT NULL,
    birth_date        DATE DEFAULT NULL,
    gender            ENUM('male','female','prefer_not_to_say') DEFAULT NULL,
    civil_status      ENUM('single','married','widowed','divorced') DEFAULT NULL,
    sss_number        VARCHAR(30) DEFAULT NULL,
    philhealth_number VARCHAR(30) DEFAULT NULL,
    pagibig_number    VARCHAR(30) DEFAULT NULL,
    tin_number        VARCHAR(30) DEFAULT NULL,
    emergency_contact_name  VARCHAR(150) DEFAULT NULL,
    emergency_contact_phone VARCHAR(30)  DEFAULT NULL,
    notes             TEXT DEFAULT NULL,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)       REFERENCES users(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (position_id)   REFERENCES positions(id),
    FOREIGN KEY (manager_id)    REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_dept   (department_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============================================================
-- 5. ATTENDANCE
-- ============================================================

CREATE TABLE attendance (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id   INT UNSIGNED NOT NULL,
    date          DATE NOT NULL,
    clock_in      DATETIME DEFAULT NULL,
    clock_out     DATETIME DEFAULT NULL,
    hours_worked  DECIMAL(5,2) DEFAULT 0.00,
    overtime_hours DECIMAL(5,2) DEFAULT 0.00,
    status        ENUM('present','absent','late','half_day','on_leave') DEFAULT 'present',
    remarks       TEXT DEFAULT NULL,
    approved_by   INT UNSIGNED DEFAULT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_att_emp_date (employee_id, date),
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_att_date (date),
    INDEX idx_att_emp  (employee_id)
) ENGINE=InnoDB;

-- ============================================================
-- 6. LEAVE MANAGEMENT
-- ============================================================

CREATE TABLE leave_types (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,
    code            VARCHAR(20)  NOT NULL UNIQUE,
    days_allowed    INT DEFAULT 15,
    is_paid         TINYINT(1) DEFAULT 1,
    carry_forward   TINYINT(1) DEFAULT 0,
    description     TEXT,
    is_active       TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE leave_balances (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id   INT UNSIGNED NOT NULL,
    leave_type_id INT UNSIGNED NOT NULL,
    year          YEAR NOT NULL,
    allocated     DECIMAL(5,2) DEFAULT 0,
    used          DECIMAL(5,2) DEFAULT 0,
    remaining     DECIMAL(5,2) DEFAULT 0,
    UNIQUE KEY uq_lb (employee_id, leave_type_id, year),
    FOREIGN KEY (employee_id)   REFERENCES employees(id),
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id)
) ENGINE=InnoDB;

CREATE TABLE leaves (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    leave_type_id   INT UNSIGNED NOT NULL,
    start_date      DATE NOT NULL,
    end_date        DATE NOT NULL,
    days_requested  DECIMAL(5,2) NOT NULL DEFAULT 1,
    reason          TEXT NOT NULL,
    status          ENUM('pending','approved','rejected','cancelled') DEFAULT 'pending',
    reviewed_by     INT UNSIGNED DEFAULT NULL,
    reviewed_at     TIMESTAMP NULL DEFAULT NULL,
    remarks         TEXT DEFAULT NULL,
    attachment      VARCHAR(255) DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id)   REFERENCES employees(id),
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id),
    FOREIGN KEY (reviewed_by)   REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_leave_emp    (employee_id),
    INDEX idx_leave_status (status)
) ENGINE=InnoDB;

-- ============================================================
-- 7. PAYROLL
-- ============================================================

CREATE TABLE salary_grades (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    grade       VARCHAR(20) NOT NULL UNIQUE,
    basic_min   DECIMAL(12,2) NOT NULL,
    basic_max   DECIMAL(12,2) NOT NULL,
    description VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE payroll_periods (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    period_name VARCHAR(100) NOT NULL,
    start_date  DATE NOT NULL,
    end_date    DATE NOT NULL,
    pay_date    DATE NOT NULL,
    status      ENUM('draft','processing','approved','paid') DEFAULT 'draft',
    created_by  INT UNSIGNED NOT NULL,
    approved_by INT UNSIGNED DEFAULT NULL,
    approved_at TIMESTAMP NULL DEFAULT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by)  REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE payroll (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    period_id         INT UNSIGNED NOT NULL,
    employee_id       INT UNSIGNED NOT NULL,
    basic_salary      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    days_worked       DECIMAL(5,2)  NOT NULL DEFAULT 0.00,
    overtime_hours    DECIMAL(5,2)  DEFAULT 0.00,
    overtime_pay      DECIMAL(12,2) DEFAULT 0.00,
    gross_pay         DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    sss_deduction     DECIMAL(12,2) DEFAULT 0.00,
    philhealth_deduction DECIMAL(12,2) DEFAULT 0.00,
    pagibig_deduction DECIMAL(12,2) DEFAULT 0.00,
    tax_deduction     DECIMAL(12,2) DEFAULT 0.00,
    other_deductions  DECIMAL(12,2) DEFAULT 0.00,
    total_deductions  DECIMAL(12,2) DEFAULT 0.00,
    net_pay           DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    allowances        DECIMAL(12,2) DEFAULT 0.00,
    bonuses           DECIMAL(12,2) DEFAULT 0.00,
    status            ENUM('draft','approved','paid') DEFAULT 'draft',
    notes             TEXT DEFAULT NULL,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_payroll (period_id, employee_id),
    FOREIGN KEY (period_id)   REFERENCES payroll_periods(id),
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    INDEX idx_payroll_period (period_id),
    INDEX idx_payroll_emp    (employee_id)
) ENGINE=InnoDB;

-- ============================================================
-- 8. RECRUITMENT / ATS
-- ============================================================

CREATE TABLE jobs (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(200) NOT NULL,
    department_id   INT UNSIGNED NOT NULL,
    position_id     INT UNSIGNED DEFAULT NULL,
    description     TEXT,
    requirements    TEXT,
    salary_min      DECIMAL(12,2) DEFAULT NULL,
    salary_max      DECIMAL(12,2) DEFAULT NULL,
    employment_type ENUM('full_time','part_time','contract','intern') DEFAULT 'full_time',
    vacancies       INT DEFAULT 1,
    status          ENUM('open','closed','on_hold','filled') DEFAULT 'open',
    posted_by       INT UNSIGNED NOT NULL,
    deadline        DATE DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (position_id)   REFERENCES positions(id) ON DELETE SET NULL,
    FOREIGN KEY (posted_by)     REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE applicants (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_id          INT UNSIGNED NOT NULL,
    first_name      VARCHAR(100) NOT NULL,
    last_name       VARCHAR(100) NOT NULL,
    email           VARCHAR(191) NOT NULL,
    phone           VARCHAR(30)  DEFAULT NULL,
    resume          VARCHAR(255) DEFAULT NULL,
    cover_letter    TEXT DEFAULT NULL,
    status          ENUM('new','reviewing','interview','offered','hired','rejected') DEFAULT 'new',
    interviewed_by  INT UNSIGNED DEFAULT NULL,
    interview_date  DATETIME DEFAULT NULL,
    interview_notes TEXT DEFAULT NULL,
    source          VARCHAR(100) DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id)        REFERENCES jobs(id),
    FOREIGN KEY (interviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_app_job    (job_id),
    INDEX idx_app_status (status)
) ENGINE=InnoDB;

-- ============================================================
-- 9. PERFORMANCE MANAGEMENT
-- ============================================================

CREATE TABLE kpis (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(200) NOT NULL,
    description TEXT,
    department_id INT UNSIGNED DEFAULT NULL,
    weight      DECIMAL(5,2) DEFAULT 1.00,
    is_active   TINYINT(1) DEFAULT 1,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE performance_reviews (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id    INT UNSIGNED NOT NULL,
    reviewer_id    INT UNSIGNED NOT NULL,
    review_period  VARCHAR(50) NOT NULL,  -- e.g., "Q1 2025"
    review_date    DATE NOT NULL,
    overall_rating DECIMAL(3,1) DEFAULT NULL,
    strengths      TEXT DEFAULT NULL,
    improvements   TEXT DEFAULT NULL,
    goals_next_period TEXT DEFAULT NULL,
    status         ENUM('draft','submitted','acknowledged') DEFAULT 'draft',
    employee_ack   TINYINT(1) DEFAULT 0,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (reviewer_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE performance_kpi_scores (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    review_id  INT UNSIGNED NOT NULL,
    kpi_id     INT UNSIGNED NOT NULL,
    score      DECIMAL(3,1) NOT NULL,
    comments   TEXT DEFAULT NULL,
    FOREIGN KEY (review_id) REFERENCES performance_reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (kpi_id)    REFERENCES kpis(id)
) ENGINE=InnoDB;

-- ============================================================
-- 10. TRAINING MANAGEMENT
-- ============================================================

CREATE TABLE trainings (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(200) NOT NULL,
    description TEXT,
    trainer     VARCHAR(150) DEFAULT NULL,
    start_date  DATE NOT NULL,
    end_date    DATE NOT NULL,
    location    VARCHAR(200) DEFAULT NULL,
    max_participants INT DEFAULT NULL,
    cost        DECIMAL(12,2) DEFAULT 0.00,
    status      ENUM('scheduled','ongoing','completed','cancelled') DEFAULT 'scheduled',
    created_by  INT UNSIGNED NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE training_enrollments (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    training_id INT UNSIGNED NOT NULL,
    employee_id INT UNSIGNED NOT NULL,
    status      ENUM('enrolled','completed','absent','cancelled') DEFAULT 'enrolled',
    score       DECIMAL(5,2) DEFAULT NULL,
    certificate VARCHAR(255) DEFAULT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_enroll (training_id, employee_id),
    FOREIGN KEY (training_id) REFERENCES trainings(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id)
) ENGINE=InnoDB;

-- ============================================================
-- 11. DOCUMENT MANAGEMENT
-- ============================================================

CREATE TABLE documents (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id  INT UNSIGNED DEFAULT NULL,
    title        VARCHAR(200) NOT NULL,
    category     ENUM('contract','id','certificate','policy','other') DEFAULT 'other',
    filename     VARCHAR(255) NOT NULL,
    file_type    VARCHAR(50)  DEFAULT NULL,
    file_size    INT UNSIGNED DEFAULT NULL,
    uploaded_by  INT UNSIGNED NOT NULL,
    is_public    TINYINT(1) DEFAULT 0,  -- 0=private(employee-only), 1=company-wide
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id),
    INDEX idx_doc_emp (employee_id)
) ENGINE=InnoDB;

-- ============================================================
-- 12. NOTIFICATIONS
-- ============================================================

CREATE TABLE notifications (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    title       VARCHAR(255) NOT NULL,
    message     TEXT NOT NULL,
    type        ENUM('info','success','warning','danger') DEFAULT 'info',
    module      VARCHAR(100) DEFAULT NULL,
    module_id   INT UNSIGNED DEFAULT NULL,
    is_read     TINYINT(1) DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notif_user   (user_id),
    INDEX idx_notif_unread (user_id, is_read)
) ENGINE=InnoDB;

-- ============================================================
-- 13. AUDIT LOGS
-- ============================================================

CREATE TABLE audit_logs (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED DEFAULT NULL,
    action      VARCHAR(100) NOT NULL,
    module      VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    target_id   INT UNSIGNED DEFAULT NULL,
    ip_address  VARCHAR(45)  DEFAULT NULL,
    user_agent  TEXT DEFAULT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_audit_user   (user_id),
    INDEX idx_audit_module (module),
    INDEX idx_audit_date   (created_at)
) ENGINE=InnoDB;

-- ============================================================
-- 14. SYSTEM SETTINGS
-- ============================================================

CREATE TABLE settings (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key_name    VARCHAR(150) NOT NULL UNIQUE,
    value       TEXT DEFAULT NULL,
    label       VARCHAR(200) DEFAULT NULL,
    group_name  VARCHAR(100) DEFAULT 'general',
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- VIEWS (helpers)
-- ============================================================

CREATE OR REPLACE VIEW v_employees AS
SELECT
    e.id, e.employee_number, e.employment_type, e.status, e.date_hired, e.basic_salary,
    e.phone, e.birth_date, e.gender, e.department_id, e.position_id, e.manager_id,
    u.first_name, u.last_name, CONCAT(u.first_name,' ',u.last_name) AS full_name,
    u.email, u.avatar, u.is_active AS user_active,
    d.name AS department_name, d.code AS department_code,
    p.title AS position_title,
    r.name AS role_name, r.slug AS role_slug, u.role_id
FROM employees e
JOIN users u       ON e.user_id       = u.id
JOIN departments d ON e.department_id = d.id
JOIN positions p   ON e.position_id   = p.id
JOIN roles r       ON u.role_id       = r.id;

CREATE OR REPLACE VIEW v_attendance_summary AS
SELECT
    a.employee_id,
    CONCAT(u.first_name,' ',u.last_name) AS full_name,
    d.name AS department_name,
    YEAR(a.date) AS yr, MONTH(a.date) AS mth,
    COUNT(*) AS total_days,
    SUM(a.status = 'present')  AS present,
    SUM(a.status = 'absent')   AS absent,
    SUM(a.status = 'late')     AS late,
    SUM(a.status = 'on_leave') AS on_leave,
    SUM(a.hours_worked) AS total_hours
FROM attendance a
JOIN employees e  ON a.employee_id = e.id
JOIN users u      ON e.user_id     = u.id
JOIN departments d ON e.department_id = d.id
GROUP BY a.employee_id, yr, mth;

SET FOREIGN_KEY_CHECKS = 1;
