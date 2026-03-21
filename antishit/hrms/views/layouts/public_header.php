<?php
/**
 * Public Layout Header
 */
$settings = new Setting();
$companyName = $settings->get('company_name', APP_NAME);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Careers') ?> | <?= e($companyName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .public-navbar { background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .hero-section { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); color: #fff; padding: 60px 0; }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); transition: transform 0.2s; }
        .card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg public-navbar py-3 mb-0">
    <div class="container text-center">
        <a class="navbar-brand fw-bold text-primary mx-auto" href="index.php?module=jobs">
            <i class="bi bi-briefcase-fill me-2"></i><?= e($companyName) ?> - Careers
        </a>
    </div>
</nav>
<main>
    <div class="flash-container container mt-3">
        <?php $flash = getFlash(); if($flash): ?>
        <div class="alert alert-<?= $flash['type']==='error'?'danger':$flash['type'] ?> alert-dismissible fade show">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
    </div>
