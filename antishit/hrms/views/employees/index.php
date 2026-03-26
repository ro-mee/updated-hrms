<?php
/** Employee List View */
$pageTitle  = 'Employees';
$breadcrumb = [['label'=>'Employees','active'=>true]];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-700 mb-0"><i class="bi bi-people text-primary me-2"></i>Employees</h5>
        <?php if (can('employees','manage')): ?>
        <a href="index.php?module=employees&action=add" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Employee</a>
        <?php endif; ?>
    </div>
    <!-- Filters -->
    <form class="card p-3 mb-3" method="GET" action="index.php">
        <input type="hidden" name="module" value="employees">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name / ID / Email…" value="<?= e(get('search')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select">
                    <option value="">All Departments</option>
                    <?php foreach($departments as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= get('department_id')==$d['id']?'selected':'' ?>><?= e($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <?php foreach(['active','inactive','resigned','terminated','on_leave'] as $s): ?>
                    <option value="<?=$s?>" <?= get('status')===$s?'selected':'' ?>><?= ucwords(str_replace('_',' ',$s)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="index.php?module=employees" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            </div>
        </div>
    </form>
    <!-- Table -->
    <div class="card table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="dataTable">
                <thead><tr><th>Employee</th><th>Department</th><th>Position</th><th>Type</th><th>Date Hired</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                <?php if (empty($employees)): ?>
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-people"></i>No employees found.</div></td></tr>
                <?php else: ?>
                <?php foreach($employees as $emp): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= avatarUrl($emp['avatar']) ?>" class="avatar-sm" alt="">
                            <div>
                                <div class="fw-medium"><?= e($emp['full_name']) ?></div>
                                <div class="text-muted small"><?= e($emp['employee_number']) ?> · <?= e($emp['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="small"><?= e($emp['department_name']) ?></td>
                    <td class="small"><?= e($emp['position_title']) ?></td>
                    <td><span class="badge bg-light text-dark"><?= ucwords(str_replace('_',' ',$emp['employment_type'])) ?></span></td>
                    <td class="small"><?= formatDate($emp['date_hired']) ?></td>
                    <td><?= statusBadge($emp['status']) ?></td>
                    <td class="text-end">
                        <a href="index.php?module=employees&action=view&id=<?= $emp['id'] ?>" class="btn btn-outline-primary btn-sm me-1" title="View"><i class="bi bi-eye"></i></a>
                        <?php if(can('employees','edit')): ?>
                        <a href="index.php?module=employees&action=edit&id=<?= $emp['id'] ?>" class="btn btn-outline-secondary btn-sm me-1" title="Edit"><i class="bi bi-pencil"></i></a>
                        <?php endif; ?>
                        <?php if(can('employees','delete')): ?>
                        <form method="POST" action="index.php?module=employees&action=delete" class="d-inline">
                            <?= csrfField() ?><input type="hidden" name="id" value="<?= $emp['id'] ?>">
                            <button class="btn btn-outline-danger btn-sm" data-confirm="Deactivate this employee?" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($total > RECORDS_PER_PAGE): ?>
        <div class="card-footer d-flex justify-content-between align-items-center bg-transparent">
            <small class="text-muted">Showing <?= count($employees) ?> of <?= $total ?> employees</small>
            <?= paginationLinks($pg, 'index.php?module=employees&search='.urlencode(get('search')).'&department_id='.get('department_id').'&status='.get('status')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
