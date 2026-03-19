<?php
$pageTitle = 'New Payroll Period';
$breadcrumb = [
    ['label' => 'Payroll', 'url' => 'index.php?module=payroll'],
    ['label' => 'New Period', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="row justify-content-center mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white pb-0 border-0 mt-2">
                <h5 class="fw-bold"><i class="bi bi-calendar-plus text-primary me-2"></i>Create Payroll Period</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?module=payroll&action=create">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label class="form-label">Period Name <span class="text-danger">*</span></label>
                        <input type="text" name="period_name" class="form-control <?= isset($errors['period_name']) ? 'is-invalid' : '' ?>" placeholder="e.g. March 2026 1st Half" required>
                        <?php if(isset($errors['period_name'])): ?><div class="invalid-feedback"><?= $errors['period_name'] ?></div><?php endif; ?>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control <?= isset($errors['start_date']) ? 'is-invalid' : '' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control <?= isset($errors['end_date']) ? 'is-invalid' : '' ?>" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Pay Date <span class="text-danger">*</span></label>
                        <input type="date" name="pay_date" class="form-control <?= isset($errors['pay_date']) ? 'is-invalid' : '' ?>" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center"><i class="bi bi-check-circle me-2"></i>Create & Proceed to Generation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
