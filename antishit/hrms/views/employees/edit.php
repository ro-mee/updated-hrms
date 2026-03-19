<?php
/** Employee Edit View */
$pageTitle  = 'Edit Employee: ' . e($employee['full_name']);
$breadcrumb = [['label'=>'Employees','url'=>'index.php?module=employees','active'=>false],['label'=>'Edit','active'=>true]];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
<div class="card">
    <div class="card-header py-3"><i class="bi bi-pencil text-primary me-2"></i>Edit Employee: <strong><?= e($employee['full_name']) ?></strong></div>
    <div class="card-body p-4">
    <?php if(!empty($errors)):?><div class="alert alert-danger py-2"><?php foreach($errors as $e): ?><div><?=e($e)?></div><?php endforeach;?></div><?php endif;?>
    <form method="POST" action="index.php?module=employees&action=edit&id=<?= $employee['id'] ?>">
        <?= csrfField() ?>
        <h6 class="fw-700 text-primary mb-3 border-bottom pb-2">Account</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-control" value="<?= e($employee['first_name']) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-control" value="<?= e($employee['last_name']) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" value="<?= e($employee['email']) ?>" required></div>
            <?php if(hasRole(ROLE_SUPER_ADMIN)):?>
            <div class="col-md-4"><label class="form-label">System Role</label><select name="role_id" class="form-select"><?php foreach($roles as $r):?><option value="<?=$r['id']?>" <?=$employee['role_id']==$r['id']?'selected':''?>><?=e($r['name'])?></option><?php endforeach;?></select></div>
            <?php endif;?>
        </div>
        <h6 class="fw-700 text-primary mb-3 border-bottom pb-2">Employment</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><label class="form-label">Department</label><select name="department_id" class="form-select"><?php foreach($departments as $d):?><option value="<?=$d['id']?>" <?=$employee['department_id']==$d['id']?'selected':''?>><?=e($d['name'])?></option><?php endforeach;?></select></div>
            <div class="col-md-4"><label class="form-label">Position</label><select name="position_id" class="form-select"><?php foreach($positions as $p):?><option value="<?=$p['id']?>" <?=$employee['position_id']==$p['id']?'selected':''?>><?=e($p['title'])?></option><?php endforeach;?></select></div>
            <div class="col-md-4"><label class="form-label">Manager</label><select name="manager_id" class="form-select"><option value="">None</option><?php foreach($managers as $m):?><option value="<?=$m['id']?>" <?=$employee['manager_id']==$m['id']?'selected':''?>><?=e($m['full_name'])?></option><?php endforeach;?></select></div>
            <div class="col-md-3"><label class="form-label">Employment Type</label><select name="employment_type" class="form-select"><?php foreach(['full_time'=>'Full Time','part_time'=>'Part Time','contract'=>'Contract','intern'=>'Intern'] as $v=>$l):?><option value="<?=$v?>" <?=$employee['employment_type']===$v?'selected':''?>><?=$l?></option><?php endforeach;?></select></div>
            <div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><?php foreach(['active','inactive','resigned','terminated','on_leave'] as $s):?><option value="<?=$s?>" <?=$employee['status']===$s?'selected':''?>><?=ucwords(str_replace('_',' ',$s))?></option><?php endforeach;?></select></div>
            <div class="col-md-3"><label class="form-label">Date Hired</label><input type="date" name="date_hired" class="form-control" value="<?=e($employee['date_hired'])?>"></div>
            <?php if(can('payroll','view')):?><div class="col-md-3"><label class="form-label">Basic Salary (₱)</label><input type="number" name="basic_salary" class="form-control" step="0.01" value="<?=e($employee['basic_salary'])?>"></div><?php endif;?>
        </div>
        <h6 class="fw-700 text-primary mb-3 border-bottom pb-2">Personal</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?=e($employee['phone'])?>"></div>
            <div class="col-md-3"><label class="form-label">Birth Date</label><input type="date" name="birth_date" class="form-control" value="<?=e($employee['birth_date'])?>"></div>
            <div class="col-md-3"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">--</option><?php foreach(['male'=>'Male','female'=>'Female','prefer_not_to_say'=>'Prefer not to say'] as $v=>$l):?><option value="<?=$v?>" <?=$employee['gender']===$v?'selected':''?>><?=$l?></option><?php endforeach;?></select></div>
            <div class="col-md-3"><label class="form-label">Civil Status</label><select name="civil_status" class="form-select"><?php foreach(['single'=>'Single','married'=>'Married','widowed'=>'Widowed','divorced'=>'Divorced'] as $v=>$l):?><option value="<?=$v?>" <?=$employee['civil_status']===$v?'selected':''?>><?=$l?></option><?php endforeach;?></select></div>
            <div class="col-md-6"><label class="form-label">Address</label><input type="text" name="address" class="form-control" value="<?=e($employee['address'])?>"></div>
            <div class="col-md-3"><label class="form-label">City</label><input type="text" name="city" class="form-control" value="<?=e($employee['city'])?>"></div>
        </div>
        <h6 class="fw-700 text-primary mb-3 border-bottom pb-2">Government IDs</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-3"><label class="form-label">SSS</label><input type="text" name="sss_number" class="form-control" value="<?=e($employee['sss_number'])?>"></div>
            <div class="col-md-3"><label class="form-label">PhilHealth</label><input type="text" name="philhealth_number" class="form-control" value="<?=e($employee['philhealth_number'])?>"></div>
            <div class="col-md-3"><label class="form-label">Pag-IBIG</label><input type="text" name="pagibig_number" class="form-control" value="<?=e($employee['pagibig_number'])?>"></div>
            <div class="col-md-3"><label class="form-label">TIN</label><input type="text" name="tin_number" class="form-control" value="<?=e($employee['tin_number'])?>"></div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6"><label class="form-label">Emergency Contact Name</label><input type="text" name="emergency_contact_name" class="form-control" value="<?=e($employee['emergency_contact_name'])?>"></div>
            <div class="col-md-6"><label class="form-label">Emergency Contact Phone</label><input type="text" name="emergency_contact_phone" class="form-control" value="<?=e($employee['emergency_contact_phone'])?>"></div>
            <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="3"><?=e($employee['notes'])?></textarea></div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
            <a href="index.php?module=employees&action=view&id=<?= $employee['id'] ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
    </div>
</div>
</div>
<?php include APP_ROOT.'/views/layouts/footer.php';?>
