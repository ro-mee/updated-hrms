<?php /** Audit Log View */
$pageTitle='Audit Logs'; $breadcrumb=[['label'=>'Audit Logs','active'=>true]];
include APP_ROOT.'/views/layouts/header.php';?>
<div class="container-fluid px-4 py-3">
<h5 class="fw-700 mb-3"><i class="bi bi-shield-check text-primary me-2"></i>Audit Logs</h5>
<form class="card p-3 mb-3" method="GET" action="index.php">
<input type="hidden" name="module" value="audit">
<div class="row g-2 align-items-end">
    <div class="col-md-3"><label class="form-label">User (Name/Email)</label><input type="text" name="search" class="form-control" placeholder="e.g. Rome Lorente" value="<?=e(get('search'))?>"></div>
    <div class="col-md-2"><label class="form-label">Module</label><input type="text" name="log_module" class="form-control" placeholder="e.g. employees" value="<?=e(get('log_module'))?>"></div>
    <div class="col-md-2"><label class="form-label">Date From</label><input type="date" name="date_from" class="form-control" value="<?=e(get('date_from',date('Y-m-01')))?>"></div>
    <div class="col-md-2"><label class="form-label">Date To</label><input type="date" name="date_to" class="form-control" value="<?=e(get('date_to',date('Y-m-d')))?>"></div>
    <div class="col-md-3 d-flex gap-2"><button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search me-1"></i>Filter</button><a href="index.php?module=audit" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a></div>
</div>
</form>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover table-sm mb-0">
<thead><tr><th>Time</th><th>User</th><th>Action</th><th>Module</th><th>Description</th><th>IP</th></tr></thead>
<tbody>
<?php if(empty($logs)):?><tr><td colspan="6"><div class="empty-state"><i class="bi bi-shield"></i>No audit logs found</div></td></tr><?php endif;?>
<?php foreach($logs as $l):?>
<tr>
    <td class="small text-muted"><?=formatDateTime($l['created_at'])?></td>
    <td class="small"><?=e($l['full_name']??'System')?></td>
    <td><span class="badge bg-light text-dark"><?=e($l['action'])?></span></td>
    <td class="small"><?=e($l['module'])?></td>
    <td class="small"><?=e(truncate($l['description'],80))?></td>
    <td class="small text-muted"><?=e($l['ip_address'])?></td>
</tr>
<?php endforeach;?>
</tbody>
</table></div>
<?php if($total > RECORDS_PER_PAGE):?>
<div class="card-footer d-flex justify-content-between align-items-center bg-transparent">
<small class="text-muted">Showing <?=count($logs)?> of <?=$total?></small>
<?=paginationLinks($pg,'index.php?module=audit&log_module='.get('log_module').'&date_from='.get('date_from').'&date_to='.get('date_to').'&search='.get('search'))?>
</div>
<?php endif;?>
</div></div>
<?php include APP_ROOT.'/views/layouts/footer.php';?>
