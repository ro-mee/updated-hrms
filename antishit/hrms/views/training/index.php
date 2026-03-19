<?php /** Training Programs List */
$pageTitle='Training'; $breadcrumb=[['label'=>'Training','active'=>true]];
include APP_ROOT.'/views/layouts/header.php';?>
<div class="container-fluid px-4 py-3">
<div class="d-flex justify-content-between align-items-center mb-3">
<h5 class="fw-700 mb-0"><i class="bi bi-mortarboard text-primary me-2"></i>Training Programs</h5>
<?php if(can('training','create')):?><a href="index.php?module=training&action=create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Create Training</a><?php endif;?>
</div>
<div class="row g-3">
<?php if(empty($trainings)):?><div class="col-12"><div class="empty-state"><i class="bi bi-mortarboard"></i>No training programs yet.</div></div><?php endif;?>
<?php foreach($trainings as $t):
$enrolled = in_array($t['id'], array_column($myEnrollments,'training_id'));
?>
<div class="col-md-6 col-lg-4">
<div class="card h-100">
    <div class="card-body">
        <?=statusBadge($t['status'])?>
        <h6 class="fw-700 mt-2 mb-1"><?=e($t['title'])?></h6>
        <p class="text-muted small mb-2"><i class="bi bi-person me-1"></i><?=e($t['trainer']??'TBD')?></p>
        <?php if($t['location']):?><p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i><?=e($t['location'])?></p><?php endif;?>
        <p class="small mb-1"><i class="bi bi-calendar me-1 text-primary"></i><?=formatDate($t['start_date'],'M d')?> – <?=formatDate($t['end_date'],'M d, Y')?></p>
        <?php if($t['cost']>0):?><p class="text-success small mb-0"><i class="bi bi-cash me-1"></i><?=formatCurrency($t['cost'])?></p><?php endif;?>
    </div>
    <div class="card-footer bg-transparent">
    <?php if(!$enrolled && currentUser()['employee_id'] && $t['status']==='upcoming'):?>
    <button class="btn btn-primary btn-sm w-100" onclick="enrollTraining(<?=$t['id']?>)">Enroll</button>
    <?php elseif($enrolled):?>
    <span class="badge bg-success w-100 py-2">Enrolled ✓</span>
    <?php endif;?>
    </div>
</div>
</div>
<?php endforeach;?>
</div>
</div>
<form id="enrollForm" method="POST" action="index.php?module=training&action=enroll" class="d-none">
<?=csrfField()?><input type="hidden" name="training_id" id="enrollId">
</form>
<script>
async function enrollTraining(id){
    const res = await postJson('index.php?module=training&action=enroll',{training_id:id});
    showToast(res.success?'success':'danger',res.message);
    if(res.success) setTimeout(()=>location.reload(),1000);
}
</script>
<?php include APP_ROOT.'/views/layouts/footer.php';?>
