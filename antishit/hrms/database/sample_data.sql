-- ============================================================
-- HRMS Sample Data
-- Run AFTER schema.sql
-- ============================================================
USE hrms_db;

SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM performance_kpi_scores;
DELETE FROM performance_reviews;
DELETE FROM payroll;
DELETE FROM payroll_periods;
DELETE FROM salary_grades;
DELETE FROM training_enrollments;
DELETE FROM trainings;
DELETE FROM applicants;
DELETE FROM jobs;
DELETE FROM attendance;
DELETE FROM leaves;
DELETE FROM leave_balances;
DELETE FROM leave_types;
DELETE FROM documents;
DELETE FROM audit_logs;
DELETE FROM notifications;
DELETE FROM settings;
DELETE FROM kpis;
DELETE FROM employees;
DELETE FROM users;
DELETE FROM positions;
DELETE FROM departments;
DELETE FROM role_permissions;
DELETE FROM permissions;
DELETE FROM roles;

ALTER TABLE roles AUTO_INCREMENT = 1;
ALTER TABLE permissions AUTO_INCREMENT = 1;
ALTER TABLE departments AUTO_INCREMENT = 1;
ALTER TABLE positions AUTO_INCREMENT = 1;
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE employees AUTO_INCREMENT = 1;
ALTER TABLE leave_types AUTO_INCREMENT = 1;
ALTER TABLE jobs AUTO_INCREMENT = 1;
ALTER TABLE trainings AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- ── Roles ────────────────────────────────────────────────────
INSERT INTO roles (name, slug, description) VALUES
('Super Admin',         'super_admin',         'Full system control'),
('HR Director',         'hr_director',         'Full HR lifecycle management'),
('Department Manager',  'department_manager',   'Manage department employees'),
('Finance Manager',     'finance_manager',      'Payroll and financial operations'),
('HR Specialist',       'hr_specialist',        'Employee records and attendance'),
('Recruitment Officer', 'recruitment_officer',  'Recruitment and onboarding'),
('Employee',            'employee',             'Self-service access');

-- ── Permissions ───────────────────────────────────────────────
INSERT INTO permissions (module, action, description) VALUES
-- Dashboard
('dashboard','view','View dashboard'),
-- Employees
('employees','view','View employees'),('employees','create','Add employee'),
('employees','edit','Edit employee'),('employees','delete','Delete employee'),
-- Attendance
('attendance','view','View attendance'),('attendance','manage','Manage attendance'),
-- Leaves
('leaves','view','View leaves'),('leaves','request','Request leave'),
('leaves','approve','Approve/reject leaves'),
-- Payroll
('payroll','view','View payroll'),('payroll','generate','Generate payroll'),
('payroll','approve','Approve payroll'),
-- Recruitment
('recruitment','view','View jobs & applicants'),('recruitment','manage','Manage recruitment'),
-- Performance
('performance','view','View reviews'),('performance','manage','Manage reviews'),
-- Training
('training','view','View trainings'),('training','manage','Manage trainings'),
-- Documents
('documents','view','View documents'),('documents','upload','Upload documents'),
('documents','delete','Delete documents'),
-- Reports
('reports','view','View reports'),('reports','export','Export reports'),
-- Notifications
('notifications','view','View notifications'),
-- Audit
('audit','view','View audit logs'),
-- Settings
('settings','view','View settings'),('settings','manage','Manage settings');

-- ── Role Permissions ──────────────────────────────────────────
-- HR Director (id=2): all except settings manage
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, id FROM permissions WHERE module NOT IN ('settings','audit') OR action = 'view';

-- Department Manager (id=3)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, id FROM permissions WHERE (module='dashboard' AND action='view')
   OR (module='employees' AND action IN ('view','edit'))
   OR (module='attendance')
   OR (module='leaves' AND action IN ('view','approve'))
   OR (module='performance')
   OR (module='training' AND action='view')
   OR (module='documents' AND action='view')
   OR (module='reports' AND action='view')
   OR (module='notifications' AND action='view');

-- Finance Manager (id=4)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 4, id FROM permissions WHERE (module='dashboard' AND action='view')
   OR (module='employees' AND action='view')
   OR (module='payroll')
   OR (module='reports')
   OR (module='notifications' AND action='view');

-- HR Specialist (id=5)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 5, id FROM permissions WHERE (module='dashboard' AND action='view')
   OR (module='employees' AND action IN ('view','create','edit'))
   OR (module='attendance')
   OR (module='leaves' AND action IN ('view','approve'))
   OR (module='documents')
   OR (module='reports' AND action='view')
   OR (module='notifications' AND action='view');

-- Recruitment Officer (id=6)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 6, id FROM permissions WHERE (module='dashboard' AND action='view')
   OR (module='employees' AND action='view')
   OR (module='recruitment')
   OR (module='documents' AND action IN ('view','upload'))
   OR (module='notifications' AND action='view');

-- Employee (id=7)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 7, id FROM permissions WHERE (module='dashboard' AND action='view')
   OR (module='attendance' AND action='view')
   OR (module='leaves' AND action IN ('view','request'))
   OR (module='payroll' AND action='view')
   OR (module='documents' AND action IN ('view','upload'))
   OR (module='performance' AND action='view')
   OR (module='training' AND action='view')
   OR (module='notifications' AND action='view');

-- ── Departments ───────────────────────────────────────────────
INSERT INTO departments (name, code, description) VALUES
('Human Resources',   'HR',  'People management and culture'),
('Finance',           'FIN', 'Financial operations'),
('Information Technology','IT','Software and infrastructure'),
('Operations',        'OPS', 'Day-to-day business operations'),
('Sales & Marketing', 'MKT', 'Revenue generation'),
('Administration',    'ADM', 'General administration');

-- ── Positions ─────────────────────────────────────────────────
INSERT INTO positions (title, department_id, salary_min, salary_max) VALUES
('HR Director',             1, 80000, 120000),
('HR Specialist',           1, 35000, 55000),
('Recruitment Officer',     1, 30000, 50000),
('Finance Manager',         2, 70000, 100000),
('Accountant',              2, 30000, 50000),
('IT Manager',              3, 75000, 110000),
('Software Developer',      3, 40000, 80000),
('System Administrator',    3, 35000, 60000),
('Operations Manager',      4, 60000, 90000),
('Operations Staff',        4, 20000, 35000),
('Sales Manager',           5, 55000, 85000),
('Sales Representative',    5, 25000, 40000),
('Admin Officer',           6, 20000, 30000);

-- ── Users (password: Admin@1234 → bcrypt hash) ───────────────
-- Hash for 'Admin@1234': $2y$12$...
INSERT INTO users (role_id, email, password_hash, first_name, last_name) VALUES
(1, 'superadmin@hrms.com',    '$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942', 'System',    'Administrator'),
(2, 'hrdirector@hrms.com',    '$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942', 'Maria',     'Santos'),
(3, 'manager.it@hrms.com',    '$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942', 'Jose',      'Reyes'),
(4, 'finance@hrms.com',       '$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942', 'Ana',       'Cruz'),
(5, 'hrspecialist@hrms.com',  '$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942', 'Lorna',     'Garcia'),
(6, 'recruiter@hrms.com',     '$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942', 'Ryan',      'Torres'),
(7, 'employee@hrms.com',      '$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942', 'Juan',      'Dela Cruz'),
(7, 'employee2@hrms.com',     '$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942', 'Rosa',      'Mendoza'),
(7, 'employee3@hrms.com',     '$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942', 'Carlo',     'Buenaventura'),
(7, 'employee4@hrms.com',     '$2y$10$ss9KVAOaiWb3HPID03udb.lYQ36mXg0PqCLioVyReJtuwamvd5942', 'Patricia',  'Villanueva');

-- ── Employees ─────────────────────────────────────────────────
INSERT INTO employees (user_id, employee_number, department_id, position_id, date_hired, basic_salary, status, employment_type, gender, birth_date, phone, sss_number, philhealth_number, pagibig_number, tin_number) VALUES
(1, 'EMP-0001', 6, 13, '2020-01-01', 50000,  'active', 'full_time', 'male',   '1985-05-10', '09171234567', '01-2345678-9', '12-345678901-2', '1234-5678-9012', '123-456-789-000'),
(2, 'EMP-0002', 1, 1,  '2020-01-15', 95000,  'active', 'full_time', 'female', '1982-03-22', '09181234567', '01-3456789-0', '12-456789012-3', '2345-6789-0123', '234-567-890-000'),
(3, 'EMP-0003', 3, 6,  '2020-02-01', 88000,  'active', 'full_time', 'male',   '1980-07-15', '09191234567', '01-4567890-1', '12-567890123-4', '3456-7890-1234', '345-678-901-000'),
(4, 'EMP-0004', 2, 4,  '2020-03-01', 82000,  'active', 'full_time', 'female', '1983-11-30', '09201234567', '01-5678901-2', '12-678901234-5', '4567-8901-2345', '456-789-012-000'),
(5, 'EMP-0005', 1, 2,  '2021-01-10', 45000,  'active', 'full_time', 'female', '1990-06-18', '09211234567', '01-6789012-3', '12-789012345-6', '5678-9012-3456', '567-890-123-000'),
(6, 'EMP-0006', 1, 3,  '2021-06-01', 38000,  'active', 'full_time', 'male',   '1993-09-25', '09221234567', '01-7890123-4', '12-890123456-7', '6789-0123-4567', '678-901-234-000'),
(7, 'EMP-0007', 3, 7,  '2022-03-15', 55000,  'active', 'full_time', 'male',   '1995-01-10', '09231234567', '01-8901234-5', '12-901234567-8', '7890-1234-5678', '789-012-345-000'),
(8, 'EMP-0008', 5, 12, '2022-07-01', 32000,  'active', 'full_time', 'female', '1997-08-14', '09241234567', '01-9012345-6', '12-012345678-9', '8901-2345-6789', '890-123-456-000'),
(9, 'EMP-0009', 4, 10, '2023-01-01', 28000,  'active', 'full_time', 'male',   '1999-03-05', '09251234567', '01-0123456-7', '12-123456789-0', '9012-3456-7890', '901-234-567-000'),
(10,'EMP-0010', 5, 12, '2023-06-01', 30000,  'active', 'full_time', 'female', '1998-12-20', '09261234567', '01-1234567-8', '12-234567890-1', '0123-4567-8901', '012-345-678-000');

-- Update department managers
UPDATE departments SET manager_id = 3 WHERE id = 3;  -- IT
UPDATE departments SET manager_id = 2 WHERE id = 1;  -- HR

-- ── Leave Types ───────────────────────────────────────────────
INSERT INTO leave_types (name, code, days_allowed, is_paid, carry_forward, description) VALUES
('Vacation Leave',    'VL', 15, 1, 1, 'Annual vacation leave'),
('Sick Leave',        'SL', 15, 1, 0, 'Medical/health leave'),
('Maternity Leave',   'ML', 105,1, 0, 'For female employees'),
('Paternity Leave',   'PL', 7,  1, 0, 'For male employees upon birth of child'),
('Emergency Leave',   'EL', 3,  1, 0, 'Family emergencies'),
('Unpaid Leave',      'UL', 30, 0, 0, 'Leave without pay');

-- ── Leave Balances (Year 2026) ────────────────────────────────
INSERT INTO leave_balances (employee_id, leave_type_id, year, allocated, used, remaining)
SELECT e.id, lt.id, 2026, lt.days_allowed, 0, lt.days_allowed
FROM employees e CROSS JOIN leave_types lt WHERE lt.id IN (1,2,5,6);

-- ── Sample Leaves ─────────────────────────────────────────────
INSERT INTO leaves (employee_id, leave_type_id, start_date, end_date, days_requested, reason, status) VALUES
(7, 1, '2026-03-10', '2026-03-12', 3, 'Family vacation trip', 'approved'),
(8, 2, '2026-03-05', '2026-03-05', 1, 'Doctor appointment',   'approved'),
(9, 1, '2026-03-20', '2026-03-21', 2, 'Personal matters',     'pending'),
(10,2, '2026-03-18', '2026-03-18', 1, 'Not feeling well',     'pending');

-- ── Sample Attendance (last 7 days) ──────────────────────────
INSERT INTO attendance (employee_id, date, clock_in, clock_out, hours_worked, status) VALUES
(7, DATE_SUB(CURDATE(),INTERVAL 6 DAY), CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 DAY),' 08:02:00'), CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 DAY),' 17:05:00'), 8.98, 'present'),
(7, DATE_SUB(CURDATE(),INTERVAL 5 DAY), CONCAT(DATE_SUB(CURDATE(),INTERVAL 5 DAY),' 08:15:00'), CONCAT(DATE_SUB(CURDATE(),INTERVAL 5 DAY),' 17:00:00'), 8.75, 'late'),
(7, DATE_SUB(CURDATE(),INTERVAL 4 DAY), CONCAT(DATE_SUB(CURDATE(),INTERVAL 4 DAY),' 08:01:00'), CONCAT(DATE_SUB(CURDATE(),INTERVAL 4 DAY),' 17:05:00'), 9.07, 'present'),
(8, DATE_SUB(CURDATE(),INTERVAL 6 DAY), CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 DAY),' 08:00:00'), CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 DAY),' 17:00:00'), 9.00, 'present'),
(8, DATE_SUB(CURDATE(),INTERVAL 5 DAY), CONCAT(DATE_SUB(CURDATE(),INTERVAL 5 DAY),' 08:00:00'), CONCAT(DATE_SUB(CURDATE(),INTERVAL 5 DAY),' 17:00:00'), 9.00, 'present'),
(9, DATE_SUB(CURDATE(),INTERVAL 6 DAY), CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 DAY),' 09:15:00'), CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 DAY),' 17:00:00'), 7.75, 'late'),
(10,DATE_SUB(CURDATE(),INTERVAL 6 DAY), CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 DAY),' 08:05:00'), CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 DAY),' 17:00:00'), 8.92, 'present');

-- ── Jobs (Recruitment) ────────────────────────────────────────
INSERT INTO jobs (title, department_id, position_id, description, requirements, employment_type, vacancies, status, posted_by, deadline) VALUES
('Software Developer', 3, 7, 'Looking for a skilled PHP/Laravel developer.', '3+ years PHP, MySQL, REST APIs', 'full_time', 2, 'open', 6, DATE_ADD(CURDATE(), INTERVAL 30 DAY)),
('Sales Representative', 5, 12, 'Dynamic sales rep needed.', '1+ year sales experience', 'full_time', 3, 'open', 6, DATE_ADD(CURDATE(), INTERVAL 45 DAY)),
('HR Specialist', 1, 2, 'HR Specialist to support recruitment and engagement.', 'HR background, 2 years exp', 'full_time', 1, 'open', 6, DATE_ADD(CURDATE(), INTERVAL 20 DAY));

-- ── Applicants ────────────────────────────────────────────────
INSERT INTO applicants (job_id, first_name, last_name, email, phone, status, source) VALUES
(1, 'Marco',   'Tan',     'marco.tan@email.com',     '09301234567', 'interview',  'LinkedIn'),
(1, 'Linda',   'Wong',    'linda.wong@email.com',    '09311234567', 'reviewing',  'JobStreet'),
(2, 'Bernard', 'Castro',  'bernard.c@email.com',     '09321234567', 'new',        'Referral'),
(3, 'Grace',   'Navarro', 'grace.n@email.com',       '09331234567', 'reviewing',  'Indeed');

-- ── Trainings ────────────────────────────────────────────────
INSERT INTO trainings (title, description, trainer, start_date, end_date, location, max_participants, status, created_by) VALUES
('Data Privacy Act Compliance', 'Annual DPA training for all employees', 'Legal Team', CURDATE(), DATE_ADD(CURDATE(),INTERVAL 1 DAY), 'Conference Room A', 30, 'scheduled', 2),
('Leadership Development Program', 'Management skills enhancement', 'HR Consulting Group', DATE_ADD(CURDATE(),INTERVAL 7 DAY), DATE_ADD(CURDATE(),INTERVAL 9 DAY), 'Training Center', 15, 'scheduled', 2);

INSERT INTO training_enrollments (training_id, employee_id, status)
SELECT 1, e.id, 'enrolled' FROM employees e WHERE e.id IN (3,4,5,6,7,8);

-- ── Notifications ─────────────────────────────────────────────
INSERT INTO notifications (user_id, title, message, type, module) VALUES
(7, 'Leave Approved', 'Your vacation leave (Mar 10-12) has been approved.', 'success', 'leaves'),
(9, 'Leave Pending', 'Your leave request is under review.', 'info', 'leaves'),
(2, 'New Leave Request', 'Employee Juan Dela Cruz submitted a leave request.', 'info', 'leaves');

-- ── Settings ──────────────────────────────────────────────────
INSERT INTO settings (key_name, value, label, group_name) VALUES
('company_name',      'NexaHR Corporation',   'Company Name',         'general'),
('company_address',   '123 Business Ave, Makati City, Philippines', 'Company Address', 'general'),
('company_phone',     '+63 2 8888 0000',         'Company Phone',        'general'),
('company_email',     'info@hrmspro.com',         'Company Email',        'general'),
('work_start_time',   '08:00',                    'Work Start Time',      'attendance'),
('work_end_time',     '17:00',                    'Work End Time',        'attendance'),
('late_threshold_min','15',                        'Late Threshold (mins)','attendance'),
('overtime_rate',     '1.25',                      'Overtime Rate',        'payroll'),
('sss_employee_rate', '0.045',                     'SSS Employee Rate',    'payroll'),
('philhealth_rate',   '0.05',                      'PhilHealth Rate',      'payroll'),
('pagibig_rate',      '0.02',                      'Pag-IBIG Rate',        'payroll'),
('currency_symbol',   '₱',                         'Currency Symbol',      'general'),
('date_format',       'M d, Y',                    'Date Format',          'general');

-- ── KPIs ─────────────────────────────────────────────────────
INSERT INTO kpis (name, description, weight) VALUES
('Quality of Work',       'Accuracy and quality of output',           1.5),
('Productivity',          'Volume and efficiency of work completed',  1.5),
('Communication Skills',  'Clarity and effectiveness of communication',1.0),
('Teamwork',              'Collaboration and team support',           1.0),
('Initiative',            'Proactively taking action beyond duties',  1.0),
('Attendance & Punctuality','Consistent attendance and punctuality',  1.0);
