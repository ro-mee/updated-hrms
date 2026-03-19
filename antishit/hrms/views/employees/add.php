<?php
/** Add Employee View */
$pageTitle  = 'Add Employee';
$breadcrumb = [['label'=>'Employees','url'=>'index.php?module=employees','active'=>false],['label'=>'Add New','active'=>true]];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
<div class="card">
    <div class="card-header py-3"><i class="bi bi-person-plus text-primary me-2"></i>Add New Employee</div>
    <div class="card-body p-4">
    <?php if (!empty($errors['general'])): ?><div class="alert alert-danger"><?= e($errors['general']) ?></div><?php endif; ?>
    <form method="POST" action="index.php?module=employees&action=add" enctype="multipart/form-data">
        <?= csrfField() ?>
        <h6 class="fw-700 text-primary mb-3 border-bottom pb-2">Account Information</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-control <?= !empty($errors['first_name'])?'is-invalid':'' ?>" value="<?= e(post('first_name')) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-control <?= !empty($errors['last_name'])?'is-invalid':'' ?>" value="<?= e(post('last_name')) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Email *</label><input type="email" name="email" class="form-control <?= !empty($errors['email'])?'is-invalid':'' ?>" value="<?= e(post('email')) ?>" required><div class="invalid-feedback"><?= e($errors['email']??'') ?></div></div>
            <div class="col-md-4">
                <label class="form-label">Password *</label>
                <input type="password" name="password" id="password" class="form-control <?= !empty($errors['password'])?'is-invalid':'' ?>" required>
                <div class="invalid-feedback"><?= e($errors['password']??'') ?></div>
                <div id="password-requirements" class="password-requirements">
                    <span class="requirement" id="req-length"><i class="bi"></i>At least 8 chars</span>
                    <span class="requirement" id="req-upper"><i class="bi"></i>One uppercase</span>
                    <span class="requirement" id="req-lower"><i class="bi"></i>One lowercase</span>
                    <span class="requirement" id="req-number"><i class="bi"></i>One number</span>
                    <span class="requirement" id="req-special"><i class="bi"></i>One special char</span>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">System Role *</label>
                <select name="role_id" class="form-select" required>
                    <?php foreach($roles as $r): ?><option value="<?=$r['id']?>" <?= post('role_id')==$r['id']?'selected':'' ?>><?= e($r['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
        </div>
        <h6 class="fw-700 text-primary mb-3 border-bottom pb-2">Employment Details</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Department *</label>
                <select name="department_id" class="form-select" id="deptSelect" required>
                    <option value="">-- Select --</option>
                    <?php foreach($departments as $d): ?><option value="<?=$d['id']?>" <?= post('department_id')==$d['id']?'selected':'' ?>><?= e($d['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Position *</label>
                <select name="position_id" class="form-select" id="posSelect" required>
                    <option value="">-- Select Department First --</option>
                    <?php foreach($positions as $p): ?><option value="<?=$p['id']?>" <?= post('position_id')==$p['id']?'selected':'' ?>><?= e($p['title']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Direct Manager</label>
                <select name="manager_id" class="form-select">
                    <option value="">-- None --</option>
                    <?php foreach($managers as $m): ?><option value="<?=$m['id']?>" <?= post('manager_id')==$m['id']?'selected':'' ?>><?= e($m['full_name']) ?> (<?= e($m['dept']) ?>)</option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3"><label class="form-label">Employment Type</label><select name="employment_type" class="form-select"><?php foreach(['full_time'=>'Full Time','part_time'=>'Part Time','contract'=>'Contract','intern'=>'Intern'] as $v=>$l): ?><option value="<?=$v?>" <?= post('employment_type')===$v?'selected':'' ?>><?=$l?></option><?php endforeach; ?></select></div>
            <div class="col-md-3"><label class="form-label">Date Hired *</label><input type="date" name="date_hired" class="form-control" value="<?= e(post('date_hired',date('Y-m-d'))) ?>" required></div>
            <div class="col-md-3"><label class="form-label">Date Regularized</label><input type="date" name="date_regularized" class="form-control" value="<?= e(post('date_regularized')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Basic Salary (₱)</label><input type="number" name="basic_salary" class="form-control" min="0" step="0.01" value="<?= e(post('basic_salary',0)) ?>" required></div>
        </div>
        <h6 class="fw-700 text-primary mb-3 border-bottom pb-2">Personal Information</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?= e(post('phone')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Birth Date</label><input type="date" name="birth_date" class="form-control" value="<?= e(post('birth_date')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">--</option><option value="male">Male</option><option value="female">Female</option><option value="prefer_not_to_say">Prefer not to say</option></select></div>
            <div class="col-md-3"><label class="form-label">Civil Status</label><select name="civil_status" class="form-select"><option value="">--</option><option value="single">Single</option><option value="married">Married</option><option value="widowed">Widowed</option><option value="divorced">Divorced</option></select></div>
            <div class="col-md-6"><label class="form-label">Address</label><input type="text" name="address" class="form-control" value="<?= e(post('address')) ?>"></div>
            <div class="col-md-3"><label class="form-label">City</label><input type="text" name="city" class="form-control" value="<?= e(post('city')) ?>"></div>
        </div>
        <h6 class="fw-700 text-primary mb-3 border-bottom pb-2">Government IDs</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-3"><label class="form-label">SSS Number</label><input type="text" name="sss_number" class="form-control" value="<?= e(post('sss_number')) ?>"></div>
            <div class="col-md-3"><label class="form-label">PhilHealth No.</label><input type="text" name="philhealth_number" class="form-control" value="<?= e(post('philhealth_number')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Pag-IBIG No.</label><input type="text" name="pagibig_number" class="form-control" value="<?= e(post('pagibig_number')) ?>"></div>
            <div class="col-md-3"><label class="form-label">TIN Number</label><input type="text" name="tin_number" class="form-control" value="<?= e(post('tin_number')) ?>"></div>
        </div>
        <h6 class="fw-700 text-primary mb-3 border-bottom pb-2">Emergency Contact</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6"><label class="form-label">Contact Name</label><input type="text" name="emergency_contact_name" class="form-control" value="<?= e(post('emergency_contact_name')) ?>"></div>
            <div class="col-md-6"><label class="form-label">Contact Phone</label><input type="text" name="emergency_contact_phone" class="form-control" value="<?= e(post('emergency_contact_phone')) ?>"></div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Employee</button>
            <a href="index.php?module=employees" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
    </div>
</div>
</div>
<script>
// Dynamic position dropdown
document.getElementById('deptSelect')?.addEventListener('change', function() {
    const deptId = this.value;
    fetch('index.php?module=employees&action=positions&department_id=' + deptId)
        .then(r=>r.json()).then(positions => {
            const sel = document.getElementById('posSelect');
            sel.innerHTML = '<option value="">-- Select Position --</option>';
            positions.forEach(p => sel.innerHTML += `<option value="${p.id}">${p.title}</option>`);
        });
});

document.getElementById('password')?.addEventListener('input', function() {
    const pw = this.value;
    const requirements = [
        { id: 'req-length',  met: pw.length >= 8 },
        { id: 'req-upper',   met: /[A-Z]/.test(pw) },
        { id: 'req-lower',   met: /[a-z]/.test(pw) },
        { id: 'req-number',  met: /[0-9]/.test(pw) },
        { id: 'req-special', met: /[\W_]/.test(pw) }
    ];
    requirements.forEach(req => {
        const el = document.getElementById(req.id);
        if (el) {
            el.classList.toggle('valid', req.met);
            el.classList.toggle('invalid', !req.met && pw.length > 0);
        }
    });
});
</script>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
