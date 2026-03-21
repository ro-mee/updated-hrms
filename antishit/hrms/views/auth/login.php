<?php
/**
 * Login Page
 * Accessible at: http://localhost/antishit/hrms/
 */
if (isLoggedIn()) redirect('index.php?module=dashboard&action=index');
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | NexaHR</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(rgba(30, 27, 75, 0.4), rgba(30, 27, 75, 0.4)), url('assets/images/background.jpg') no-repeat center center fixed; 
            background-size: cover; 
            min-height: 100vh; 
            display:flex; 
            align-items:center; 
            justify-content:center; 
            font-family:'Inter',sans-serif; 
        }
        .login-card { width: 100%; max-width: 440px; border-radius: 20px; border:none; box-shadow: 0 24px 64px rgba(0,0,0,0.14); overflow:hidden; }
        .login-brand { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color:#fff; padding: 2.5rem 2rem 2rem; text-align:center; }
        .login-brand .brand-icon { width:64px;height:64px;background:transparent;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:2rem; }
        .login-brand h1 { font-size:1.6rem;font-weight:700;margin:0; }
        .login-brand p  { font-size:.85rem;opacity:.8;margin:.3rem 0 0; }
        .login-body { padding: 2rem; }
        .form-floating .form-control { border-radius: 10px; border-color: #e2e8f0; font-size:.95rem; }
        .form-floating .form-control:focus { border-color:#4f46e5;box-shadow:0 0 0 .2rem rgba(79,70,229,.15); }
        .btn-login { background: linear-gradient(135deg,#4f46e5,#7c3aed); border:none;border-radius:10px;font-weight:600;padding:.75rem;font-size:1rem;letter-spacing:.4px;transition:all .25s; }
        .btn-login:hover { transform:translateY(-1px);box-shadow:0 8px 24px rgba(79,70,229,.4); }
        .divider-text { position:relative;text-align:center;color:#94a3b8;font-size:.78rem;margin:1.2rem 0; }
        .divider-text::before,.divider-text::after { content:'';position:absolute;top:50%;width:42%;height:1px;background:#e2e8f0; }
        .divider-text::before { left:0; } .divider-text::after { right:0; }
        .demo-creds { background:#f8faff;border:1px solid #e0e7ff;border-radius:10px;padding:1rem;font-size:.78rem;color:#4338ca; }
        .demo-creds strong { color:#3730a3; }
    </style>
</head>
<body>
<div class="login-card card">
    <div class="login-brand">
        <div class="brand-icon"><img src="assets/images/logo.png" alt="Logo" style="width:100%;height:100%;object-fit:contain;"></div>
        <h1>NexaHR</h1>
        <p>Human Resource Management System</p>
    </div>
    <div class="login-body">
        <?php if (!isset($_SESSION['pending_2fa_user_id'])): ?>
            <h2 class="fw-700 mb-1" style="font-size:1.25rem">Welcome back 👋</h2>
            <p class="text-muted small mb-3">Sign in to your account to continue</p>
        <?php else: ?>
            <h2 class="fw-700 mb-1" style="font-size:1.25rem">2-step verification</h2>
            <p class="text-muted small mb-3">To keep your account safe, we need to check that this is really you.</p>
        <?php endif; ?>

        <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger d-flex align-items-center py-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <span><?= e($errors['general']) ?></span>
        </div>
        <?php endif; ?>

        <?php if (!isset($_SESSION['pending_2fa_user_id'])): ?>
            <!-- Login Form -->
            <form method="POST" action="index.php?module=auth&action=login" novalidate id="loginForm">
                <?= csrfField() ?>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                           id="email" name="email" placeholder="you@company.com"
                           value="<?= e(post('email')) ?>" required autocomplete="email">
                    <label for="email"><i class="bi bi-envelope me-1"></i>Email address</label>
                    <?php if (!empty($errors['email'])): ?><div class="invalid-feedback"><?= e($errors['email']) ?></div><?php endif; ?>
                </div>
                <div class="form-floating mb-3 position-relative">
                    <input type="password" class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                           id="password" name="password" placeholder="Password" required autocomplete="current-password">
                    <label for="password"><i class="bi bi-lock me-1"></i>Password</label>
                    <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-2 px-2 text-muted" id="togglePw" style="z-index:10;background:none;border:none">
                        <i class="bi bi-eye" id="pwIcon"></i>
                    </button>
                    <?php if (!empty($errors['password'])): ?><div class="invalid-feedback"><?= e($errors['password']) ?></div><?php endif; ?>
                </div>
                <div class="text-end mb-3">
                    <a href="index.php?module=auth&action=forgotPassword" class="text-decoration-none small fw-bold">Forgot Password?</a>
                </div>
                <button type="submit" class="btn btn-primary btn-login w-100 text-white mb-3">
                    <span id="loginSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                    Sign In
                </button>
            </form>
        <?php else: 
            // 2FA Form
            $pendingEmail = $user['email'] ?? 'your email';
            $parts = explode('@', $pendingEmail);
            $name = $parts[0];
            $domain = $parts[1] ?? '';
            $maskedEmail = substr($name, 0, 1) . str_repeat('•', min(12, strlen($name)-1)) . substr($name, -1, 1) . '@' . $domain;
        ?>
            <div class="d-flex align-items-start mb-4 p-3 bg-light rounded-3 border">
                <div class="text-primary me-3 mt-1" style="font-size: 1.5rem;">
                    <i class="bi bi-envelope-check-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">Verify it's you by email</h6>
                    <p class="text-muted mb-0" style="font-size: 0.8rem;">We've just sent a 6-digit code to: <br><strong><?= e($maskedEmail) ?></strong></p>
                </div>
            </div>

            <form method="POST" action="index.php?module=auth&action=verify2fa" novalidate id="otpForm">
                <?= csrfField() ?>
                <div class="mb-4">
                    <label for="code" class="form-label small fw-bold text-muted">Enter 6-digit code</label>
                    <input type="text" class="form-control form-control-lg text-center fw-bold letter-spacing-2" 
                           id="code" name="code" placeholder="000000" maxlength="6" required autofocus
                           style="letter-spacing: 0.5rem; font-size: 1.5rem; border-radius: 12px;">
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="trust_device" name="trust_device" value="1">
                    <label class="form-check-label small ms-2" for="trust_device">Trust this device for 10 days.</label>
                </div>

                <button type="submit" class="btn btn-primary btn-login w-100 text-white mb-3">
                    <span id="otpSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                    Continue
                </button>
                <a href="index.php?module=auth&action=logout" class="btn btn-link w-100 text-muted text-decoration-none small">
                    Cancel login
                </a>
            </form>
        <?php endif; ?>

        
        <p class="text-center text-muted mt-3 mb-0" style="font-size:.73rem">
            &copy; <?= date('Y') ?> NexaHR. All rights reserved.
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Password toggle
const togglePw = document.getElementById('togglePw');
if (togglePw) {
    togglePw.addEventListener('click', function() {
        const pw = document.getElementById('password');
        const icon = document.getElementById('pwIcon');
        if (pw.type === 'password') {
            pw.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            pw.type = 'password';
            icon.className = 'bi bi-eye';
        }
    });
}

// Show spinner on submit
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', function() {
        const spinner = document.getElementById('loginSpinner');
        if (spinner) spinner.classList.remove('d-none');
    });
}

const otpForm = document.getElementById('otpForm');
if (otpForm) {
    otpForm.addEventListener('submit', function() {
        const spinner = document.getElementById('otpSpinner');
        if (spinner) spinner.classList.remove('d-none');
    });
}
</script>
</body>
</html>
