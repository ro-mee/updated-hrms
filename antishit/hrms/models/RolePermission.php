<?php
/**
 * Role and Permission Models
 */

class Role {
    private PDO $db;
    public function __construct() { $this->db = db(); }

    public function all(): array {
        return $this->db->query("SELECT * FROM roles ORDER BY id")->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM roles WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findBySlug(string $slug): ?array {
        $stmt = $this->db->prepare("SELECT * FROM roles WHERE slug=?");
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function permissions(int $roleId): array {
        $stmt = $this->db->prepare("
            SELECT permission_id FROM role_permissions WHERE role_id=?
        ");
        $stmt->execute([$roleId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function syncPermissions(int $roleId, array $permissionIds): void {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("DELETE FROM role_permissions WHERE role_id=?");
            $stmt->execute([$roleId]);

            if (!empty($permissionIds)) {
                $stmt = $this->db->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
                foreach ($permissionIds as $pId) {
                    $stmt->execute([$roleId, $pId]);
                }
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}

class Permission {
    private PDO $db;
    public function __construct() { $this->db = db(); }

    public function all(): array {
        return $this->db->query("SELECT * FROM permissions ORDER BY module, action")->fetchAll();
    }

    public function allGroupedByModule(): array {
        $rows = $this->all();
        $grouped = [];
        foreach ($rows as $r) {
            $grouped[strtoupper($r['module'])][] = $r;
        }
        return $grouped;
    }
}
