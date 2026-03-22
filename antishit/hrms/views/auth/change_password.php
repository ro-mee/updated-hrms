<?php
/**
 * Change Password Page
 */
$pageTitle  = 'Change Password';
$breadcrumb = [['label'=>'Change Password','active'=>true]];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0"><i class="bi bi-key me-2 text-primary"></i>Change Password</h5>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger py-2">
                        <?php foreach($errors as $e): ?><div><?= e($e) ?></div><?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <form method="POST" action="index.php?module=auth&action=changePassword">
                        <?= csrfField() ?>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">New Password</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" required>
                            <div id="password-requirements" class="password-requirements">
                                <span class="requirement" id="req-length"><i class="bi"></i>At least 8 chars</span>
                                <span class="requirement" id="req-upper"><i class="bi"></i>One uppercase</span>
                                <span class="requirement" id="req-lower"><i class="bi"></i>One lowercase</span>
                                <span class="requirement" id="req-number"><i class="bi"></i>One number</span>
                                <span class="requirement" id="req-special"><i class="bi"></i>One special char</span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-medium">Confirm New Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                            <div id="match-feedback" class="form-text mt-1 d-none"></div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update Password</button>
                            <a href="index.php?module=profile" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function validatePassword() {
    const pw = document.getElementById('new_password').value;
    const confirm = document.getElementById('confirm_password').value;
    const feedback = document.getElementById('match-feedback');
    const confirmInput = document.getElementById('confirm_password');
    const submitBtn = document.querySelector('button[type="submit"]');

    // Strength validation
    const requirements = [
        { id: 'req-length',  met: pw.length >= 8 },
        { id: 'req-upper',   met: /[A-Z]/.test(pw) },
        { id: 'req-lower',   met: /[a-z]/.test(pw) },
        { id: 'req-number',  met: /[0-9]/.test(pw) },
        { id: 'req-special', met: /[\W_]/.test(pw) }
    ];
    let allMet = true;
    requirements.forEach(req => {
        const el = document.getElementById(req.id);
        if (el) {
            el.classList.toggle('valid', req.met);
            el.classList.toggle('invalid', !req.met && pw.length > 0);
            if (!req.met) allMet = false;
        }
    });

    // Match validation
    if (confirm.length > 0) {
        feedback.classList.remove('d-none');
        if (pw === confirm) {
            feedback.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Passwords match';
            feedback.className = 'form-text mt-1 text-success small';
            confirmInput.classList.remove('is-invalid');
            confirmInput.classList.add('is-valid');
        } else {
            feedback.innerHTML = '<i class="bi bi-exclamation-circle-fill me-1"></i>Passwords do not match';
            feedback.className = 'form-text mt-1 text-danger small';
            confirmInput.classList.remove('is-valid');
            confirmInput.classList.add('is-invalid');
            allMet = false;
        }
    } else {
        feedback.classList.add('d-none');
        confirmInput.classList.remove('is-valid', 'is-invalid');
    }

    // Enable/Disable submit
    submitBtn.disabled = !allMet || confirm !== pw || pw.length === 0;
}

document.getElementById('new_password')?.addEventListener('input', validatePassword);
document.getElementById('confirm_password')?.addEventListener('input', validatePassword);
</script>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
