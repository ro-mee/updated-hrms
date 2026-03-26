<?php
/**
 * Dashboard View (role-based)
 */
$role = currentRole();
$pageTitle  = 'Dashboard';
$breadcrumb = [['label'=>'Dashboard','active'=>true]];
include APP_ROOT . '/views/layouts/header.php';
?>
<style>
.report-card { border-radius: 8px; border: 1px solid var(--hrms-card-border); background: var(--hrms-card-bg); box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
.report-card-title { font-size: 0.85rem; font-weight: 600; color: var(--hrms-text-main); }
.report-card-subtitle { font-size: 0.75rem; color: var(--hrms-text-muted); margin-bottom: 1rem; }
.apexcharts-legend-text { color: var(--hrms-text-main) !important; }
.apexcharts-text tspan { fill: var(--hrms-text-muted); }
.apexcharts-tooltip { background: var(--hrms-card-bg) !important; border-color: var(--hrms-card-border) !important; color: var(--hrms-text-main) !important; box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; }
.apexcharts-tooltip-title { background: rgba(0,0,0,0.03) !important; border-bottom: 1px solid var(--hrms-card-border) !important; }
.apexcharts-menu { background: var(--hrms-card-bg) !important; border-color: var(--hrms-card-border) !important; color: var(--hrms-text-main) !important; }
</style>
<div class="container-fluid px-4 py-3">

    <!-- ── Greeting ─────────────────────────────────────────── -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-700 mb-0">Good <?= date('H')<12?'Morning':( date('H')<17?'Afternoon':'Evening') ?>, <?= e(currentUser()['first_name']) ?> 👋</h4>
            <p class="text-muted mb-0 small"><?= date('l, F j, Y') ?> &nbsp;·&nbsp; <?= e(currentUser()['role_name']) ?></p>
        </div>
        <?php if ($role === ROLE_EMPLOYEE && isset($roleData['today_record'])): ?>
        <div class="d-flex gap-2">
            <?php $rec = $roleData['today_record']; ?>
            <?php if (!$rec || empty($rec['clock_in'])): ?>
            <button class="btn btn-success" onclick="handleClock('in')" id="clockInBtn"><i class="bi bi-play-circle me-1"></i>Clock In</button>
            <?php elseif (empty($rec['clock_out'])): ?>
            <button class="btn btn-danger" onclick="handleClock('out')" id="clockOutBtn"><i class="bi bi-stop-circle me-1"></i>Clock Out</button>
            <?php else: ?>
            <span class="badge bg-success fs-6 px-3 py-2"><i class="bi bi-check-circle me-1"></i>Done for today</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- ── Stat Cards (Restored Info + New Design) ──────── -->
    <?php if (in_array($role,[ROLE_SUPER_ADMIN,ROLE_HR_DIRECTOR,ROLE_HR_SPECIALIST])): ?>
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="report-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="report-card-title">Total Employees</div>
                    <div class="text-muted"><i class="bi bi-people"></i></div>
                </div>
                <div class="fs-3 fw-bold mb-1"><?= formatNumber($stats['total_employees']) ?></div>
                <div class="small text-success fw-medium">
                    <i class="bi bi-check-circle me-1"></i><?= $stats['active_employees'] ?> Active
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="report-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="report-card-title">Pending Leaves</div>
                    <div class="text-muted"><i class="bi bi-calendar-x"></i></div>
                </div>
                <div class="fs-3 fw-bold mb-1"><?= $stats['pending_leaves'] ?></div>
                <div class="small fw-medium">
                    <a href="index.php?module=leaves&status=pending" class="text-decoration-none text-warning">Review now →</a>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="report-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="report-card-title">Present Today</div>
                    <div class="text-muted"><i class="bi bi-clock-history"></i></div>
                </div>
                <div class="fs-3 fw-bold mb-1"><?= $stats['today_attendance']['present'] ?? 0 ?></div>
                <div class="small text-danger fw-medium">
                    <i class="bi bi-person-x me-1"></i><?= $stats['today_attendance']['absent']??0 ?> Absent
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="report-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="report-card-title">Late Today</div>
                    <div class="text-muted"><i class="bi bi-clock"></i></div>
                </div>
                <div class="fs-3 fw-bold mb-1"><?= $stats['today_attendance']['late'] ?? 0 ?></div>
                <div class="small text-info fw-medium">
                    <i class="bi bi-calendar-check me-1"></i><?= $stats['today_attendance']['on_leave']??0 ?> On Leave
                </div>
            </div>
        </div>
    </div>

    <!-- ── Charts Row 1 ─────────────────────────────────────── -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Employees by Dept</div>
                <div class="report-card-subtitle">Headcount per department (Bar Chart)</div>
                <div id="deptChart" class="w-100"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Leave Type Distribution</div>
                <div class="report-card-subtitle">Approved requests breakdown</div>
                <div id="leaveTypeChart" class="w-100"></div>
            </div>
        </div>
    </div>

    <!-- ── 3-Grid Analytics Row ────────────────────────────── -->
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <?php if (in_array($role, [ROLE_RECRUITMENT_OFFICER, ROLE_HR_DIRECTOR, ROLE_SUPER_ADMIN])): ?>
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Recruitment Pipeline</div>
                <div class="report-card-subtitle">Applicant status breakdown</div>
                <div id="recChart" class="w-100"></div>
            </div>
            <?php endif; ?>
        </div>
        <div class="col-lg-4">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Department Distribution</div>
                <div class="report-card-subtitle">Employee count by department</div>
                <div id="deptDistChart" class="w-100"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Performance Distribution</div>
                <div class="report-card-subtitle">Employee rating breakdown</div>
                <div id="perfChart" class="w-100"></div>
            </div>
        </div>
    </div>

    <!-- ── Recent Employees & Leaves ──────────────────── -->
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between py-3">
                    <span><i class="bi bi-people-fill text-primary me-2"></i>Recent Employees</span>
                    <a href="index.php?module=employees" class="btn btn-outline-primary btn-sm">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Employee</th><th>Department</th><th>Type</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach(($roleData['recent_employees']??[]) as $emp): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= avatarUrl($emp['avatar']) ?>" class="avatar-sm" alt="">
                                    <div>
                                        <div class="fw-medium"><?= e($emp['full_name']) ?></div>
                                        <div class="text-muted small"><?= e($emp['employee_number']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="small"><?= e($emp['department_name']) ?></td>
                            <td><span class="badge bg-light text-dark"><?= ucwords(str_replace('_',' ',$emp['employment_type'])) ?></span></td>
                            <td><?= statusBadge($emp['status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between py-3">
                    <span><i class="bi bi-calendar-check text-primary me-2"></i>Pending Leaves</span>
                    <a href="index.php?module=leaves&status=pending" class="btn btn-outline-warning btn-sm">Review</a>
                </div>
                <div class="list-group list-group-flush">
                <?php foreach(($roleData['recent_leaves']??[]) as $lv): ?>
                    <div class="list-group-item px-3 py-2">
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= avatarUrl($lv['avatar']) ?>" class="avatar-sm" alt="">
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-medium small"><?= e($lv['full_name']) ?></div>
                                <div class="text-muted" style="font-size:.73rem"><?= e($lv['leave_type_name']) ?> · <?= formatDate($lv['start_date'],'M d') ?> – <?= formatDate($lv['end_date'],'M d') ?> (<?= $lv['days_requested'] ?> day<?= $lv['days_requested']>1?'s':'' ?>)</div>
                            </div>
                            <?= statusBadge($lv['status']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($roleData['recent_leaves'])): ?>
                    <div class="empty-state"><i class="bi bi-check2-all"></i>No pending leaves</div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── Employee Self-Service ───────────────────────── -->
    <?php if ($role === ROLE_EMPLOYEE): ?>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card clock-card">
                <div class="clock-display" id="liveClock"></div>
                <div class="clock-date mb-3" id="liveDate"></div>
                <?php $rec = $roleData['today_record'] ?? null; ?>
                <?php if (!$rec || empty($rec['clock_in'])): ?>
                <button class="clock-btn clock-in-btn mx-auto" onclick="handleClock('in')" id="clockInBtn"><i class="bi bi-play-circle d-block fs-3 mb-1"></i>Clock In</button>
                <?php elseif (empty($rec['clock_out'])): ?>
                <button class="clock-btn clock-out-btn mx-auto" onclick="handleClock('out')" id="clockOutBtn"><i class="bi bi-stop-circle d-block fs-3 mb-1"></i>Clock Out</button>
                <div class="text-muted small mt-2">Clocked in at <?= formatDateTime($rec['clock_in']) ?></div>
                <?php else: ?>
                <div class="text-success fw-600 fs-5"><i class="bi bi-check-circle-fill me-1"></i>Completed</div>
                <div class="text-muted small mt-1">In: <?= date('h:i A',strtotime($rec['clock_in'])) ?> · Out: <?= date('h:i A',strtotime($rec['clock_out'])) ?> · <?= $rec['hours_worked'] ?>h</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header py-3"><i class="bi bi-wallet2 text-primary me-2"></i>Leave Balance</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Type</th><th class="text-center">Alloc</th><th class="text-center">Used</th><th class="text-center">Left</th></tr></thead>
                        <tbody>
                        <?php foreach(($roleData['leave_balance']??[]) as $b): ?>
                        <tr>
                            <td class="small"><?= e($b['leave_type_name']) ?></td>
                            <td class="text-center small"><?= $b['allocated'] ?></td>
                            <td class="text-center small"><?= $b['used'] ?></td>
                            <td class="text-center"><strong class="text-<?= $b['remaining']>5?'success':($b['remaining']>0?'warning':'danger') ?>"><?= $b['remaining'] ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent"><a href="index.php?module=leaves&action=request" class="btn btn-primary btn-sm w-100">+ Request Leave</a></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header py-3"><i class="bi bi-receipt text-primary me-2"></i>Recent Payslips</div>
                <div class="list-group list-group-flush">
                <?php foreach(array_slice($roleData['my_payslips']??[],0,4) as $ps): ?>
                    <a href="index.php?module=payroll&action=payslip&period_id=<?= $ps['period_id'] ?>&employee_id=<?= currentUser()['employee_id'] ?>" class="list-group-item list-group-item-action px-3 py-2">
                        <div class="d-flex justify-content-between">
                            <span class="fw-medium small"><?= e($ps['period_name']) ?></span>
                            <?= statusBadge($ps['period_status']) ?>
                        </div>
                        <div class="text-muted small">Net Pay: <strong><?= formatCurrency($ps['net_pay']) ?></strong></div>
                    </a>
                <?php endforeach; ?>
                <?php if(empty($roleData['my_payslips'])): ?><div class="empty-state"><i class="bi bi-receipt-cutoff"></i>No payslips yet</div><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- ── Department Manager ──────────────────────────── -->
    <?php if ($role === ROLE_DEPT_MANAGER && !empty($roleData['dept_name'])): ?>
    <div class="row g-3 mb-4">
        <div class="col-12 mb-2">
            <h5 class="fw-600 mb-0 text-primary"><i class="bi bi-building me-2"></i><?= e($roleData['dept_name']) ?> Department</h5>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-indigo">
                <div class="stat-icon"><i class="bi bi-people"></i></div>
                <div class="stat-number"><?= $roleData['dept_employees_count'] ?? 0 ?></div>
                <div class="stat-label">Dept. Employees</div>
                <div class="stat-change text-white opacity-75">Your Team</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-emerald">
                <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                <div class="stat-number"><?= $roleData['dept_attendance']['present'] ?? 0 ?></div>
                <div class="stat-label">Present Today</div>
                <div class="stat-change text-white opacity-75"><?= $roleData['dept_attendance']['late'] ?? 0 ?> Late</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-amber">
                <div class="stat-icon"><i class="bi bi-calendar-x"></i></div>
                <div class="stat-number"><?= $roleData['dept_pending_leaves'] ?? 0 ?></div>
                <div class="stat-label">Pending Leaves</div>
                <div class="stat-change"><a href="index.php?module=leaves&status=pending" class="text-white opacity-75">Review →</a></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-rose">
                <div class="stat-icon"><i class="bi bi-person-x"></i></div>
                <div class="stat-number"><?= $roleData['dept_attendance']['absent'] ?? 0 ?></div>
                <div class="stat-label">Absent Today</div>
                <div class="stat-change text-white opacity-75"><?= $roleData['dept_attendance']['on_leave'] ?? 0 ?> On Leave</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between py-3">
                    <span><i class="bi bi-calendar-check text-primary me-2"></i>Recent Leave Requests</span>
                    <a href="index.php?module=leaves" class="btn btn-outline-primary btn-sm">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Employee</th><th>Leave Type</th><th>Dates</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach(($roleData['recent_dept_leaves']??[]) as $lv): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= avatarUrl($lv['avatar']) ?>" class="avatar-sm" alt="">
                                    <div class="fw-medium small"><?= e($lv['full_name']) ?></div>
                                </div>
                            </td>
                            <td class="small"><?= e($lv['leave_type_name']) ?></td>
                            <td class="small"><?= formatDate($lv['start_date'],'M d') ?> – <?= formatDate($lv['end_date'],'M d') ?></td>
                            <td><?= statusBadge($lv['status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($roleData['recent_dept_leaves'])): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">No recent leave requests</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 0.75rem;">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <h6 class="mb-0 fw-bold">Dept. Attendance</h6>
                    <small class="text-muted">Today's presence</small>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div id="deptAttChart" class="w-100" style="min-height: 220px;"></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
</div>

</div>

</div>

<?php
// Chart data
$deptLabels = array_column($roleData['by_department']??[], 'name');
$deptData   = array_column($roleData['by_department']??[], 'cnt');
$att = $stats['today_attendance'] ?? [];
$recStatusData = $roleData['applicants_by_status'] ?? [];

$perfDataMap = [];
foreach (($roleData['perf_dist'] ?? []) as $pd) {
    $perfDataMap[(int)$pd['rating']] = $pd['cnt'];
}
?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
(function() {
    let chartInstances = [];

    function initCharts() {
        // Destroy existing instances
        chartInstances.forEach(chart => { try { chart.destroy(); } catch(e){} });
        chartInstances = [];

        if (typeof ApexCharts === 'undefined') return;

        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const textColor = isDark ? '#94a3b8' : '#64748b';
        const gridColor = isDark ? 'rgba(148, 163, 184, 0.1)' : 'rgba(100, 116, 139, 0.1)';
        const cardBg = getComputedStyle(document.documentElement).getPropertyValue('--hrms-card-bg').trim() || (isDark ? '#1e1b4b' : '#ffffff');

        const commonOptions = {
            chart: {
                fontFamily: 'Inter, sans-serif',
                background: 'transparent',
                toolbar: { show: false },
                animations: { enabled: true }
            },
            theme: { mode: isDark ? 'dark' : 'light' },
            dataLabels: { enabled: false },
            grid: { borderColor: gridColor, strokeDashArray: 4 },
            xaxis: { labels: { style: { colors: textColor } }, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { labels: { style: { colors: textColor } } },
            legend: { labels: { colors: textColor }, position: 'bottom' }
        };

        // 1. Department Bar Chart
        if (document.getElementById('deptChart')) {
            try {
                const chart = new ApexCharts(document.querySelector("#deptChart"), {
                    ...commonOptions,
                    series: [{ name: 'Employees', data: <?= json_encode($deptData ?: []) ?> }],
                    chart: { ...commonOptions.chart, type: 'bar', height: 260 },
                    colors: ['#6366f1'],
                    plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                    xaxis: { ...commonOptions.xaxis, categories: <?= json_encode($deptLabels ?: []) ?> },
                    yaxis: { ...commonOptions.yaxis, labels: { ...commonOptions.yaxis.labels, formatter: (v) => Math.floor(v) } }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("Dept Chart Error:", e); }
        }

        // 2. Leave Type Pie Chart
        if (document.getElementById('leaveTypeChart')) {
            try {
                <?php
                $leaveLabels = array_column($roleData['leave_type_dist'] ?? [], 'name');
                $leaveData   = array_column($roleData['leave_type_dist'] ?? [], 'cnt');
                if (empty($leaveLabels)) {
                    $leaveLabels = ['No Leaves'];
                    $leaveData = [1];
                    $leaveColors = ['#94a3b8'];
                } else {
                    $leaveColors = ['#3b82f6', '#ef4444', '#f59e0b', '#8b5cf6', '#64748b', '#10b981'];
                }
                ?>
                const chart = new ApexCharts(document.querySelector("#leaveTypeChart"), {
                    ...commonOptions,
                    series: <?= json_encode(array_map('intval', $leaveData)) ?>,
                    labels: <?= json_encode($leaveLabels) ?>,
                    chart: { ...commonOptions.chart, type: 'pie', height: 280 },
                    colors: <?= json_encode($leaveColors) ?>,
                    stroke: { width: 1, colors: [cardBg] },
                    legend: { show: false },
                    dataLabels: {
                        enabled: true,
                        style: { fontSize: '11px', colors: ['#fff'] },
                        dropShadow: { enabled: true }
                    }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("Leave Type Chart Error:", e); }
        }

        // 3. Recruitment Pipeline Donut
        if (document.getElementById('recChart')) {
            try {
                <?php
                $recKeys = array_keys($recStatusData ?? []);
                $recVals = array_values($recStatusData ?? []);
                if (empty($recVals) || array_sum($recVals) === 0) {
                    $recKeys = ['No Applicants'];
                    $recVals = [1];
                    $recColors = ['#94a3b8'];
                } else {
                    $recKeys = array_map(function($k) { return ucwords(str_replace('_', ' ', $k)); }, $recKeys);
                    $recColors = ['#6366f1','#f59e0b','#10b981','#ef4444','#8b5cf6','#ec4899'];
                }
                ?>
                const chart = new ApexCharts(document.querySelector("#recChart"), {
                    ...commonOptions,
                    series: <?= json_encode(array_map('intval', $recVals)) ?>,
                    labels: <?= json_encode($recKeys) ?>,
                    chart: { ...commonOptions.chart, type: 'donut', height: 280 },
                    colors: <?= json_encode($recColors) ?>,
                    stroke: { width: 1, colors: [cardBg] }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("Recruitment Chart Error:", e); }
        }

        // 4. Dept Distribution Pie
        if (document.getElementById('deptDistChart')) {
            try {
                const chart = new ApexCharts(document.querySelector("#deptDistChart"), {
                    ...commonOptions,
                    series: <?= json_encode(array_map('intval', $deptData ?: [])) ?>,
                    labels: <?= json_encode($deptLabels ?: []) ?>,
                    chart: { ...commonOptions.chart, type: 'pie', height: 280 },
                    colors: ['#3b82f6', '#ef4444', '#f59e0b', '#8b5cf6', '#10b981', '#6366f1'],
                    stroke: { width: 1, colors: [cardBg] }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("Dept Dist Chart Error:", e); }
        }

        // 5. Performance Distribution Horizontal Bar
        if (document.getElementById('perfChart')) {
            try {
                <?php
                $perfSeriesData = [
                    (int)($perfDataMap[5] ?? 0),
                    (int)($perfDataMap[4] ?? 0),
                    (int)($perfDataMap[3] ?? 0),
                    (int)($perfDataMap[2] ?? 0),
                    (int)($perfDataMap[1] ?? 0)
                ];
                ?>
                const chart = new ApexCharts(document.querySelector("#perfChart"), {
                    ...commonOptions,
                    series: [{ name: 'Employees', data: <?= json_encode($perfSeriesData) ?> }],
                    chart: { ...commonOptions.chart, type: 'bar', height: 280 },
                    plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '60%' } },
                    colors: ['#10b981'],
                    xaxis: { ...commonOptions.xaxis, categories: ['5-Excell', '4-Good', '3-Meets', '2-Below', '1-Unsat'] }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("Performance Chart Error:", e); }
        }

        // 6. Dept Attendance Donut (Manager)
        if (document.getElementById('deptAttChart')) {
            try {
                <?php 
                $datt = $roleData['dept_attendance'] ?? []; 
                $dattVals = [(int)($datt['present']??0), (int)($datt['absent']??0), (int)($datt['late']??0), (int)($datt['on_leave']??0)];
                $dattLabels = array_sum($dattVals) === 0 ? ['No Data'] : ['Present','Absent','Late','On Leave'];
                $dattColors = array_sum($dattVals) === 0 ? ['#94a3b8'] : ['#10b981','#ef4444','#f59e0b','#3b82f6'];
                ?>
                const chart = new ApexCharts(document.querySelector("#deptAttChart"), {
                    ...commonOptions,
                    series: <?= json_encode(array_sum($dattVals) === 0 ? [1] : $dattVals) ?>,
                    labels: <?= json_encode($dattLabels) ?>,
                    chart: { ...commonOptions.chart, type: 'donut', height: 260 },
                    colors: <?= json_encode($dattColors) ?>,
                    stroke: { width: 1, colors: [cardBg] }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("Dept Attendance Chart Error:", e); }
        }
    }

    // Initial load
    window.addEventListener('load', initCharts);

    // Watch for theme changes
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'data-bs-theme') {
                initCharts();
            }
        });
    });
    observer.observe(document.documentElement, { attributes: true });
})();
</script>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
