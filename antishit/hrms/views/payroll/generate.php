<?php
$pageTitle = 'Generate Payroll: ' . e($period['period_name']);
$breadcrumb = [
    ['label' => 'Payroll', 'url' => 'index.php?module=payroll'],
    ['label' => 'Generate', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="row justify-content-center mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm text-center">
            <div class="card-body p-5">
                <div class="mb-4">
                    <i class="bi bi-calculator text-primary" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-bold mb-3">Generate Payroll for <?= e($period['period_name']) ?></h4>
                <p class="text-muted mb-4">
                    This will compute the gross pay, deductions (SSS, PhilHealth, Pag-IBIG, Tax), and net pay for all active employees based on their basic salary and attendance records between <strong><?= formatDate($period['start_date']) ?></strong> and <strong><?= formatDate($period['end_date']) ?></strong>.
                </p>
                
                <form method="POST" action="index.php?module=payroll&action=generate&id=<?= $period['id'] ?>" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Processing...';">
                    <?= csrfField() ?>
                    <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                        <i class="bi bi-gear-fill me-2"></i>Start Processing
                    </button>
                    <a href="index.php?module=payroll" class="btn btn-light btn-lg px-4 ms-2 rounded-pill">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
