<?php
/** Admin Attendance Edit View */
$pageTitle = 'Edit Attendance Record';
$breadcrumb = [
    ['label' => 'Attendance', 'url' => 'index.php?module=attendance'],
    ['label' => 'Edit Record', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="row justify-content-center mt-3">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pt-3">
                <h5 class="fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Attendance</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-4 p-3 rounded shadow-sm border-start border-primary border-4" style="background: rgba(255,255,255,0.03);">
                    <div class="flex-shrink-0">
                        <img src="<?= avatarUrl($employee['avatar']) ?>" class="rounded-circle border" width="50" height="50" alt="" style="object-fit:cover">
                    </div>
                    <div class="ms-3">
                        <div class="fw-bold text-main"><?= e($employee['full_name']) ?></div>
                        <div class="text-muted small"><?= e($employee['employee_number']) ?> &bull; <?= e($employee['department_name']) ?></div>
                        <div class="badge bg-primary mt-1"><?= formatDate($date, 'M d, Y') ?></div>
                    </div>
                </div>

                <form method="POST" action="index.php?module=attendance&action=edit&employee_id=<?= $employeeId ?>&date=<?= $date ?>">
                    <?= csrfField() ?>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Clock In</label>
                            <input type="time" name="clock_in" class="form-control" value="<?= $existing['clock_in'] ? date('H:i', strtotime($existing['clock_in'])) : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Clock Out</label>
                            <input type="time" name="clock_out" class="form-control" value="<?= $existing['clock_out'] ? date('H:i', strtotime($existing['clock_out'])) : '' ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <?php foreach(['present','absent','late','half_day','on_leave'] as $s): ?>
                                <option value="<?= $s ?>" <?= ($existing['status'] ?? 'present') === $s ? 'selected' : '' ?>>
                                    <?= ucwords(str_replace('_',' ',$s)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3" placeholder="Reason for adjustment..."><?= e($existing['remarks'] ?? '') ?></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-save me-2"></i>Update Record</button>
                        <a href="index.php?module=attendance" class="btn btn-light btn-lg rounded-pill">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
