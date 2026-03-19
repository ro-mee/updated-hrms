<?php
/**
 * Main Layout Wrapper
 * Usage: $pageTitle, $module are set before including this
 */
$user     = currentUser();
$notifCount = (new Notification())->unreadCount($user['id']);
$flash    = getFlash();
$settings = new Setting();
$companyName = $settings->get('company_name', APP_NAME);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="<?= isset($_COOKIE['hrms_theme']) && $_COOKIE['hrms_theme']==='dark' ? 'dark' : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($companyName) ?> - Human Resource Management System">
    <title><?= e($pageTitle ?? 'Dashboard') ?> | <?= e($companyName) ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= APP_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php 
$sidebarCollapsed = isset($_COOKIE['hrms_sidebar_collapsed']) && $_COOKIE['hrms_sidebar_collapsed'] === '1';
?>
<!-- ── Sidebar ────────────────────────────────────────────────── -->
<div class="sidebar <?= $sidebarCollapsed ? 'collapsed' : '' ?>" id="sidebar">
    <?php include APP_ROOT . '/views/layouts/sidebar.php'; ?>
</div>

<!-- ── Main Wrapper ─────────────────────────────────────────── -->
<div class="main-wrapper <?= $sidebarCollapsed ? 'expanded' : '' ?>" id="mainWrapper">
    <!-- Topbar -->
    <nav class="topbar navbar navbar-expand px-3">
        <button class="btn btn-link sidebar-toggle me-2" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="bi bi-list fs-4"></i>
        </button>
        <nav aria-label="breadcrumb" class="d-none d-sm-block">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php?module=dashboard">Home</a></li>
                <?php if (!empty($breadcrumb)): foreach ($breadcrumb as $bc): ?>
                <li class="breadcrumb-item <?= (!empty($bc['active'])) ? 'active' : '' ?>">
                    <?= (!empty($bc['active'])) ? e($bc['label']) : '<a href="'.e($bc['url'] ?? '#').'">'.e($bc['label']).'</a>' ?>
                </li>
                <?php endforeach; endif; ?>
            </ol>
        </nav>
        <div class="ms-auto d-flex align-items-center gap-3">
            <!-- Dark mode toggle -->
            <button class="btn btn-ghost-sm theme-toggle" id="themeToggle" title="Toggle theme">
                <i class="bi bi-moon-stars fs-5"></i>
            </button>
            <!-- Notifications -->
            <div class="dropdown">
                <button class="btn btn-ghost-sm position-relative" data-bs-toggle="dropdown" id="notifBell">
                    <i class="bi bi-bell fs-5"></i>
                    <?php if ($notifCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notif-badge"><?= $notifCount ?></span>
                    <?php endif; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end notif-dropdown p-0" style="min-width:320px">
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                        <strong>Notifications</strong>
                        <a href="index.php?module=notifications" class="small text-primary">View all</a>
                    </div>
                    <div id="notifList" style="max-height:300px;overflow-y:auto">
                        <?php
                        $recentNotifs = (new Notification())->forUser(currentUserId(), false, 5);
                        if (empty($recentNotifs)): ?>
                            <div class="text-center text-muted py-4 small"><i class="bi bi-bell-slash fs-4 d-block mb-1"></i>No recent notifications.</div>
                        <?php else: ?>
                            <?php foreach($recentNotifs as $n): ?>
                            <a class="dropdown-item px-3 py-2 <?= $n['is_read'] ? 'bg-transparent text-muted' : 'bg-light' ?> border-bottom" href="index.php?module=notifications&action=read&id=<?= $n['id'] ?>" style="white-space: normal;">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-<?= $n['type'] ?> p-1 rounded-circle" style="width:10px;height:10px;"></span>
                                    <span class="fw-bold small"><?= e($n['title']) ?></span>
                                </div>
                                <div class="small lh-sm text-truncate text-secondary mb-1"><?= e($n['message']) ?></div>
                                <div class="text-secondary" style="font-size: 0.7rem;"><i class="bi bi-clock me-1"></i><?= timeAgo($n['created_at']) ?></div>
                            </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- User Menu -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle user-pill" data-bs-toggle="dropdown">
                    <img src="<?= avatarUrl($user['avatar']) ?>" class="rounded-circle me-2" width="32" height="32" alt="avatar" style="object-fit:cover">
                    <span class="d-none d-md-block fw-medium"><?= e($user['first_name']) ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><h6 class="dropdown-header"><?= e($user['full_name']) ?><br><small class="text-muted"><?= e($user['role_name']) ?></small></h6></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li><a class="dropdown-item" href="index.php?module=profile"><i class="bi bi-person me-2"></i>My Profile</a></li>
                    <li><a class="dropdown-item" href="index.php?module=auth&action=changePassword"><i class="bi bi-key me-2"></i>Change Password</a></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form method="POST" action="index.php?module=auth&action=logout">
                            <?= csrfField() ?>
                            <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="page-content" id="pageContent">
        <!-- Flash messages -->
        <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type']==='error'?'danger':$flash['type'] ?> alert-dismissible d-flex align-items-center mx-4 mt-3" role="alert">
            <i class="bi bi-<?= $flash['type']==='success'?'check-circle':'exclamation-triangle' ?>-fill me-2"></i>
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <!-- Content injected here -->
