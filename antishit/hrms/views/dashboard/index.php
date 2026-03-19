<?php
/**
 * Dashboard View (role-based)
 */
$role = currentRole();
$pageTitle  = 'Dashboard';
$breadcrumb = [['label'=>'Dashboard','active'=>true]];
include APP_ROOT . '/views/layouts/header.php';
?>
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

    <!-- ── Stat Cards ──────────────────────────────────────── -->
    <?php if (in_array($role,[ROLE_SUPER_ADMIN,ROLE_HR_DIRECTOR,ROLE_HR_SPECIALIST])): ?>
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-indigo">
                <div class="stat-icon"><i class="bi bi-people"></i></div>
                <div class="stat-number"><?= formatNumber($stats['total_employees']) ?></div>
                <div class="stat-label">Total Employees</div>
                <div class="stat-change"><i class="bi bi-check-circle me-1"></i><?= $stats['active_employees'] ?> Active</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-amber">
                <div class="stat-icon"><i class="bi bi-calendar-x"></i></div>
                <div class="stat-number"><?= $stats['pending_leaves'] ?></div>
                <div class="stat-label">Pending Leaves</div>
                <div class="stat-change"><a href="index.php?module=leaves&status=pending" class="text-white opacity-75">Review now →</a></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-emerald">
                <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                <div class="stat-number"><?= $stats['today_attendance']['present'] ?? 0 ?></div>
                <div class="stat-label">Present Today</div>
                <div class="stat-change"><i class="bi bi-person-x me-1"></i><?= $stats['today_attendance']['absent']??0 ?> Absent</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-rose">
                <div class="stat-icon"><i class="bi bi-clock"></i></div>
                <div class="stat-number"><?= $stats['today_attendance']['late'] ?? 0 ?></div>
                <div class="stat-label">Late Today</div>
                <div class="stat-change"><i class="bi bi-calendar-check me-1"></i><?= $stats['today_attendance']['on_leave']??0 ?> On Leave</div>
            </div>
        </div>
    </div>

    <!-- ── Charts Row ─────────────────────────────────────── -->
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <span><i class="bi bi-bar-chart-line text-primary me-2"></i>Employees by Department</span>
                </div>
                <div class="card-body"><canvas id="deptChart" height="200"></canvas></div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header py-3"><i class="bi bi-pie-chart text-primary me-2"></i>Attendance Today</div>
                <div class="card-body d-flex align-items-center justify-content-center"><canvas id="attChart" height="200"></canvas></div>
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
            <div class="card h-100">
                <div class="card-header py-3"><i class="bi bi-pie-chart text-primary me-2"></i>Dept. Attendance</div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="deptAttChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── Finance Manager ─────────────────────────────── -->
    <?php if (in_array($role,[ROLE_FINANCE_MANAGER,ROLE_HR_DIRECTOR,ROLE_SUPER_ADMIN]) && !empty($roleData['payroll_summary'])): ?>
    <div class="row g-3 mt-1">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-3"><i class="bi bi-cash-stack text-primary me-2"></i>Payroll Summary</div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Period</th><th>Pay Date</th><th>Employees</th><th>Gross</th><th>Net</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach($roleData['payroll_summary'] as $row): ?>
                        <tr>
                            <td class="fw-medium small"><?= e($row['period_name']) ?></td>
                            <td class="small"><?= formatDate($row['pay_date']) ?></td>
                            <td class="text-center"><?= $row['employees']??0 ?></td>
                            <td class="small"><?= formatCurrency($row['total_gross']??0) ?></td>
                            <td class="small fw-medium text-success"><?= formatCurrency($row['total_net']??0) ?></td>
                            <td><?= statusBadge($row['status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php
// Chart data
$deptLabels = array_column($roleData['by_department']??[], 'name');
$deptData   = array_column($roleData['by_department']??[], 'cnt');
$att = $stats['today_attendance'] ?? [];
?>
<script>
// ── Department Chart ─────────────────────────────────────
const deptCtx = document.getElementById('deptChart');
if (deptCtx) {
    new Chart(deptCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($deptLabels) ?>,
            datasets: [{ label: 'Employees', data: <?= json_encode($deptData) ?>,
                backgroundColor: '#4f46e5', borderRadius: 6, barThickness: 30 }]
        },
        options: { responsive: true, plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize:1 } } } }
    });
}
// ── Attendance Doughnut ──────────────────────────────────
const attCtx = document.getElementById('attChart');
if (attCtx) {
    new Chart(attCtx, {
        type: 'doughnut',
        data: {
            labels: ['Present','Absent','Late','On Leave'],
            datasets: [{ data: [
                <?= (int)($att['present']??0) ?>,<?= (int)($att['absent']??0) ?>,
                <?= (int)($att['late']??0) ?>,<?= (int)($att['on_leave']??0) ?>
            ], backgroundColor: ['#059669','#e11d48','#d97706','#0284c7'], borderWidth: 0, hoverOffset:6 }]
        },
        options: { responsive:true, cutout:'65%', plugins:{ legend:{ position:'bottom', labels:{ font:{size:11} } } } }
    });
}
// ── Dept Attendance Doughnut ──────────────────────────────
const deptAttCtx = document.getElementById('deptAttChart');
if (deptAttCtx) {
    <?php $datt = $roleData['dept_attendance'] ?? []; ?>
    new Chart(deptAttCtx, {
        type: 'doughnut',
        data: {
            labels: ['Present','Absent','Late','On Leave'],
            datasets: [{ data: [
                <?= (int)($datt['present']??0) ?>,<?= (int)($datt['absent']??0) ?>,
                <?= (int)($datt['late']??0) ?>,<?= (int)($datt['on_leave']??0) ?>
            ], backgroundColor: ['#059669','#e11d48','#d97706','#0284c7'], borderWidth: 0, hoverOffset:6 }]
        },
        options: { responsive:true, cutout:'65%', plugins:{ legend:{ position:'bottom', labels:{ font:{size:11} } } } }
    });
}
</script>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
