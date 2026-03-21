<?php
/**
 * User Model
 */
class User {
    private PDO $db;
    public function __construct() { $this->db = db(); }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name AS role_name, r.slug AS role_slug,
                   e.id AS employee_id
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN employees e ON e.user_id = u.id
            WHERE u.email = ?
        ");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name AS role_name, r.slug AS role_slug,
                   e.id AS employee_id
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN employees e ON e.user_id = u.id
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function all(int $limit = 100, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name AS role_name, r.slug AS role_slug
            FROM users u JOIN roles r ON u.role_id = r.id
            ORDER BY u.created_at DESC LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO users (role_id, email, password_hash, first_name, last_name, is_active)
            VALUES (?, ?, ?, ?, ?, 1)
        ");
        $stmt->execute([
            $data['role_id'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            $data['first_name'],
            $data['last_name'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [];
        $allowed = ['email','first_name','last_name','role_id','is_active','avatar'];
        foreach ($allowed as $f) {
            if (isset($data[$f])) {
                $fields[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        if (empty($fields)) return false;
        $params[] = $id;
        $stmt = $this->db->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($params);
    }

    public function updateLastLogin(int $id): void {
        $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$id]);
    }

    public function updatePassword(int $id, string $newPassword): bool {
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        return $stmt->execute([password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]), $id]);
    }

    public function updateSessionToken(int $id, ?string $token): bool {
        $stmt = $this->db->prepare("UPDATE users SET session_token = ? WHERE id = ?");
        return $stmt->execute([$token, $id]);
    }

    public function updateAvatar(int $id, string $filename): bool {
        $stmt = $this->db->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        return $stmt->execute([$filename, $id]);
    }

    public function countAll(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    public function roles(): array {
        return $this->db->query("SELECT * FROM roles ORDER BY id")->fetchAll();
    }

    public function deactivate(int $id): bool {
        $stmt = $this->db->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // ── Account Lockout / Rate Limiting ──────────────────────────────

    public function incrementFailedAttempts(int $id): void {
        $this->db->prepare("UPDATE users SET failed_attempts = failed_attempts + 1 WHERE id = ?")->execute([$id]);
    }

    public function resetFailedAttempts(int $id): void {
        $this->db->prepare("UPDATE users SET failed_attempts = 0, lockout_until = NULL WHERE id = ?")->execute([$id]);
    }

    public function lockoutUser(int $id, int $minutes = 15): void {
        $until = date('Y-m-d H:i:s', strtotime("+{$minutes} minutes"));
        $this->db->prepare("UPDATE users SET lockout_until = ? WHERE id = ?")->execute([$until, $id]);
    }

    public function findByEmailWithSecurity(string $email): ?array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name AS role_name, r.slug AS role_slug,
                   e.id AS employee_id
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN employees e ON e.user_id = u.id
            WHERE u.email = ?
        ");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    // ── Login Log ─────────────────────────────────────────────────────

    public function recordLoginLog(array $data): void {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO login_logs
                    (user_id, ip_address, country, city, latitude, longitude, isp, device, is_new_ip, is_suspicious, login_time)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $data['user_id'],
                $data['ip_address'],
                $data['country']   ?? null,
                $data['city']      ?? null,
                $data['latitude']  ?? null,
                $data['longitude'] ?? null,
                $data['isp']       ?? null,
                $data['device']    ?? null,
                (int)($data['is_new_ip']     ?? 0),
                (int)($data['is_suspicious'] ?? 0),
            ]);
        } catch (Exception $e) {
            error_log('Login log error: ' . $e->getMessage());
        }
    }

    public function getLoginHistory(int $userId, int $limit = 10, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT * FROM login_logs
            WHERE user_id = ?
            ORDER BY login_time DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function getLoginHistoryCount(int $userId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM login_logs WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    // ── 2-Step Verification (2FA) ─────────────────────────────────────

    public function setTwoFactorCode(int $userId, ?string $code, ?string $expiresAt): bool {
        $stmt = $this->db->prepare("UPDATE users SET two_factor_code = ?, two_factor_expires_at = ? WHERE id = ?");
        return $stmt->execute([$code, $expiresAt, $userId]);
    }

    public function isValidTwoFactorCode(int $userId, string $code): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM users 
            WHERE id = ? AND two_factor_code = ? AND two_factor_expires_at > NOW()
        ");
        $stmt->execute([$userId, $code]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function clearTwoFactorCode(int $userId): bool {
        return $this->setTwoFactorCode($userId, null, null);
    }

    // ── Trusted Devices ───────────────────────────────────────────────

    public function trustDevice(int $userId, string $token, string $expiresAt): bool {
        $stmt = $this->db->prepare("
            INSERT INTO trusted_devices (user_id, token, expires_at) VALUES (?, ?, ?)
        ");
        return $stmt->execute([$userId, $token, $expiresAt]);
    }

    public function isValidTrustedDevice(int $userId, string $token): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM trusted_devices 
            WHERE user_id = ? AND token = ? AND expires_at > NOW()
        ");
        $stmt->execute([$userId, $token]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function checkIfNewIp(int $userId, string $ip): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM login_logs WHERE user_id = ? AND ip_address = ?
        ");
        $stmt->execute([$userId, $ip]);
        return (int)$stmt->fetchColumn() === 0;
    }

    public function checkIfNewLocation(int $userId, string $country, string $city): bool {
        if (empty($country) && empty($city)) return false;
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM login_logs WHERE user_id = ? AND country = ? AND city = ?
        ");
        $stmt->execute([$userId, $country, $city]);
        return (int)$stmt->fetchColumn() === 0;
    }

    public function getLastLogin(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM login_logs WHERE user_id = ? ORDER BY login_time DESC LIMIT 1
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    // ── Password Reset ────────────────────────────────────────────────
    public function setResetToken(string $email, string $token, string $expiresAt): bool {
        $stmt = $this->db->prepare("UPDATE users SET password_reset_token = ?, password_reset_expiry = ? WHERE email = ?");
        return $stmt->execute([$token, $expiresAt, $email]);
    }

    public function verifyResetToken(string $token): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE password_reset_token = ? AND password_reset_expiry > NOW() AND is_active = 1
        ");
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public function clearResetToken(int $userId): bool {
        $stmt = $this->db->prepare("UPDATE users SET password_reset_token = NULL, password_reset_expiry = NULL WHERE id = ?");
        return $stmt->execute([$userId]);
    }
}
