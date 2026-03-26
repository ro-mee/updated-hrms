<?php
/**
 * Sidebar Navigation (role-based)
 */
$role     = currentRole();
$user     = currentUser();
$curModule= preg_replace('/[^a-z_]/','',strtolower($_GET['module']??'dashboard'));

// Nav items definition: [module, label, icon, action(optional), roles(optional-fallback)]
$navItems = [
    ['module'=>'dashboard',    'label'=>'Dashboard',      'icon'=>'bi-speedometer2'],
    // ── HR Core ──
    ['section'=>'HR Management'],
    ['module'=>'employees',    'label'=>'Employees',       'icon'=>'bi-people',          'perm'=>'employees.view'],
    ['module'=>'attendance',   'label'=>'Attendance',      'icon'=>'bi-clock-history',   'perm'=>'attendance.manage'],
    ['module'=>'attendance',   'label'=>'My Attendance',   'icon'=>'bi-clock',           'action'=>'my', 'perm'=>'attendance.self'],
    ['module'=>'leaves',       'label'=>'Leave Management','icon'=>'bi-calendar-check',  'perm'=>'leaves.manage'],
    ['module'=>'leaves',       'label'=>'My Leaves',       'icon'=>'bi-calendar2-heart', 'action'=>'my', 'perm'=>'leaves.self'],
    // ── Finance ──
    ['section'=>'Finance'],
    ['module'=>'payroll',      'label'=>'Payroll',         'icon'=>'bi-cash-stack',      'perm'=>'payroll.manage'],
    ['module'=>'payroll',      'label'=>'My Payslips',     'icon'=>'bi-receipt',         'action'=>'myPayslips', 'perm'=>'payroll.self'],
    // ── Recruitment ──
    ['section'=>'Recruitment'],
    ['module'=>'recruitment',  'label'=>'Jobs & ATS',      'icon'=>'bi-briefcase',       'perm'=>'recruitment.manage'],
    // ── Growth ──
    ['section'=>'Growth'],
    ['module'=>'performance',  'label'=>'Performance',     'icon'=>'bi-graph-up-arrow',  'perm'=>'performance.review'],
    ['module'=>'performance',  'label'=>'My Performance',  'icon'=>'bi-graph-up',        'action'=>'my', 'perm'=>'performance.self'],
    ['module'=>'training',     'label'=>'Training',        'icon'=>'bi-mortarboard',     'perm'=>'training.view'],
    // ── Documents ──
    ['section'=>'Documents'],
    ['module'=>'documents',    'label'=>'Documents',       'icon'=>'bi-folder2-open',    'perm'=>'documents.manage'],
    ['module'=>'documents',    'label'=>'My Documents',    'icon'=>'bi-folder',          'action'=>'my', 'perm'=>'documents.self'],
    // ── Reports ──
    ['section'=>'Reports & Admin'],
    ['module'=>'reports',      'label'=>'Reports',         'icon'=>'bi-bar-chart-line',  'perm'=>'reports.view'],
    ['module'=>'audit',        'label'=>'Audit Logs',      'icon'=>'bi-shield-check',    'perm'=>'audit.view'],
    ['module'=>'roles',        'label'=>'Manage Roles',    'icon'=>'bi-shield-lock',     'perm'=>'users.manage_roles'],
    ['module'=>'settings',     'label'=>'Settings',        'icon'=>'bi-gear',            'perm'=>'settings.manage'],
];

$settings = new Setting();
$companyName = $settings->get('company_name','NexaHR');
?>
<div class="sidebar-header">
    <div class="brand-logo">
        <div class="brand-icon-box"><img src="assets/images/favicon.png" alt="Logo" style="width:100%;height:100%;object-fit:contain;"></div>
        <div class="brand-text">
            <span class="brand-name"><?= e($companyName) ?></span>
            <span class="brand-sub">HRMS</span>
        </div>
    </div>
    <button class="btn-close-sidebar d-lg-none" id="closeSidebar"><i class="bi bi-x-lg"></i></button>
</div>

<!-- User quick info -->
<div class="sidebar-user">
    <img src="<?= avatarUrl($user['avatar']) ?>" class="rounded-circle" width="38" height="38" alt="me" style="object-fit:cover;border:2px solid var(--hrms-sidebar-border)">
    <div class="ms-2 overflow-hidden">
        <div class="sidebar-user-name"><?= e($user['full_name']) ?></div>
        <div class="sidebar-user-role"><?= e($user['role_name']) ?></div>
    </div>
</div>

<nav class="sidebar-nav">
    <ul class="nav flex-column">
    <?php foreach ($navItems as $index => $item): ?>
        <?php 
        if (isset($item['section'])) {
            // Check if any subsequent items (until next section) are accessible
            $showSection = false;
            for ($j = $index + 1; $j < count($navItems); $j++) {
                if (isset($navItems[$j]['section'])) break;
                $m = $navItems[$j]['module'];
                $p = $navItems[$j]['perm'] ?? "$m.view";
                $pp = explode('.', $p);
                if (can($pp[0], $pp[1] ?? 'view')) {
                    $showSection = true;
                    break;
                }
            }
            if (!$showSection) continue;
        } else {
            $mod    = $item['module'];
            $action = $item['action'] ?? 'view'; // Default sidebar action is view
            $perm   = $item['perm'] ?? "$mod.view";
            
            // Split perm into mod.act
            $pParts = explode('.', $perm);
            if (!can($pParts[0], $pParts[1] ?? 'view')) continue;
        }
        
        if (isset($item['section'])): 
        ?>
            <li class="nav-section"><?= e($item['section']) ?></li>
        <?php else:
            $mod    = $item['module'];
            $action = $item['action'] ?? 'index';
            $href   = "index.php?module=$mod&action=$action";
            $curAction = strtolower($_GET['action'] ?? 'index');
            
            // Refined active logic: 
            // 1. My actions (my, myPayslips) only highlight their specific links.
            // 2. Other actions (index, add, edit, etc.) highlight the main module link.
            $isMyAction = in_array($curAction, ['my', 'mypayslips']);
            if ($curModule === $mod) {
                if ($isMyAction) {
                    $active = (strtolower($action) === $curAction);
                } else {
                    $active = ($action === 'index');
                }
            } else {
                $active = false;
            }
        ?>
            <li class="nav-item">
                <a href="<?= $href ?>" class="nav-link <?= $active ? 'active' : '' ?>">
                    <i class="bi <?= $item['icon'] ?> nav-icon"></i>
                    <span><?= e($item['label']) ?></span>
                    <?php if ($mod==='leaves' && $action === 'index' && !hasRole(ROLE_EMPLOYEE)):
                        $lModel = new Leave();
                        $lFilters = ['status' => 'pending'];
                        if (hasRole(ROLE_DEPT_MANAGER)) {
                            $lFilters['department_id'] = currentUser()['employee_id'] ? (new Employee())->findById(currentUser()['employee_id'])['department_id'] ?? -1 : -1;
                        }
                        $pend = $lModel->count($lFilters);
                        if($pend>0): ?><span class="nav-badge"><?=$pend?></span><?php endif;
                    endif; ?>
                </a>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
    </ul>
</nav>
<div class="sidebar-footer">
    <form method="POST" action="index.php?module=auth&action=logout" onsubmit="localStorage.setItem('hrms-logout-event', Date.now());">
        <?= csrfField() ?>
        <button class="btn-sidebar-logout w-100 d-flex align-items-center justify-content-center">
            <i class="bi bi-box-arrow-right me-2"></i><span>Logout</span>
        </button>
    </form>
</div>
