<?php
/**
 * Recruitment - Jobs List View
 */
$pageTitle  = 'Jobs & ATS';
$breadcrumb = [['label'=>'Recruitment','active'=>true]];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-700 mb-0"><i class="bi bi-briefcase text-primary me-2"></i>Jobs & Applicants</h5>
    <?php if(can('recruitment','manage')):?><a href="index.php?module=recruitment&action=addJob" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Post Job</a><?php endif;?>
</div>
<!-- Status pills -->
<div class="d-flex gap-2 mb-3 flex-wrap">
    <?php foreach([''=> 'All','open'=>'Open','pending_approval'=>'Pending Approval','closed'=>'Closed','draft'=>'Draft'] as $s=>$l):?>
    <a href="index.php?module=recruitment<?=$s?'&status='.$s:''?>" class="btn btn-sm <?=get('status')===$s?'btn-primary':'btn-outline-secondary'?>"><?=$l?></a>
    <?php endforeach;?>
</div>
<div class="row g-3">
<?php if(empty($jobs)):?><div class="col-12"><div class="empty-state"><i class="bi bi-briefcase"></i>No job postings yet.</div></div><?php endif;?>
<?php foreach($jobs as $job):?>
<div class="col-md-6 col-lg-4">
<div class="card h-100">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-2"><?=statusBadge($job['status'])?><span class="badge bg-light text-dark small"><?=ucwords(str_replace('_',' ',$job['employment_type']))?></span></div>
        <h6 class="fw-700 mb-1"><?=e($job['title'])?></h6>
        <p class="text-muted small mb-2"><?=e($job['department_name'])?></p>
        <?php if($job['salary_min'] || $job['salary_max']):?>
        <p class="text-success small mb-2"><i class="bi bi-cash me-1"></i><?=formatCurrency($job['salary_min'])?> – <?=formatCurrency($job['salary_max'])?></p>
        <?php endif;?>
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted"><i class="bi bi-people me-1"></i><?=$job['applicant_count']??0?> applicant<?=($job['applicant_count']??0)!=1?'s':''?></small>
            <?php if($job['deadline']):?><small class="text-muted"><i class="bi bi-calendar me-1"></i><?=formatDate($job['deadline'])?></small><?php endif;?>
        </div>
    </div>
    <div class="card-footer bg-transparent d-flex gap-2">
        <a href="index.php?module=recruitment&action=viewJob&id=<?=$job['id']?>" class="btn btn-outline-primary btn-sm flex-grow-1">View Applicants</a>
        
        <?php if($job['status'] === 'pending_approval' && hasRole(ROLE_SUPER_ADMIN, ROLE_HR_DIRECTOR)): ?>
            <form action="index.php?module=recruitment&action=approveJob" method="POST" class="d-inline">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= $job['id'] ?>">
                <button type="submit" class="btn btn-outline-success btn-sm" title="Approve Job"><i class="bi bi-check-lg"></i></button>
            </form>
        <?php endif; ?>

        <?php if(currentRole() !== ROLE_RECRUITMENT_OFFICER): ?>
        <a href="index.php?module=recruitment&action=editJob&id=<?=$job['id']?>" class="btn btn-outline-secondary btn-sm" title="Edit Job"><i class="bi bi-pencil"></i></a>
        <form action="index.php?module=recruitment&action=deleteJob" method="POST" onsubmit="return confirm('Delete this job?');" class="d-inline">
            <?= csrfField() ?>
            <input type="hidden" name="id" value="<?= $job['id'] ?>">
            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Job"><i class="bi bi-trash"></i></button>
        </form>
        <?php endif; ?>
    </div>
</div>
</div>
<?php endforeach;?>
</div>
</div>
<?php include APP_ROOT.'/views/layouts/footer.php';?>
