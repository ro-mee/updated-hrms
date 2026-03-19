<?php /** My Payslips List */
$pageTitle='My Payslips'; $breadcrumb=[['label'=>'My Payslips','active'=>true]];
include APP_ROOT.'/views/layouts/header.php';?>
<div class="container-fluid px-4 py-3">
<h5 class="fw-700 mb-3"><i class="bi bi-receipt text-primary me-2"></i>My Payslips</h5>
<div class="card table-card">
<div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th>Period</th><th>Pay Date</th><th>Gross</th><th>Deductions</th><th>Net Pay</th><th>Status</th><th></th></tr></thead>
<tbody>
<?php if(empty($payslips)):?><tr><td colspan="7"><div class="empty-state"><i class="bi bi-receipt-cutoff"></i>No payslips yet.</div></td></tr><?php endif;?>
<?php foreach($payslips as $ps):?>
<tr>
    <td class="fw-medium"><?=e($ps['period_name'])?></td>
    <td class="small"><?=formatDate($ps['pay_date'])?></td>
    <td class="small"><?=formatCurrency($ps['gross_pay'])?></td>
    <td class="small text-danger"><?=formatCurrency($ps['total_deductions'])?></td>
    <td class="fw-700 text-success"><?=formatCurrency($ps['net_pay'])?></td>
    <td><?=statusBadge($ps['period_status']??$ps['status'])?></td>
    <td><a href="index.php?module=payroll&action=payslip&period_id=<?=$ps['period_id']?>&employee_id=<?=currentUser()['employee_id']?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a></td>
</tr>
<?php endforeach;?>
</tbody>
</table></div>
</div></div>
<?php include APP_ROOT.'/views/layouts/footer.php';?>
