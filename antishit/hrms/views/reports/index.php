<?php /** Reports Hub */
$pageTitle='Reports'; $breadcrumb=[['label'=>'Reports','active'=>true]];
include APP_ROOT.'/views/layouts/header.php';?>
<div class="container-fluid px-4 py-3">
<h5 class="fw-700 mb-4"><i class="bi bi-bar-chart-line text-primary me-2"></i>Reports</h5>
<div class="row g-3">
<?php $reports=[
    ['title'=>'Attendance Report','icon'=>'bi-clock-history','desc'=>'View and export clock-in/out records by date range and department.','url'=>'index.php?module=reports&action=attendance','color'=>'stat-indigo'],
    ['title'=>'Employee Report','icon'=>'bi-people','desc'=>'Full employee directory with positions, departments, and status.','url'=>'index.php?module=reports&action=employees','color'=>'stat-emerald'],
    ['title'=>'Payroll Report','icon'=>'bi-cash-stack','desc'=>'Payroll period summaries with gross, deductions, and net pay.','url'=>'index.php?module=reports&action=payroll','color'=>'stat-amber'],
    ['title'=>'Leave Report','icon'=>'bi-calendar-check','desc'=>'All leave requests with status, type, and approval dates.','url'=>'index.php?module=leaves','color'=>'stat-rose'],
];
foreach($reports as $r):?>
<div class="col-md-6 col-lg-3">
<a href="<?=$r['url']?>" class="text-decoration-none">
<div class="stat-card <?=$r['color']?> h-100 d-flex flex-column">
    <i class="bi <?=$r['icon']?> fs-1 mb-3 opacity-75"></i>
    <h6 class="fw-700 text-white mb-1"><?=e($r['title'])?></h6>
    <p class="small mb-0 opacity-75"><?=e($r['desc'])?></p>
</div>
</a>
</div>
<?php endforeach;?>
</div>
</div>
<?php include APP_ROOT.'/views/layouts/footer.php';?>
