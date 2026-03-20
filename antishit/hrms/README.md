# HRMS - Human Resource Management System

A comprehensive, production-ready, web-based Human Resource Management System built with PHP and MySQL. This system provides a robust platform for managing employee lifecycle, attendance, payroll, and more, with a strong focus on security and data privacy.

## 🚀 Core Modules & Features

### 📊 Dashboard
- Real-time overview of HR metrics.
- Quick stats for employees, attendance, and leave requests.

### 👥 Employee Management
- Complete employee profile management.
- Secure document storage for each employee.
- Avatar/Profile picture uploads.
- Role and department assignment.

### 📅 Attendance Tracking
- Real-time Clock-in/Clock-out system.
- Attendance logs with status badges (Present, Late, Absent, Half Day).
- Monthly attendance reports.

### 💰 Payroll Management
- Automated salary calculations.
- Payslip generation with currency formatting (PHP/₱).
- Detailed breakdown of earnings and deductions.

### 🏖️ Leave Management
- Online leave application process.
- Multi-level approval workflow (Pending, Approved, Rejected).
- Tracking of leave history and balances.

### 🎯 Recruitment & Talent Acquisition
- Job posting and applicant tracking.
- Status management for candidates (Interview, Offered, Hired, etc.).

### 📈 Performance & Training
- Employee performance evaluations.
- Training program management and progress tracking.

---

## 🔒 Security Implementations

This system implements enterprise-grade security measures to protect sensitive HR data and prevent unauthorized access.

### 1. Authentication & Session Security
- **Secure Authentication**: Password hashing using industry-standard `bcrypt` algorithm.
- **Two-Factor Authentication (2FA)**: Mandatory OTP verification via email for added login security.
- **Trusted Device Management**: Secure device trust mechanism (10-day validity) using cryptographically secure tokens.
- **Session Fixation Protection**: Automatic session ID regeneration upon successful login.
- **Session Timeout**: Automatic session invalidation after 15 minutes of inactivity.
- **Immediate Invalidation**: Sessions are immediately invalidated if the user's password is changed on another device.
- **Session Device Management**: Users can view all active login sessions (IP, Device, Location) and remotely revoke (log out) any session from their profile.
- **Dual-Device Limit**: Strict enforcement of a maximum of **2 simultaneous active devices** per user. Automatically logs out the oldest session when a 3rd device is added.
- **Cross-Tab Logout Synchronization**: Real-time logout detection across all open browser tabs using `localStorage` broadcasting. Logging out in one tab instantly redirects all other tabs to the login page.

### 2. Access Control (RBAC)
- **Granular Permissions**: Role-Based Access Control allowing specific permissions (View, Create, Edit, Delete) for each module.
- **Super Admin Bypass**: Full system access for administrative roles.

### 3. Attack Prevention
- **CSRF Protection**: Token-based validation for all state-changing requests (Forms and AJAX).
- **XSS Mitigation**: Global output escaping and input sanitization to prevent Cross-Site Scripting.
- **Account Lockout**: 15-minute temporary lockout after 5 consecutive failed login attempts to prevent brute-force attacks.
- **Rate Limiting**: Throttling of authentication attempts.

### 4. Data Privacy & Integrity
- **PII Encryption**: Sensitive employee data (Personal Identifiable Information) is encrypted at rest using **AES-256-CBC**.
- **Password Policy**: Strict enforcement of complexity rules (Minimum 8 chars, Uppercase, Lowercase, Numbers, and Special characters).
- **Breached Password Check**: Real-time integration with the *Have I Been Pwned* API to prevent the use of compromised passwords.

### 5. Monitoring & Auditing
- **Comprehensive Audit Logs**: Every significant action is tracked with details on the performer, module, action description, IP address, and User-Agent.
- **IP & Geolocation Tracking**: Real visitor IP detection (proxy-aware) with geolocation logging (Country, City, ISP) for every login.
- **Login Notifications**: Instant email alerts when a login occurs from a new or unrecognized IP address.

---

## 🛠️ Technical Stack
- **Backend**: PHP 8.x
- **Database**: MySQL / MariaDB
- **Mailing**: PHPMailer with SMTP integration
- **APIs**: Have I Been Pwned (Security), ip-api.com (Geolocation)
- **Frontend**: Vanilla CSS, JavaScript, Bootstrap (for layout)
