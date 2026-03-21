<?php
/**
 * General Helper Functions
 * HRMS - Human Resource Management System
 */

// ── Redirect ─────────────────────────────────────────────────────

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

// ── Flash Messages ────────────────────────────────────────────────

function setFlash(string $type, string $message): void {
    $_SESSION[SESSION_FLASH] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    $flash = $_SESSION[SESSION_FLASH] ?? null;
    unset($_SESSION[SESSION_FLASH]);
    return $flash;
}

// ── XSS Sanitization ─────────────────────────────────────────────

function e(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function sanitizeInput(string $input): string {
    return trim(strip_tags($input));
}

// ── Date Formatting ───────────────────────────────────────────────

function formatDate(string $date, string $format = 'M d, Y'): string {
    if (empty($date) || $date === '0000-00-00') return '—';
    return date($format, strtotime($date));
}

function formatDateTime(string $dt): string {
    if (empty($dt)) return '—';
    return date('M d, Y h:i A', strtotime($dt));
}

function timeAgo(string $datetime): string {
    $now  = new DateTime();
    $ago  = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day'   . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour'  . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' min'   . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'just now';
}

// ── Number / Currency ─────────────────────────────────────────────

function formatCurrency(float $amount, string $symbol = '₱'): string {
    return $symbol . number_format($amount, 2);
}

function formatNumber(mixed $n): string {
    return number_format((float)$n);
}

// ── Pagination ────────────────────────────────────────────────────

function paginate(int $total, int $page, int $perPage = RECORDS_PER_PAGE): array {
    $totalPages = max(1, (int)ceil($total / $perPage));
    $page       = max(1, min($page, $totalPages));
    $offset     = ($page - 1) * $perPage;
    return [
        'total'       => $total,
        'per_page'    => $perPage,
        'current'     => $page,
        'total_pages' => $totalPages,
        'offset'      => $offset,
        'has_prev'    => $page > 1,
        'has_next'    => $page < $totalPages,
    ];
}

function paginationLinks(array $pg, string $baseUrl): string {
    if ($pg['total_pages'] <= 1) return '';
    $html = '<nav><ul class="pagination pagination-sm mb-0">';
    $html .= '<li class="page-item ' . (!$pg['has_prev'] ? 'disabled' : '') . '">'
           . '<a class="page-link" href="' . $baseUrl . '&page=' . ($pg['current'] - 1) . '">‹</a></li>';
    for ($i = 1; $i <= $pg['total_pages']; $i++) {
        $active = ($i === $pg['current']) ? ' active' : '';
        $html  .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $baseUrl . '&page=' . $i . '">' . $i . '</a></li>';
    }
    $html .= '<li class="page-item ' . (!$pg['has_next'] ? 'disabled' : '') . '">'
           . '<a class="page-link" href="' . $baseUrl . '&page=' . ($pg['current'] + 1) . '">›</a></li>';
    $html .= '</ul></nav>';
    return $html;
}

// ── File Upload Helpers ───────────────────────────────────────────

function uploadFile(array $file, string $destDir, array $allowedTypes, int $maxSize = MAX_FILE_SIZE): array {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error code: ' . $file['error']];
    }
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File size exceeds limit (' . ($maxSize / 1024 / 1024) . ' MB).'];
    }
    // Validate MIME via finfo (not just extension)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, $allowedTypes, true)) {
        // Fallback or better error message
        return ['success' => false, 'message' => "File type ($mime) not allowed. Please upload a PDF or Word document."];
    }
    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = bin2hex(random_bytes(16)) . '.' . strtolower($ext);
    $destPath = rtrim($destDir, '/') . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['success' => false, 'message' => 'Failed to move uploaded file.'];
    }
    return ['success' => true, 'filename' => $filename];
}

// ── Audit Logging ─────────────────────────────────────────────────

function auditLog(string $action, string $module, string $description, ?int $targetId = null): void {
    try {
        $stmt = db()->prepare("
            INSERT INTO audit_logs (user_id, action, module, description, target_id, ip_address, user_agent, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            currentUserId() ?: null,
            $action,
            $module,
            $description,
            $targetId,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
    } catch (Exception $e) {
        error_log("Audit log error: " . $e->getMessage());
    }
}

// ── JSON Response ─────────────────────────────────────────────────

function jsonResponse(bool $success, string $message = '', mixed $data = null, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

// ── Status Badge HTML ─────────────────────────────────────────────

function statusBadge(string $status): string {
    $map = [
        'active'    => 'success', 'approved'  => 'success', 'paid'       => 'success',
        'present'   => 'success', 'hired'      => 'success',
        'inactive'  => 'secondary', 'rejected' => 'danger', 'absent'     => 'danger',
        'pending'   => 'warning',  'draft'     => 'secondary',
        'processing'=> 'info',    'late'       => 'warning', 'reviewing'  => 'info',
        'interview' => 'primary',  'offered'   => 'primary', 'cancelled'  => 'secondary',
        'on_leave'  => 'info',    'half_day'   => 'warning',
    ];
    $color = $map[strtolower($status)] ?? 'secondary';
    return '<span class="badge bg-' . $color . '">' . ucwords(str_replace('_', ' ', $status)) . '</span>';
}

// ── String Helpers ────────────────────────────────────────────────

function slugify(string $text): string {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
}

function truncate(string $text, int $len = 80): string {
    return strlen($text) > $len ? substr($text, 0, $len) . '…' : $text;
}

function avatarUrl(?string $filename): string {
    if ($filename && file_exists(UPLOAD_AVATAR_PATH . $filename)) {
        return APP_URL . '/uploads/avatars/' . $filename;
    }
    return APP_URL . '/assets/images/default-avatar.png';
}

// ── Array / Validation Helpers ────────────────────────────────────

function post(string $key, mixed $default = ''): mixed {
    return $_POST[$key] ?? $default;
}

function get(string $key, mixed $default = ''): mixed {
    return $_GET[$key] ?? $default;
}

function validateRequired(array $fields, array $data): array {
    $errors = [];
    foreach ($fields as $field) {
        if (empty(trim((string)($data[$field] ?? '')))) {
            $errors[$field] = ucwords(str_replace('_', ' ', $field)) . ' is required.';
        }
    }
    return $errors;
}

function currentPageUrl(): string {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
         . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function workingDaysBetween(string $start, string $end): int {
    $start = new DateTime($start);
    $end   = new DateTime($end);
    $end->modify('+1 day');
    $days = 0;
    while ($start < $end) {
        if ($start->format('N') < 6) $days++;
        $start->modify('+1 day');
    }
    return $days;
}

// ── Security Enhancements ─────────────────────────────────────────

/**
 * Checks if a password has been exposed in data breaches using the Have I Been Pwned k-Anonymity API.
 * Returns the number of times it was found (0 if safe).
 */
function checkPwnedPassword(string $password): int {
    $hash = strtoupper(sha1($password));
    $prefix = substr($hash, 0, 5);
    $suffix = substr($hash, 5);

    $url = "https://api.pwnedpasswords.com/range/" . $prefix;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'HRMS-Security-Check');
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $response) {
        $lines = explode("\n", $response);
        foreach ($lines as $line) {
            $parts = explode(':', trim($line));
            if (count($parts) === 2 && $parts[0] === $suffix) {
                return (int)$parts[1];
            }
        }
    }
    return 0;
}

// ── Data-at-Rest Encryption ───────────────────────────────────────

/**
 * Encrypts sensitive data using AES-256-CBC.
 */
function encrypt_pii(?string $data): ?string {
    if ($data === null || $data === '') return $data;
    $method = 'aes-256-cbc';
    $key = defined('APP_ENCRYPTION_KEY') ? APP_ENCRYPTION_KEY : 'default_insecure_key_override_me';
    $key = substr(hash('sha256', $key, true), 0, 32); 
    $ivLength = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

/**
 * Decrypts sensitive AES-256-CBC encrypted data.
 */
function decrypt_pii(?string $data): ?string {
    if ($data === null || $data === '') return $data;
    $decoded = base64_decode($data, true);
    if ($decoded === false) return $data;
    $method = 'aes-256-cbc';
    $key = defined('APP_ENCRYPTION_KEY') ? APP_ENCRYPTION_KEY : 'default_insecure_key_override_me';
    $key = substr(hash('sha256', $key, true), 0, 32);
    $ivLength = openssl_cipher_iv_length($method);
    if (strlen($decoded) < $ivLength) return $data;
    $iv = substr($decoded, 0, $ivLength);
    $ciphertext = substr($decoded, $ivLength);
    $decrypted = openssl_decrypt($ciphertext, $method, $key, 0, $iv);
    return $decrypted === false ? $data : $decrypted;
}

/**
 * Generate a random temporary password.
 */
function generateRandomPassword(int $length = 12): string {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $pass = '';
    for ($i = 0; $i < $length; $i++) {
        $pass .= $chars[random_int(0, strlen($chars) - 1)];
    }
    // Ensure it meets basic complexity (at least one uppercase, lowercase, and number)
    if (!preg_match('/[A-Z]/', $pass) || !preg_match('/[a-z]/', $pass) || !preg_match('/[0-9]/', $pass)) {
        return generateRandomPassword($length);
    }
    return $pass;
}
