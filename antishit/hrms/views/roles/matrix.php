<?php
/**
 * Role Permission Matrix View
 */
$pageTitle = 'Role Permission Matrix';
$breadcrumb = [['label' => 'Settings', 'url' => 'index.php?module=settings'], ['label' => 'Role Permission Matrix', 'active' => true]];
include APP_ROOT . '/views/layouts/header.php';
?>

<style>
.perm-matrix-container { background: var(--hrms-body-bg); border-radius: 8px; padding: 20px; }
.role-section { background: var(--hrms-card-bg); border: 1px solid var(--hrms-card-border); border-radius: 8px; margin-bottom: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.role-header { background: var(--hrms-card-bg); padding: 15px 20px; border-bottom: 1px solid var(--hrms-card-border); font-weight: 700; font-size: 1.1rem; color: var(--hrms-text-main); border-radius: 8px 8px 0 0; }
.module-row { padding: 15px 20px; border-bottom: 1px solid var(--hrms-card-border); }
.module-row:last-child { border-bottom: none; }
.module-name { font-weight: 800; font-size: 0.85rem; color: var(--hrms-text-muted); margin-bottom: 10px; letter-spacing: 0.5px; text-transform: uppercase; }
.perm-list { display: flex; flex-wrap: wrap; gap: 10px; }
.perm-item { display: flex; align-items: center; background: var(--hrms-card-bg); border: 1px solid var(--hrms-card-border); border-radius: 6px; padding: 5px 12px; font-size: 0.9rem; cursor: pointer; transition: all 0.2s; color: var(--hrms-text-main); }
.perm-item:hover { border-color: var(--hrms-primary); background: rgba(79, 70, 229, 0.05); }
.perm-item input[type="checkbox"] { margin-right: 8px; width: 16px; height: 16px; cursor: pointer; }
.perm-item.checked { border-color: var(--hrms-primary); color: var(--hrms-primary); background-color: rgba(79, 70, 229, 0.1); }
[data-bs-theme="dark"] .perm-item.checked { background-color: rgba(99, 102, 241, 0.2); border-color: var(--hrms-primary-light); color: #fff; }
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
