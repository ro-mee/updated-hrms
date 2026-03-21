<?php
/**
 * CSRF Protection Helpers
 * HRMS - Human Resource Management System
 */

/**
 * Generate a CSRF token (stored in session).
 */
function csrfToken(): string {
    if (empty($_SESSION[CSRF_TOKEN_KEY])) {
        $_SESSION[CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_KEY];
}

/**
 * Output a hidden CSRF input field (use inside forms).
 */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Validate the CSRF token from a POST/PUT/DELETE request.
 * Kills the request with 403 if invalid.
 */
function validateCsrf(): void {
    $token  = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $stored = $_SESSION[CSRF_TOKEN_KEY] ?? '';

    if (!$stored || !hash_equals($stored, $token)) {
        http_response_code(403);
        if (isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Security token expired. Please refresh and try again.']);
        } else {
            setFlash('error', 'Security token expired. Please try again.');
            redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
        }
        exit;
    }
    // Token rotation disabled to prevent "Token Expired" on back button/multi-tab
}

/**
 * Check if the current request is an AJAX request.
 */
function isAjax(): bool {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
