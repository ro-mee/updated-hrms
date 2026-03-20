<?php
/**
 * UserSession Model
 * Handles persistent session tracking in the database.
 */
class UserSession {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    /**
     * Create a new session record.
     */
    public function create(array $data): bool {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent, device, location, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE last_activity = NOW()
            ");
            return $stmt->execute([
                $data['user_id'],
                $data['session_id'],
                $data['ip_address'] ?? null,
                $data['user_agent'] ?? null,
                $data['device'] ?? null,
                $data['location'] ?? null
            ]);
        } catch (Exception $e) {
            error_log("Session create error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all active sessions for a user.
     */
    public function getByUserId(int $userId): array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM user_sessions WHERE user_id = ? ORDER BY last_activity DESC");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Check if a specific session exists and is valid.
     */
    public function isValid(int $userId, string $sessionId): bool {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM user_sessions WHERE user_id = ? AND session_id = ?");
            $stmt->execute([$userId, $sessionId]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return true; // Graceful failure: don't boot user if DB is down
        }
    }

    /**
     * Delete a session by ID.
     */
    public function revoke(int $id, int $userId): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM user_sessions WHERE id = ? AND user_id = ?");
            return $stmt->execute([$id, $userId]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Delete a session by PHP session ID.
     */
    public function remove(string $sessionId): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM user_sessions WHERE session_id = ?");
            return $stmt->execute([$sessionId]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Clean up old sessions (optional, can be called via cron).
     */
    public function cleanup(int $days = 30): void {
        $this->db->prepare("DELETE FROM user_sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL ? DAY)")
                 ->execute([$days]);
    }

    /**
     * Enforce a maximum number of sessions for a user.
     * Deletes the oldest sessions if the count exceeds the limit.
     */
    public function enforceLimit(int $userId, int $limit = 2): void {
        try {
            // Find sessions to delete (ordered by oldest first)
            $stmt = $this->db->prepare("
                SELECT id FROM user_sessions 
                WHERE user_id = ? 
                ORDER BY created_at ASC
            ");
            $stmt->execute([$userId]);
            $sessions = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (count($sessions) >= $limit) {
                // Number of sessions to delete to make room for 1 new session
                $toDelete = count($sessions) - ($limit - 1);
                if ($toDelete > 0) {
                    $ids = array_slice($sessions, 0, $toDelete);
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $stmt = $this->db->prepare("DELETE FROM user_sessions WHERE id IN ($placeholders)");
                    $stmt->execute($ids);
                }
            }
        } catch (Exception $e) {
            error_log("Session limit error: " . $e->getMessage());
        }
    }
}
