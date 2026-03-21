<?php
/**
 * Forgot Password Page
 */
if (isLoggedIn()) redirect('index.php?module=dashboard&action=index');
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <title>Forgot Password | NexaHR</title>
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
        .login-body { padding: 2rem; }
        .btn-login { background: linear-gradient(135deg,#4f46e5,#7c3aed); border:none;border-radius:10px;font-weight:600;padding:.75rem;font-size:1rem;transition:all .25s; }
        .btn-login:hover { transform:translateY(-1px);box-shadow:0 8px 24px rgba(79,70,229,.4); }
    </style>
</head>
<body>
<div class="login-card card">
    <div class="login-brand">
        <div class="brand-icon"><img src="assets/images/logo.png" alt="Logo" style="width:100%;height:100%;object-fit:contain;"></div>
        <h1>NexaHR</h1>
        <p>Password Recovery</p>
    </div>
    <div class="login-body">
        <?php if ($success): ?>
            <div class="text-center py-3">
                <i class="bi bi-check-circle-fill text-success fs-1 mb-3 d-block"></i>
                <h4 class="fw-bold">Check your email</h4>
                <p class="text-muted small">We've sent a password reset link to your email address if it exists in our system.</p>
                <a href="index.php?module=auth&action=login" class="btn btn-outline-primary w-100 mt-2">Back to Login</a>
            </div>
        <?php else: ?>
            <h2 class="fw-700 mb-1" style="font-size:1.25rem">Forgot Password?</h2>
            <p class="text-muted small mb-3">Enter your email and we'll send you a link to reset your password.</p>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger py-2 small"><?= e($errors['general']) ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?module=auth&action=forgotPassword" novalidate id="forgotForm">
                <?= csrfField() ?>
                <div class="form-floating mb-4">
                    <input type="email" class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                           id="email" name="email" placeholder="you@company.com"
                           value="<?= e(post('email')) ?>" required autocomplete="email">
                    <label for="email"><i class="bi bi-envelope me-1"></i>Email address</label>
                    <?php if (!empty($errors['email'])): ?><div class="invalid-feedback"><?= e($errors['email']) ?></div><?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary btn-login w-100 text-white mb-3">
                    <span id="spinner" class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                    Send Reset Link
                </button>
                <div class="text-center">
                    <a href="index.php?module=auth&action=login" class="text-decoration-none small fw-bold"><i class="bi bi-arrow-left me-1"></i>Back to Login</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
<script>
document.getElementById('forgotForm')?.addEventListener('submit', function() {
    document.getElementById('spinner').classList.remove('d-none');
});
</script>
</body>
</html>
