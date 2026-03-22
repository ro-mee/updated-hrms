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
        :root {
            --accent-primary: #6366f1;
            --accent-secondary: #8b5cf6;
            --glass-bg: rgba(255, 255, 255, 0.82);
            --glass-border: rgba(255, 255, 255, 0.35);
            --text-main: #1e293b;
            --text-muted: #64748b;
        }
        body { 
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.7), rgba(88, 28, 135, 0.6)), url('assets/images/background.jpg') no-repeat center center fixed; 
            background-size: cover; 
            min-height: 100vh; 
            display:flex; 
            align-items:center; 
            justify-content:center; 
            font-family:'Inter', sans-serif;
            padding: 20px;
        }
        .login-card { 
            width: 100%; 
            max-width: 420px; 
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px; 
            border: 1px solid var(--glass-border); 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden; 
            animation: fadeInScale 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .login-brand { 
            background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%); 
            color: #fff; 
            padding: 3rem 2rem 2.5rem; 
            text-align: center;
            position: relative;
        }
        .login-brand::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 15px solid transparent;
            border-right: 15px solid transparent;
            border-top: 15px solid var(--accent-secondary);
        }
        .login-brand .brand-icon { 
            width: 72px; height: 72px; 
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border-radius: 18px; 
            display: flex; align-items: center; justify-content: center; 
            margin: 0 auto 1.25rem; 
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .login-brand h1 { font-size: 1.8rem; font-weight: 800; margin: 0; letter-spacing: -0.02em; }
        .login-brand p { font-size: 0.85rem; opacity: 0.9; margin: 0.4rem 0 0; font-weight: 400; }
        
        .login-body { padding: 3rem 2.25rem 2.5rem; }
        
        .form-floating > .form-control { 
            border-radius: 12px; 
            border: 1px solid #e2e8f0; 
            background: rgba(255, 255, 255, 0.5);
            padding-left: 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .form-floating > .form-control:focus { 
            background: #fff;
            border-color: var(--accent-primary); 
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12); 
        }
        .form-floating > label { padding-left: 1rem; color: var(--text-muted); }
        
        .btn-login { 
            background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%); 
            border: none; 
            border-radius: 12px; 
            font-weight: 600; 
            padding: 0.9rem; 
            font-size: 1rem; 
            letter-spacing: 0.01em;
            color: #fff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }
        .btn-login:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.4);
            filter: brightness(1.1);
        }
        .btn-login:active { transform: translateY(0); }

        .forgot-link { color: var(--accent-primary); font-weight: 600; font-size: 0.85rem; transition: color 0.2s; }
        .forgot-link:hover { color: var(--accent-secondary); }
        
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
                    <label for="email"><i class="bi bi-envelope-at me-2 text-primary opacity-50"></i>Email address</label>
                    <?php if (!empty($errors['email'])): ?><div class="invalid-feedback"><?= e($errors['email']) ?></div><?php endif; ?>
                </div>
                <div class="form-floating mb-3 position-relative">
                    <input type="password" class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                           id="password" name="password" placeholder="Password" required autocomplete="current-password">
                    <label for="password"><i class="bi bi-shield-lock me-2 text-primary opacity-50"></i>Password</label>
                    <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-2 px-2 text-muted" id="togglePw" style="z-index:10;background:none;border:none">
                        <i class="bi bi-eye" id="pwIcon"></i>
                    </button>
                    <?php if (!empty($errors['password'])): ?><div class="invalid-feedback"><?= e($errors['password']) ?></div><?php endif; ?>
                </div>
                <div class="text-end mb-4">
                    <a href="index.php?module=auth&action=forgotPassword" class="forgot-link">Forgot Password?</a>
                </div>
                <button type="submit" class="btn btn-login w-100 mb-3 shadow-lg">
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
            <div class="d-flex align-items-start mb-4 p-3 bg-primary bg-opacity-10 rounded-4 border border-primary border-opacity-10">
                <div class="text-primary me-3 mt-1" style="font-size: 1.5rem;">
                    <i class="bi bi-envelope-check-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: var(--accent-primary);">Verify it's you</h6>
                    <p class="text-muted mb-0" style="font-size: 0.8rem;">We've sent a 6-digit code to: <br><strong class="text-dark"><?= e($maskedEmail) ?></strong></p>
                </div>
            </div>

            <form method="POST" action="index.php?module=auth&action=verify2fa" novalidate id="otpForm">
                <?= csrfField() ?>
                <div class="mb-4">
                    <label for="code" class="form-label small fw-bold text-muted mb-2">Verification Code</label>
                    <input type="text" class="form-control form-control-lg text-center fw-800 letter-spacing-2" 
                           id="code" name="code" placeholder="000000" maxlength="6" required autofocus
                           style="letter-spacing: 0.6rem; font-size: 1.75rem; border-radius: 16px; border: 2px solid #e2e8f0; height: 70px;">
                </div>

                <div class="form-check form-switch mb-4 ms-1">
                    <input class="form-check-input mt-1" type="checkbox" id="trust_device" name="trust_device" value="1">
                    <label class="form-check-label small ms-2 text-muted" for="trust_device">Trust this device for 10 days</label>
                </div>

                <button type="submit" class="btn btn-login w-100 mb-3 shadow-lg">
                    <span id="otpSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                    Verify & Continue
                </button>
                <a href="index.php?module=auth&action=logout" class="back-link d-block text-center text-decoration-none">
                    Cancel & Logout
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
