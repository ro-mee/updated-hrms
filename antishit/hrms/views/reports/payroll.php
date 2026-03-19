<?php
$pageTitle = 'Payroll Report';
$breadcrumb = [
    ['label' => 'Reports', 'url' => 'index.php?module=reports'],
    ['label' => 'Payroll Report', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-cash-stack text-primary me-2"></i>Payroll Summary Report</h6>
            <a href="index.php?module=reports" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Back to Reports</a>
        </div>
    </div>

    <div class="card table-card">
        <div class="table-responsive" style="max-height: 500px">
            <table class="table table-hover table-sm align-middle mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>Period Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Pay Date</th>
                        <th class="text-center">Employees</th>
                        <th class="text-end">Total Gross</th>
                        <th class="text-end">Total Deductions</th>
                        <th class="text-end">Total Net</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($periods)): ?><tr><td colspan="9">No records found.</td></tr><?php endif; ?>
                <?php foreach($periods as $p): ?>
                <tr>
                    <td class="fw-medium"><?= e($p['period_name']) ?></td>
                    <td><?= formatDate($p['start_date']) ?></td>
                    <td><?= formatDate($p['end_date']) ?></td>
                    <td><?= formatDate($p['pay_date']) ?></td>
                    <td class="text-center"><?= $p['employees']??0 ?></td>
                    <td class="text-end text-primary"><?= formatCurrency($p['total_gross']??0) ?></td>
                    <td class="text-end text-danger">-<?= formatCurrency($p['total_deductions']??0) ?></td>
                    <td class="text-end fw-bold text-success"><?= formatCurrency($p['total_net']??0) ?></td>
                    <td><?= statusBadge($p['status']) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
