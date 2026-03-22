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

        .back-link { color: var(--text-muted); font-weight: 500; font-size: 0.85rem; transition: color 0.2s; }
        .back-link:hover { color: var(--accent-primary); }
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
            <div class="text-center py-2">
                <div class="mb-4 mx-auto d-flex align-items-center justify-content-center" style="width:64px;height:64px;background:rgba(16, 185, 129, 0.1);color:#10b981;border-radius:50%">
                    <i class="bi bi-envelope-check-fill fs-3"></i>
                </div>
                <h4 class="fw-bold mb-2">Check your email</h4>
                <p class="text-muted small mb-4">We've sent a password reset link to your email address if it exists in our system.</p>
                <a href="index.php?module=auth&action=login" class="btn btn-login w-100">Back to Login</a>
            </div>
        <?php else: ?>
            <h2 class="fw-800 mb-1" style="font-size:1.35rem; letter-spacing:-0.01em">Forgot Password?</h2>
            <p class="text-muted small mb-4">Enter your email and we'll send you a link to reset your password.</p>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger py-2 small border-0 bg-danger bg-opacity-10 text-danger mb-4"><?= e($errors['general']) ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?module=auth&action=forgotPassword" novalidate id="forgotForm">
                <?= csrfField() ?>
                <div class="form-floating mb-4">
                    <input type="email" class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                           id="email" name="email" placeholder="you@company.com"
                           value="<?= e(post('email')) ?>" required autocomplete="email">
                    <label for="email"><i class="bi bi-envelope-at me-2 text-primary opacity-50"></i>Email address</label>
                    <?php if (!empty($errors['email'])): ?><div class="invalid-feedback"><?= e($errors['email']) ?></div><?php endif; ?>
                </div>
                <button type="submit" class="btn btn-login w-100 mb-4 shadow-lg">
                    <span id="spinner" class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                    Send Reset Link
                </button>
                <div class="text-center">
                    <a href="index.php?module=auth&action=login" class="back-link"><i class="bi bi-arrow-left me-1"></i>Back to Login</a>
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
