<?php
/**
 * Authentication Helpers
 * HRMS - Human Resource Management System
 */

/**
 * Check if the user is currently logged in and has a valid session token.
 */
function isLoggedIn(): bool {
    if (!isset($_SESSION[SESSION_USER]) || empty($_SESSION[SESSION_USER]['id'])) {
        return false;
    }
    
    // Check session_token against database (Optional: remove if you want full multi-device)
    // Removed token comparison to allow up to 2 devices per Option 1
    
    // Check persistent user_sessions table
    try {
        $stmt = db()->prepare("SELECT COUNT(*) FROM user_sessions WHERE user_id = ? AND session_id = ?");
        $stmt->execute([$_SESSION[SESSION_USER]['id'], session_id()]);
        if ((int)$stmt->fetchColumn() === 0) {
            destroySession();
            return false;
        }
    } catch (Exception $e) {}
    return true;
}

/**
 * Redirect to login if not authenticated.
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        setFlash('error', 'Please log in to access this page.');
        redirect('index.php?module=auth&action=login');
    }
    checkSessionTimeout();
}

/**
 * Get current logged-in user data.
 */
function currentUser(): ?array {
    return $_SESSION[SESSION_USER] ?? null;
}

/**
 * Get current user's role slug.
 */
function currentRole(): string {
    return $_SESSION[SESSION_USER]['role'] ?? '';
}

/**
 * Get current user's ID.
 */
function currentUserId(): int {
    return (int)($_SESSION[SESSION_USER]['id'] ?? 0);
}

/**
 * Check if current user has one of the given roles.
 */
function hasRole(string ...$roles): bool {
    return in_array(currentRole(), $roles, true);
}

/**
 * Require one or more roles; deny access otherwise.
 */
function requireRole(string ...$roles): void {
    requireLogin();
    if (!hasRole(...$roles)) {
        http_response_code(403);
        include APP_ROOT . '/views/errors/403.php';
        exit;
    }
}

/**
 * Require a specific permission; deny access otherwise.
 */
function requirePermission(string $module, string $action = 'view'): void {
    requireLogin();
    if (!can($module, $action)) {
        http_response_code(403);
        include APP_ROOT . '/views/errors/403.php';
        exit;
    }
}

/**
 * Check if the current user can access a given module/action permission.
 * Permissions are stored in session as an array of "module.action" strings.
 * Super admins bypass all permission checks.
 */
function can(string $module, string $action = 'view'): bool {
    if (currentRole() === ROLE_SUPER_ADMIN) return true;
    $perms = $_SESSION[SESSION_USER]['permissions'] ?? [];
    return in_array("$module.$action", $perms, true) || in_array("$module.*", $perms, true);
}

/**
 * Regenerate session ID (call on login to prevent session fixation).
 */
function regenerateSession(): void {
    session_regenerate_id(true);
}

/**
 * Destroy session and redirect to login.
 */
function destroySession(): void {
    if (session_id()) {
        try {
            // Remove from database if exists
            db()->prepare("DELETE FROM user_sessions WHERE session_id = ?")
               ->execute([session_id()]);
        } catch (Exception $e) {}
    }
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/**
 * Load user permissions into session after login.
 */
function loadUserPermissions(int $userId, string $roleSlug): array {
    if ($roleSlug === ROLE_SUPER_ADMIN) return ['*'];
    $stmt = db()->prepare("
        SELECT CONCAT(p.module, '.', p.action) AS perm
        FROM role_permissions rp
        JOIN permissions p ON rp.permission_id = p.id
        JOIN roles r ON rp.role_id = r.id
        WHERE r.slug = ?
    ");
    $stmt->execute([$roleSlug]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// ── Session Inactivity Timeout ─────────────────────────────────────

/**
 * Enforce 15-minute session inactivity timeout.
 * Called inside requireLogin() so every protected page checks it.
 */
function checkSessionTimeout(): void {
    if (!isLoggedIn()) return;
    $timeout = defined('SESSION_TIMEOUT_SECONDS') ? SESSION_TIMEOUT_SECONDS : 900;
    $lastAction = $_SESSION['last_action'] ?? null;
    if ($lastAction !== null && (time() - (int)$lastAction) > $timeout) {
        destroySession();
        setFlash('warning', 'Your session expired due to inactivity. Please log in again.');
        redirect('index.php?module=auth&action=login');
    }
    $_SESSION['last_action'] = time();
}

// ── IP / Device / Geolocation Helpers ─────────────────────────────

/**
 * Get the real visitor IP, proxy-aware.
 */
function getUserIP(): string {
    $keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
             'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = trim(explode(',', $_SERVER[$key])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Get geolocation data from ip-api.com.
 * Returns keys: country, city, latitude, longitude, isp.
 * Gracefully returns defaults on failure.
 */
function getGeolocation(string $ip): array {
    $default = ['country' => '', 'city' => '', 'latitude' => null, 'longitude' => null, 'isp' => ''];
    // Skip for local IPs (development)
    $isLocal = in_array($ip, ['127.0.0.1', '::1'])
        || !filter_var($ip, FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    if ($isLocal) {
        return array_merge($default, ['country' => 'Localhost', 'city' => 'Local']);
    }
    $apiBase = defined('GEOLOCATION_API') ? GEOLOCATION_API : 'http://ip-api.com/json/';
    $url = $apiBase . urlencode($ip) . '?fields=status,country,city,lat,lon,isp';
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 5,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $response = curl_exec($ch);
    $err      = curl_error($ch);
    curl_close($ch);
    if ($err || !$response) { error_log("Geolocation API error [$ip]: $err"); return $default; }
    $data = json_decode($response, true);
    if (!$data || ($data['status'] ?? '') !== 'success') return $default;
    return [
        'country'   => $data['country'] ?? '',
        'city'      => $data['city']    ?? '',
        'latitude'  => $data['lat']     ?? null,
        'longitude' => $data['lon']     ?? null,
        'isp'       => $data['isp']     ?? '',
    ];
}

/**
 * Parse User-Agent into "Browser X on OS" string.
 */
function getDevice(): string {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $os = 'Unknown OS';
    foreach ([
        'Windows 11' => 'Windows NT 10\.0.*Win64',
        'Windows 10' => 'Windows NT 10\.0',
        'Windows 8'  => 'Windows NT 6\.[23]',
        'Windows 7'  => 'Windows NT 6\.1',
        'macOS'      => 'Macintosh|Mac OS X',
        'iOS'        => 'iPhone|iPad',
        'Android'    => 'Android',
        'Linux'      => 'Linux',
    ] as $name => $pattern) {
        if (preg_match("/$pattern/i", $ua)) { $os = $name; break; }
    }
    $browser = 'Unknown Browser';
    foreach ([
        'Edge'    => 'Edg\/',
        'Opera'   => 'OPR\/',
        'Chrome'  => 'Chrome\/',
        'Firefox' => 'Firefox\/',
        'Safari'  => 'Safari\/',
        'IE'      => 'MSIE|Trident\/',
    ] as $name => $pattern) {
        if (preg_match("/$pattern/i", $ua)) {
            preg_match('/(' . str_replace('\\/', '/', $pattern) . ')([\d.]+)/i', $ua, $ver);
            $v = isset($ver[2]) ? ' ' . explode('.', $ver[2])[0] : '';
            $browser = $name . $v;
            break;
        }
    }
    return "$browser on $os";
}

/**
 * Send a new-login email notification.
 * Wraps the existing sendMail() helper; silently skips if not configured.
 */
function sendNewLoginNotification(string $email, string $name, array $logData): void {
    try {
        $loc = trim(($logData['city'] ?? '') . ', ' . ($logData['country'] ?? ''), ', ');
        $time = date('M d, Y h:i A');
        $body = "<p>Hi {$name},</p>
            <p>A new login was recorded on your HRMS account:</p>
            <ul>
                <li><strong>IP:</strong> {$logData['ip_address']}</li>
                <li><strong>Location:</strong> " . ($loc ?: 'Unknown') . "</li>
                <li><strong>Device:</strong> {$logData['device']}</li>
                <li><strong>Time:</strong> {$time}</li>
            </ul>
            <p>If this was not you, please change your password immediately.</p>";
        sendMail($email, APP_NAME . ' – New Login Detected', $body);
    } catch (Exception $e) {
        error_log('Login notification error: ' . $e->getMessage());
    }
}
