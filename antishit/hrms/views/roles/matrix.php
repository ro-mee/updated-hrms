<?php
/**
 * Role Permission Matrix View
 */
$pageTitle = 'Role Permission Matrix';
$breadcrumb = [['label' => 'Settings', 'url' => 'index.php?module=settings'], ['label' => 'Role Permission Matrix', 'active' => true]];
include APP_ROOT . '/views/layouts/header.php';
?>

<style>
.perm-matrix-container { background: #f8f9fa; border-radius: 8px; padding: 20px; }
.role-section { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.role-header { background: #fff; padding: 15px 20px; border-bottom: 1px solid #eee; font-weight: 700; font-size: 1.1rem; color: #444; border-radius: 8px 8px 0 0; }
.module-row { padding: 15px 20px; border-bottom: 1px solid #f0f0f0; }
.module-row:last-child { border-bottom: none; }
.module-name { font-weight: 800; font-size: 0.85rem; color: #666; margin-bottom: 10px; letter-spacing: 0.5px; }
.perm-list { display: flex; flex-wrap: wrap; gap: 10px; }
.perm-item { display: flex; align-items: center; background: #fff; border: 1px solid #ddd; border-radius: 6px; padding: 5px 12px; font-size: 0.9rem; cursor: pointer; transition: all 0.2s; }
.perm-item:hover { border-color: #007bff; background: #f0f7ff; }
.perm-item input[type="checkbox"] { margin-right: 8px; width: 16px; height: 16px; cursor: pointer; }
.perm-item.checked { border-color: #0d6efd; color: #0d6efd; background-color: #f0f7ff; }
.update-btn-container { position: sticky; bottom: 20px; z-index: 100; text-align: right; padding-right: 20px; }
.role-selector { max-width: 300px; }
</style>

<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-700 mb-0"><i class="bi bi-shield-lock text-primary me-2"></i>Role Permission Matrix</h5>
        <div class="d-flex align-items-center">
            <span class="me-2 text-muted fw-500">Select Role:</span>
            <select class="form-select role-selector shadow-sm" onchange="location.href='index.php?module=roles&role_id='+this.value">
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= $selectedRoleId == $r['id'] ? 'selected' : '' ?>><?= e($r['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <?php if ($selectedRole): ?>
    <form action="index.php?module=roles&action=update" method="POST">
        <?= csrfField() ?>
        <input type="hidden" name="role_id" value="<?= $selectedRoleId ?>">
        
        <div class="role-section">
            <div class="role-header">
                <?= e($selectedRole['name']) ?>
            </div>
            <div class="card-body p-0">
                <?php foreach ($allPermissions as $module => $perms): ?>
                    <div class="module-row">
                        <div class="module-name"><?= e($module) ?></div>
                        <div class="perm-list">
                            <?php foreach ($perms as $p): 
                                $isChecked = in_array($p['id'], $rolePermissions);
                            ?>
                                <label class="perm-item <?= $isChecked ? 'checked' : '' ?>">
                                    <input type="checkbox" name="permissions[]" value="<?= $p['id'] ?>" <?= $isChecked ? 'selected checked' : '' ?> onchange="this.parentElement.classList.toggle('checked', this.checked)">
                                    <?= e($p['description']) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-4 mb-5">
            <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm fw-600">
                <i class="bi bi-check-circle me-2"></i>Update Permissions
            </button>
        </div>
    </form>
    <?php else: ?>
        <div class="alert alert-info">Please select a role to manage permissions.</div>
    <?php endif; ?>
</div>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
