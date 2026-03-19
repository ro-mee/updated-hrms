<?php /** Leave Request Form */
$pageTitle='Request Leave'; $breadcrumb=[['label'=>'Leaves','url'=>'index.php?module=leaves','active'=>false],['label'=>'Request','active'=>true]];
include APP_ROOT.'/views/layouts/header.php'; ?>
<div class="container-fluid px-4 py-3">
<div class="row justify-content-center"><div class="col-lg-7">
<div class="card">
    <div class="card-header py-3"><i class="bi bi-calendar-plus text-primary me-2"></i>New Leave Request</div>
    <div class="card-body p-4">
    <?php if(!empty($errors)):?><div class="alert alert-danger py-2"><?php foreach($errors as $e): ?><div><?=e($e)?></div><?php endforeach;?></div><?php endif;?>
    <form method="POST" action="index.php?module=leaves&action=request" enctype="multipart/form-data">
        <?=csrfField()?>
        <div class="mb-3">
            <label class="form-label">Leave Type *</label>
            <select name="leave_type_id" class="form-select" required id="leaveTypeSelect">
                <option value="">-- Select --</option>
                <?php foreach($leaveTypes as $lt): ?><option value="<?=$lt['id']?>" <?=post('leave_type_id')==$lt['id']?'selected':''?>><?=e($lt['name'])?> (<?=$lt['days_allowed']?> days/yr, <?=$lt['is_paid']?'Paid':'Unpaid'?>)</option><?php endforeach;?>
            </select>
        </div>
        <!-- Balance preview -->
        <div id="balancePreview" class="alert alert-info py-2 d-none small mb-3"></div>
        <div class="row g-3 mb-3">
            <div class="col-md-6"><label class="form-label">Start Date *</label><input type="date" name="start_date" class="form-control" id="startDate" value="<?=e(post('start_date',date('Y-m-d')))?>" required></div>
            <div class="col-md-6"><label class="form-label">End Date *</label><input type="date" name="end_date" class="form-control" id="endDate" value="<?=e(post('end_date',date('Y-m-d')))?>" required></div>
        </div>
        <div id="daysPreview" class="text-muted small mb-3"></div>
        <div class="mb-3"><label class="form-label">Reason / Details *</label><textarea name="reason" class="form-control" rows="4" required placeholder="Please describe the reason for your leave request…"><?=e(post('reason'))?></textarea></div>
        <div class="mb-4"><label class="form-label">Supporting Document <span class="text-muted">(optional – PDF/image, max 5MB)</span></label><input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>Submit Request</button>
            <a href="index.php?module=leaves" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
    </div>
</div>
</div></div></div>
<script>
function countDays() {
    const s=new Date(document.getElementById('startDate').value), e=new Date(document.getElementById('endDate').value);
    if(isNaN(s)||isNaN(e)||e<s){document.getElementById('daysPreview').textContent='';return;}
    let days=0,cur=new Date(s);
    while(cur<=e){if(cur.getDay()!==0&&cur.getDay()!==6)days++;cur.setDate(cur.getDate()+1);}
    document.getElementById('daysPreview').innerHTML=`<i class="bi bi-calendar3 me-1"></i><strong>${days}</strong> working day${days!==1?'s':''} requested`;
}
document.getElementById('startDate')?.addEventListener('change',countDays);
document.getElementById('endDate')?.addEventListener('change',countDays);
countDays();
</script>
<?php include APP_ROOT.'/views/layouts/footer.php';?>
