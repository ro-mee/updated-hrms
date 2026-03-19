<?php
/**
 * Application Bootstrap
 * Always included first via index.php
 */

// ── Error Reporting (disable in production) ───────────────────────
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// ── Timezone ─────────────────────────────────────────────────────
date_default_timezone_set(APP_TIMEZONE);

// ── Secure Session Configuration ─────────────────────────────────
$isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', 1);
    ini_set('session.gc_maxlifetime', 7200); // 2 hours
    if ($isSecure) {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}

// ── HTTP Security Headers ─────────────────────────────────────────
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header("Referrer-Policy: strict-origin-when-cross-origin");

if ($isSecure) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}

// Basic CSP to prevent malicious external scripts while allowing local assets & CDNs
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://unpkg.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; img-src 'self' data: https://unpkg.com; connect-src 'self' https://api.pwnedpasswords.com; frame-src 'none'; object-src 'none';");

// ── Create logs directory if missing ─────────────────────────────
$logDir = APP_ROOT . '/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
