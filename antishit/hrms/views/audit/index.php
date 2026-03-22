<?php /** Audit Log View */
$pageTitle='Audit Logs'; $breadcrumb=[['label'=>'Audit Logs','active'=>true]];
include APP_ROOT.'/views/layouts/header.php';?>
<div class="container-fluid px-4 py-3">
<h5 class="fw-700 mb-3"><i class="bi bi-shield-check text-primary me-2"></i>Audit Logs</h5>
<form class="card border-0 shadow-sm p-3 mb-4 bg-light-subtle" method="GET" action="index.php">
<input type="hidden" name="module" value="audit">
<div class="row g-3 align-items-end">
    <div class="col-md-3">
        <label class="form-label fw-600 small text-muted">User (Name/Email)</label>
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-person text-muted"></i></span>
            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="e.g. Rome Lorente" value="<?=e(get('search'))?>">
        </div>
    </div>
    <div class="col-md-2">
        <label class="form-label fw-600 small text-muted">Module</label>
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-box text-muted"></i></span>
            <input type="text" name="log_module" class="form-control border-start-0 ps-0" placeholder="e.g. employees" value="<?=e(get('log_module'))?>">
        </div>
    </div>
    <div class="col-md-2">
        <label class="form-label fw-600 small text-muted">Date From</label>
        <input type="date" name="date_from" class="form-control form-control-sm" value="<?=e(get('date_from',date('Y-m-01')))?>">
    </div>
    <div class="col-md-2">
        <label class="form-label fw-600 small text-muted">Date To</label>
        <input type="date" name="date_to" class="form-control form-control-sm" value="<?=e(get('date_to',date('Y-m-d')))?>">
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm flex-fill fw-600"><i class="bi bi-filter me-1"></i>Apply Filter</button>
        <a href="index.php?module=audit" class="btn btn-outline-secondary btn-sm px-3" title="Clear Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
    </div>
</div>
</form>
<div class="card table-card shadow-sm border-0"><div class="table-responsive"><table class="table table-hover table-sm mb-0">
<thead>
    <tr>
        <th class="ps-3">Time</th>
        <th>User</th>
        <th>Action</th>
        <th>Module</th>
        <th>Description</th>
        <th class="pe-3">IP</th>
    </tr>
</thead>
<tbody>
<?php if(empty($logs)):?>
    <tr><td colspan="6"><div class="empty-state py-5"><i class="bi bi-shield-slash fs-1"></i><p>No audit logs found matching your criteria</p></div></td></tr>
<?php endif;?>
<?php foreach($logs as $l):?>
<tr class="align-middle">
    <td class="ps-3 py-2">
        <div class="fw-500 text-main mb-0" style="font-size: 0.82rem;"><?=date('M d, Y', strtotime($l['created_at']))?></div>
        <div class="text-muted" style="font-size: 0.72rem;"><?=date('h:i A', strtotime($l['created_at']))?></div>
    </td>
    <td>
        <div class="d-flex align-items-center">
            <div class="avatar-xs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width:24px;height:24px;font-size:0.75rem;">
                <i class="bi bi-person"></i>
            </div>
            <span class="fw-600 text-main" style="font-size: 0.85rem;"><?=e($l['full_name']??'System')?></span>
        </div>
    </td>
    <td><?=logActionBadge($l['action'])?></td>
    <td><span class="text-primary-emphasis fw-500" style="font-size: 0.82rem;"><?=e($l['module'])?></span></td>
    <td style="max-width: 350px;">
        <div class="text-main text-wrap lh-sm" style="font-size: 0.82rem;"><?=e(truncate($l['description'], 120))?></div>
    </td>
    <td class="pe-3 small text-muted font-monospace" style="font-size: 0.75rem;"><?=e($l['ip_address'])?></td>
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
