<?php
/**
 * Authentication Controller
 * + Account Lockout / Rate Limiting
 * + Password Policy Enforcement
 * + IP / Geolocation / Device Login Logging
 */
class AuthController {

    public function index(): void {
        redirect('index.php?module=auth&action=login');
    }

    public function login(): void {
        if (isLoggedIn()) {
            redirect('index.php?module=dashboard&action=index');
        }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $email    = sanitizeInput(post('email'));
            $password = post('password');

            if (empty($email) || empty($password)) {
                $errors['general'] = 'Email and password are required.';
            } else {
                $userModel = new User();
                $user = $userModel->findByEmail($email);

                // ── Account Lockout Check ──────────────────────────────────
                if ($user && !empty($user['lockout_until'])) {
                    $lockoutUntil = strtotime($user['lockout_until']);
                    if ($lockoutUntil && time() < $lockoutUntil) {
                        $remaining = ceil(($lockoutUntil - time()) / 60);
                        $errors['general'] = "Account is temporarily locked. Try again in {$remaining} minute(s).";
                        auditLog('login_lockout', 'auth', "Login blocked — account locked for: $email");
                        include APP_ROOT . '/views/auth/login.php';
                        return;
                    }
                }

                if ($user && $user['is_active'] && password_verify($password, $user['password_hash'])) {
                    // Gather IP / Device / Geo before building session
                    $ip       = getUserIP();
                    $geo      = getGeolocation($ip);
                    $device   = getDevice();
                    $isNewIp  = $userModel->checkIfNewIp($user['id'], $ip);
                    $isSuspicious = !$isNewIp
                        ? false
                        : $userModel->checkIfNewLocation($user['id'], $geo['country'], $geo['city']);

                    // Load permissions
                    $perms = loadUserPermissions($user['id'], $user['role_slug']);

                    // ── 2-Step Verification Check ─────────────────────────
                    $trustedToken = $_COOKIE['trusted_device'] ?? '';
                    if ($userModel->isValidTrustedDevice($user['id'], $trustedToken)) {
                        // Device is trusted, skip 2FA
                        $this->completeLogin($user, $userModel, $perms, $ip, $geo, $device, $isNewIp, $isSuspicious);
                    } else {
                        // Generate and send 2FA code
                        $code = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
                        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                        $userModel->setTwoFactorCode($user['id'], $code, $expiresAt);
                        
                        require_once APP_ROOT . '/includes/mailer.php';
                        if (send2FACode($user['email'], $user['first_name'], $code)) {
                            $_SESSION['pending_2fa_user_id'] = $user['id'];
                            $_SESSION['pending_2fa_data'] = [
                                'perms' => $perms, 'ip' => $ip, 'geo' => $geo, 
                                'device' => $device, 'is_new_ip' => $isNewIp, 'is_suspicious' => $isSuspicious
                            ];
                            // Re-render login.php but it will now detect the pending 2FA
                            include APP_ROOT . '/views/auth/login.php';
                            return;
                        } else {
                            $errors['general'] = 'Failed to send verification email. Please try again.';
                        }
                    }
                } else {
                    // ── Failed Login / Rate Limiting ───────────────────────
                    if ($user) {
                        $userModel->incrementFailedAttempts($user['id']);
                        // Re-fetch to get fresh count
                        $fresh = $userModel->findByEmail($email);
                        $attempts = (int)($fresh['failed_attempts'] ?? 0);
                        $maxAttempts = defined('LOGIN_MAX_ATTEMPTS') ? LOGIN_MAX_ATTEMPTS : 5;

                        if ($attempts >= $maxAttempts) {
                            $minutes = defined('LOGIN_LOCKOUT_MINUTES') ? LOGIN_LOCKOUT_MINUTES : 15;
                            $userModel->lockoutUser($user['id'], $minutes);
                            $errors['general'] = "Too many failed attempts. Account locked for {$minutes} minutes.";
                            auditLog('login_locked', 'auth', "Account locked after {$attempts} attempts: $email");
                        } else {
                            $remaining = $maxAttempts - $attempts;
                            $errors['general'] = "Invalid credentials. {$remaining} attempt(s) remaining before lockout.";
                        }
                    } else {
                        $errors['general'] = 'Invalid credentials or account is disabled.';
                    }
                    auditLog('login_fail', 'auth', "Failed login attempt for: $email");
                }
            }
        }
        include APP_ROOT . '/views/auth/login.php';
    }

    public function verify2fa(): void {
        if (!isset($_SESSION['pending_2fa_user_id'])) {
            redirect('index.php?module=auth&action=login');
        }

        $userModel = new User();
        $user = $userModel->findById($_SESSION['pending_2fa_user_id']);
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $code = post('code');
            
            if ($userModel->isValidTwoFactorCode($user['id'], $code)) {
                $userModel->clearTwoFactorCode($user['id']);
                
                // Handle "Trust this device"
                if (post('trust_device') === '1') {
                    $token = bin2hex(random_bytes(32));
                    $expiresAt = date('Y-m-d H:i:s', strtotime('+10 days'));
                    $userModel->trustDevice($user['id'], $token, $expiresAt);
                    setcookie('trusted_device', $token, time() + (10 * 24 * 60 * 60), '/', '', false, true);
                }

                $data = $_SESSION['pending_2fa_data'];
                unset($_SESSION['pending_2fa_user_id'], $_SESSION['pending_2fa_data']);
                
                $this->completeLogin($user, $userModel, $data['perms'], $data['ip'], $data['geo'], $data['device'], $data['is_new_ip'], $data['is_suspicious']);
            } else {
                $errors['general'] = 'Invalid or expired verification code.';
                auditLog('2fa_failed', 'auth', "Incorrect 2FA code entered for: {$user['email']}");
            }
        }

        // Use login.php view even for verification, just in case they land here directly
        include APP_ROOT . '/views/auth/login.php';
    }

    private function completeLogin($user, $userModel, $perms, $ip, $geo, $device, $isNewIp, $isSuspicious): void {
        $userModel->resetFailedAttempts($user['id']);
        regenerateSession();
        $sessionToken = bin2hex(random_bytes(32));
        $userModel->updateSessionToken($user['id'], $sessionToken);

        $_SESSION['last_action']  = time();
        $_SESSION[SESSION_USER]   = [
            'id'          => $user['id'],
            'employee_id' => $user['employee_id'],
            'email'       => $user['email'],
            'first_name'  => $user['first_name'],
            'last_name'   => $user['last_name'],
            'full_name'   => $user['first_name'] . ' ' . $user['last_name'],
            'avatar'      => $user['avatar'],
            'role'        => $user['role_slug'],
            'role_name'   => $user['role_name'],
            'role_id'     => $user['role_id'],
            'permissions' => $perms,
            'session_token'=> $sessionToken,
        ];

        $userModel->updateLastLogin($user['id']);
        $logData = [
            'user_id'      => $user['id'],
            'ip_address'   => $ip,
            'country'      => $geo['country'],
            'city'         => $geo['city'],
            'latitude'     => $geo['latitude'],
            'longitude'    => $geo['longitude'],
            'isp'          => $geo['isp'],
            'device'       => $device,
            'is_new_ip'    => $isNewIp ? 1 : 0,
            'is_suspicious'=> $isSuspicious ? 1 : 0,
        ];
        $userModel->recordLoginLog($logData);
        
        // Record persistent session in database (Limit to 2 devices)
        $sessionModel = new UserSession();
        $sessionModel->enforceLimit($user['id'], 2); // Auto-logout oldest if 3rd device logs in
        $sessionModel->create([
            'user_id'    => $user['id'],
            'session_id' => session_id(),
            'ip_address' => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'device'     => $device,
            'location'   => trim(($geo['city'] ?? '') . ', ' . ($geo['country'] ?? ''), ', '),
        ]);
        
        require_once APP_ROOT . '/includes/mailer.php';
        if ($isNewIp) {
            sendNewLoginNotification($user['email'], $user['first_name'], $logData);
        }

        auditLog('login', 'auth', 'User logged in', $user['id']);
        redirect('index.php?module=dashboard&action=index');
    }

    public function logout(): void {
        if (isLoggedIn()) {
            auditLog('logout', 'auth', 'User logged out');
        }
        destroySession();
        setFlash('success', 'You have been logged out successfully.');
        redirect('index.php?module=auth&action=login');
    }

    public function changePassword(): void {
        requireLogin();
        $errors  = [];
        $success = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            validateCsrf();
            $currentPw = post('current_password');
            $newPw     = post('new_password');
            $confirmPw = post('confirm_password');

            $userModel = new User();
            $user = $userModel->findById(currentUserId());

            if (!password_verify($currentPw, $user['password_hash'])) {
                $errors['current_password'] = 'Current password is incorrect.';
            } elseif (!$this->validatePasswordPolicy($newPw, $errors)) {
                // errors populated inside helper
            } elseif ($newPw !== $confirmPw) {
                $errors['confirm_password'] = 'Passwords do not match.';
            } else {
                $userModel->updatePassword(currentUserId(), $newPw);
                
                // Invalidate other sessions by generating a new token
                $newToken = bin2hex(random_bytes(32));
                $userModel->updateSessionToken(currentUserId(), $newToken);
                $_SESSION[SESSION_USER]['session_token'] = $newToken;

                auditLog('change_password', 'auth', 'User changed their password');
                $success = true;
                setFlash('success', 'Password changed successfully. Other sessions have been logged out.');
                redirect('index.php?module=profile&action=index');
            }
        }
        include APP_ROOT . '/views/auth/change_password.php';
    }

    // ── Password Policy Enforcement ────────────────────────────────

    /**
     * Validate password against policy:
     *   - At least 8 characters
     *   - At least one uppercase letter
     *   - At least one lowercase letter
     *   - At least one digit
     *   - At least one special character
     * Populates $errors['new_password'] on failure.
     */
    private function validatePasswordPolicy(string $password, array &$errors): bool {
        if (strlen($password) < 8) {
            $errors['new_password'] = 'Password must be at least 8 characters.';
            return false;
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors['new_password'] = 'Password must contain at least one uppercase letter.';
            return false;
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors['new_password'] = 'Password must contain at least one lowercase letter.';
            return false;
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors['new_password'] = 'Password must contain at least one number.';
            return false;
        }
        if (!preg_match('/[\W_]/', $password)) {
            $errors['new_password'] = 'Password must contain at least one special character (e.g. @, #, !, $).';
            return false;
        }
        
        // ── Have I Been Pwned Check ────────────────────────────────
        $pwnCount = checkPwnedPassword($password);
        if ($pwnCount > 0) {
            $errors['new_password'] = "This password has been exposed in data breaches $pwnCount times. Please choose a different password.";
            return false;
        }
        
        return true;
    }
}
