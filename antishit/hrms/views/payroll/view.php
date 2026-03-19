<?php
$pageTitle = 'Payroll Period: ' . e($period['period_name']);
$breadcrumb = [
    ['label' => 'Payroll', 'url' => 'index.php?module=payroll'],
    ['label' => $period['period_name'], 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
    <?= csrfField() ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><?= e($period['period_name']) ?></h4>
            <div class="text-muted small"><strong><?= formatDate($period['start_date']) ?></strong> to <strong><?= formatDate($period['end_date']) ?></strong> &nbsp;&bull;&nbsp; Pay Date: <strong><?= formatDate($period['pay_date']) ?></strong></div>
        </div>
        <div class="d-flex gap-2">
            <?= statusBadge($period['status']) ?>
            <?php if ($period['status'] === 'processing' && can('payroll','approve')): ?>
            <button class="btn btn-success btn-sm" onclick="approvePayroll(<?= $period['id'] ?>)"><i class="bi bi-check-circle me-1"></i>Approve Payroll</button>
            <?php elseif ($period['status'] === 'approved' && can('payroll','markPaid')): ?>
            <button class="btn btn-primary btn-sm" onclick="markPaid(<?= $period['id'] ?>)"><i class="bi bi-cash-coin me-1"></i>Mark as Paid</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header bg-white pb-0 border-0 pt-3">
            <h6 class="fw-bold text-muted"><i class="bi bi-receipt me-2"></i>Payslips Generated</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Basic Salary</th>
                        <th>Gross Pay</th>
                        <th>Deductions</th>
                        <th>Net Pay</th>
                        <th>Payslip</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($payslips)): ?><tr><td colspan="6"><div class="empty-state"><i class="bi bi-emoji-frown"></i>No payslips found for this period.</div></td></tr><?php endif; ?>
                <?php foreach($payslips as $ps): ?>
                <tr>
                    <td>
                        <div class="fw-medium"><?= e($ps['full_name']) ?></div>
                        <div class="small text-muted"><?= e($ps['employee_number']) ?></div>
                    </td>
                    <td class="small"><?= formatCurrency($ps['basic_salary']) ?></td>
                    <td class="small fw-medium text-primary"><?= formatCurrency($ps['gross_pay']) ?></td>
                    <td class="small text-danger">-<?= formatCurrency($ps['total_deductions']) ?></td>
                    <td class="fw-bold text-success"><?= formatCurrency($ps['net_pay']) ?></td>
                    <td>
                        <a href="index.php?module=payroll&action=payslip&period_id=<?= $period['id'] ?>&employee_id=<?= $ps['employee_id'] ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i> View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if($pg['total_pages']>1): ?>
        <div class="card-footer bg-white border-top-0"><?= renderPagination($pg) ?></div>
        <?php endif; ?>
    </div>
</div>

<script>
async function approvePayroll(id) {
    if(!confirm('Approve this payroll? Once approved, it can be marked as paid and employees will see their payslips.')) return;
    const res = await postJson('index.php?module=payroll&action=approve', { period_id: id });
    showToast(res.success?'success':'danger', res.message);
    if(res.success) setTimeout(()=>location.reload(), 1000);
}
async function markPaid(id) {
    if(!confirm('Mark this payroll as PAID? This officially confirms funds have been disbursed.')) return;
    const res = await postJson('index.php?module=payroll&action=markPaid', { period_id: id });
    showToast(res.success?'success':'danger', res.message);
    if(res.success) setTimeout(()=>location.reload(), 1000);
}
</script>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
