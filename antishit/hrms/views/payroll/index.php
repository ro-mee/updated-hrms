<?php /** Payroll Periods List */
$pageTitle='Payroll'; $breadcrumb=[['label'=>'Payroll','active'=>true]];
include APP_ROOT.'/views/layouts/header.php';?>
<div class="container-fluid px-4 py-3">
<div class="d-flex justify-content-between align-items-center mb-3">
<h5 class="fw-700 mb-0"><i class="bi bi-cash-stack text-primary me-2"></i>Payroll Periods</h5>
<?php if(can('payroll','generate')):?><a href="index.php?module=payroll&action=create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Period</a><?php endif;?>
</div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th>Period</th><th>Start</th><th>End</th><th>Pay Date</th><th>Employees</th><th>Total Net</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php if(empty($periods)):?><tr><td colspan="8"><div class="empty-state"><i class="bi bi-cash"></i>No payroll periods yet.</div></td></tr><?php endif;?>
<?php foreach($periods as $p):?>
<tr>
    <td class="fw-medium"><?=e($p['period_name'])?></td>
    <td class="small"><?=formatDate($p['start_date'])?></td>
    <td class="small"><?=formatDate($p['end_date'])?></td>
    <td class="small"><?=formatDate($p['pay_date'])?></td>
    <td class="text-center"><?=$p['employee_count']??0?></td>
    <td class="fw-medium text-success"><?=formatCurrency($p['total_net']??0)?></td>
    <td><?=statusBadge($p['status'])?></td>
    <td>
        <a href="index.php?module=payroll&action=view&id=<?=$p['id']?>" class="btn btn-outline-primary btn-sm me-1"><i class="bi bi-eye"></i></a>
        <?php if($p['status']==='draft' && can('payroll','generate')):?>
        <a href="index.php?module=payroll&action=generate&id=<?=$p['id']?>" class="btn btn-outline-secondary btn-sm me-1" title="Generate"><i class="bi bi-calculator"></i></a>
        <?php endif;?>
        <?php if($p['status']==='processing' && can('payroll','approve')):?>
        <button class="btn btn-success btn-sm" onclick="approvePayroll(<?=$p['id']?>)" title="Approve"><i class="bi bi-check-lg"></i></button>
        <?php endif;?>
    </td>
</tr>
<?php endforeach;?>
</tbody></table></div></div>
</div>
<form id="approveForm" method="POST" action="index.php?module=payroll&action=approve" class="d-none">
    <?=csrfField()?><input type="hidden" name="period_id" id="approvePeriodId">
</form>
<script>
async function approvePayroll(id){
    if(!confirm('Approve this payroll period?')) return;
    const res = await postJson('index.php?module=payroll&action=approve',{period_id:id});
    showToast(res.success?'success':'danger',res.message);
    if(res.success) setTimeout(()=>location.reload(),1000);
}
</script>
<?php include APP_ROOT.'/views/layouts/footer.php';?>
