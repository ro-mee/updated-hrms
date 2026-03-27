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
        <?php if (isset($roleData['today_record'])): ?>
        <div class="d-flex gap-2">
            <?php $rec = $roleData['today_record']; ?>
            <?php if (!$rec || empty($rec['clock_in'])): ?>
            <button class="btn btn-success" onclick="handleClock('in')" id="clockInBtn"><i class="bi bi-play-circle me-1"></i>Clock In</button>
            <?php elseif (empty($rec['clock_out'])): ?>
                <?php if (!empty($rec['lunch_start']) && empty($rec['lunch_end'])): ?>
                <button class="btn btn-primary" onclick="handleBreak('endLunch')"><i class="bi bi-stop-circle me-1"></i>End Lunch</button>
                <?php elseif (!empty($rec['break1_start']) && empty($rec['break1_end'])): ?>
                <button class="btn btn-warning text-white" onclick="handleBreak('endBreak1')"><i class="bi bi-stop-circle me-1"></i>End 1st Break</button>
                <?php elseif (!empty($rec['break2_start']) && empty($rec['break2_end'])): ?>
                <button class="btn btn-warning text-white" onclick="handleBreak('endBreak2')"><i class="bi bi-stop-circle me-1"></i>End 2nd Break</button>
                <?php elseif (!empty($rec['emergency_break_start']) && empty($rec['emergency_break_end'])): ?>
                <button class="btn btn-danger" onclick="handleBreak('endEmergencyBreak')"><i class="bi bi-stop-circle me-1"></i>End E-Break</button>
                <?php else: ?>
                <button class="btn btn-danger" onclick="handleClock('out')" id="clockOutBtn"><i class="bi bi-stop-circle me-1"></i>Clock Out</button>
                <?php endif; ?>
            <?php else: ?>
            <span class="badge bg-success fs-6 px-3 py-2"><i class="bi bi-check-circle me-1"></i>Done for today</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- ── Stat Cards (Restored Info + New Design) ──────── -->
    <?php if (in_array($role,[ROLE_SUPER_ADMIN,ROLE_HR_DIRECTOR,ROLE_HR_SPECIALIST,ROLE_RECRUITMENT_OFFICER])): ?>
    <div class="row g-3 mb-4">
        <?php if ($role === ROLE_RECRUITMENT_OFFICER): ?>
            <!-- Recruitment Officer Highlights -->
            <div class="col-6 col-lg-3">
                <div class="report-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="report-card-title">Open Jobs</div>
                        <div class="text-muted"><i class="bi bi-briefcase"></i></div>
                    </div>
                    <div class="fs-3 fw-bold mb-1"><?= $roleData['open_jobs'] ?? 0 ?></div>
                    <div class="small fw-medium">
                        <a href="index.php?module=recruitment" class="text-decoration-none text-primary">View Positions →</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="report-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="report-card-title">Total Applicants</div>
                        <div class="text-muted"><i class="bi bi-people"></i></div>
                    </div>
                    <div class="fs-3 fw-bold mb-1"><?= $roleData['total_applicants'] ?? 0 ?></div>
                    <div class="small text-success fw-medium">
                        <i class="bi bi-plus-circle me-1"></i>Combined Pipeline
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="report-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="report-card-title">Pending Review</div>
                        <div class="text-muted"><i class="bi bi-hourglass-split"></i></div>
                    </div>
                    <div class="fs-3 fw-bold mb-1"><?= ($roleData['applicants_by_status']['new'] ?? 0) + ($roleData['applicants_by_status']['reviewing'] ?? 0) ?></div>
                    <div class="small text-warning fw-medium">
                        New & Reviewing
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="report-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="report-card-title">Hired This Month</div>
                        <div class="text-muted"><i class="bi bi-award"></i></div>
                    </div>
                    <div class="fs-3 fw-bold mb-1"><?= $roleData['hired_this_month'] ?? 0 ?></div>
                    <div class="small text-info fw-medium">
                        <i class="bi bi-calendar-check me-1"></i>Monthly Growth
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Standard HR Stats -->
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
        <?php endif; ?>
    </div>

    <!-- ── Charts Row 1 ─────────────────────────────────────── -->
    <div class="row g-3 mb-4">
        <?php if ($role === ROLE_RECRUITMENT_OFFICER): ?>
            <div class="col-lg-4">
                <div class="report-card p-3 h-100">
                    <div class="report-card-title">Recruitment Pipeline</div>
                    <div class="report-card-subtitle">Applicant status breakdown</div>
                    <div id="recChart" class="w-100"></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="report-card p-3 h-100">
                    <div class="report-card-title">Job Status Distribution</div>
                    <div class="report-card-subtitle">Open vs. Closed Positions</div>
                    <div id="jobStatusChart" class="w-100"></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="report-card p-3 h-100">
                    <div class="report-card-title">Application Sources</div>
                    <div class="report-card-subtitle">Where applicants are coming from</div>
                    <div id="sourceChart" class="w-100"></div>
                </div>
            </div>
        <?php else: ?>
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
        <?php endif; ?>
    </div>

    <?php if ($role !== ROLE_RECRUITMENT_OFFICER): ?>
    <!-- ── 3-Grid Analytics Row (Hidden for Recruitment) ────────────── -->
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <?php if (in_array($role, [ROLE_HR_DIRECTOR, ROLE_SUPER_ADMIN, ROLE_HR_SPECIALIST])): ?>
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
    <?php endif; ?>

    <!-- ── Recent Activities / Role-Specific Content ──────── -->
    <div class="row g-3">
        <?php if ($role === ROLE_RECRUITMENT_OFFICER): ?>
            <!-- Recruitment Officer: Interviews & Applicants -->
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between py-3 border-0">
                        <span class="report-card-title"><i class="bi bi-calendar-event text-primary me-2"></i>Upcoming Interviews</span>
                        <a href="index.php?module=recruitment" class="text-decoration-none small text-primary">View Schedule →</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Name</th><th>Position</th><th>Schedule</th></tr></thead>
                            <tbody>
                            <?php foreach(($roleData['upcoming_interviews']??[]) as $int): ?>
                            <tr>
                                <td><div class="fw-medium small"><?= e($int['first_name'].' '.$int['last_name']) ?></div></td>
                                <td><div class="small text-muted"><?= e($int['job_title']) ?></div></td>
                                <td class="small"><?= formatDateTime($int['interview_date']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($roleData['upcoming_interviews'])): ?>
                            <tr><td colspan="3" class="text-center py-4 text-muted small">No upcoming interviews</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between py-3 border-0">
                        <span class="report-card-title"><i class="bi bi-people text-primary me-2"></i>Recent Applicants</span>
                        <a href="index.php?module=recruitment" class="text-decoration-none small text-primary">Manage All →</a>
                    </div>
                    <div class="list-group list-group-flush">
                    <?php foreach(($roleData['recent_applicants']??[]) as $ra): ?>
                        <div class="list-group-item px-3 py-2 border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="overflow-hidden">
                                    <div class="fw-medium small text-truncate"><?= e($ra['first_name'].' '.$ra['last_name']) ?></div>
                                    <div class="text-muted" style="font-size:.73rem"><?= e($ra['job_title']) ?></div>
                                </div>
                                <?= statusBadge($ra['status']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if(empty($roleData['recent_applicants'])): ?>
                    <div class="p-4 text-center text-muted small">No applicants recorded</div>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php elseif (in_array($role,[ROLE_SUPER_ADMIN,ROLE_HR_DIRECTOR,ROLE_HR_SPECIALIST])): ?>
            <!-- HR Management: Recent Employees & Leaves -->
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between py-3 border-0">
                        <span class="report-card-title"><i class="bi bi-people-fill text-primary me-2"></i>Recent Employees</span>
                        <a href="index.php?module=employees" class="text-decoration-none small text-primary">View All →</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Employee</th><th>Department</th><th>Status</th></tr></thead>
                            <tbody>
                            <?php foreach(($roleData['recent_employees']??[]) as $emp): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?= avatarUrl($emp['avatar']) ?>" class="avatar-sm" alt="">
                                        <div>
                                            <div class="fw-medium small"><?= e($emp['full_name']) ?></div>
                                            <div class="text-muted" style="font-size:.7rem"><?= e($emp['employee_number']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="small"><?= e($emp['department_name']) ?></td>
                                <td><?= statusBadge($emp['status']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between py-3 border-0">
                        <span class="report-card-title"><i class="bi bi-calendar-check text-primary me-2"></i>Pending Leaves</span>
                        <a href="index.php?module=leaves&status=pending" class="text-decoration-none small text-primary">Review →</a>
                    </div>
                    <div class="list-group list-group-flush">
                    <?php foreach(($roleData['recent_leaves']??[]) as $lv): ?>
                        <div class="list-group-item px-3 py-2 border-0">
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?= avatarUrl($lv['avatar']) ?>" class="avatar-sm" alt="">
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-medium small"><?= e($lv['full_name']) ?></div>
                                    <div class="text-muted" style="font-size:.73rem"><?= e($lv['leave_type_name']) ?> · <?= formatDate($lv['start_date'],'M d') ?></div>
                                </div>
                                <?= statusBadge($lv['status']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($roleData['recent_leaves'])): ?>
                        <div class="p-4 text-center text-muted small"><i class="bi bi-check2-all me-1"></i>No pending leaves</div>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ── Employee Self-Service (Enhanced) ───────────────────────── -->
    <?php if ($role === ROLE_EMPLOYEE): ?>
    
    <!-- 1. Highlights Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Attendance Rate</div>
                <div class="fs-4 fw-bold mb-0"><?= $roleData['attendance_rate'] ?>%</div>
                <div class="small text-muted">This month</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Pending Leaves</div>
                <div class="fs-4 fw-bold mb-0"><?= count(array_filter($roleData['my_leaves']??[], fn($l)=>$l['status']==='pending')) ?></div>
                <div class="small text-muted">Awaiting review</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Trainings</div>
                <div class="fs-4 fw-bold mb-0"><?= $roleData['training_stats']['total'] ?></div>
                <div class="small text-muted">Programs enrolled</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Next Payday</div>
                <div class="fs-4 fw-bold mb-0"><?= $roleData['next_payday'] ? formatDate($roleData['next_payday'], 'M d') : 'TBA' ?></div>
                <div class="small text-muted"><?= $roleData['next_payday'] ? timeAgo($roleData['next_payday']) : 'Coming soon' ?></div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Main Actions & Attendance -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4 text-center">
                    <div id="liveClock" class="fs-1 fw-bold mb-0"></div>
                    <div id="liveDate" class="text-muted small mb-4"></div>
                    
                    <?php $rec = $roleData['today_record'] ?? null; ?>
                    <?php if (!$rec || empty($rec['clock_in'])): ?>
                        <button class="btn btn-primary btn-lg w-100 py-3 rounded-pill shadow-sm mb-2" onclick="handleClock('in')" id="clockInBtn">
                            <i class="bi bi-play-circle me-2"></i>Clock In
                        </button>
                    <?php elseif (empty($rec['clock_out'])): ?>
                        <button class="btn btn-danger btn-lg w-100 py-3 rounded-pill shadow-sm mb-2" onclick="handleClock('out')" id="clockOutBtn">
                            <i class="bi bi-stop-circle me-2"></i>Clock Out
                        </button>
                        <p class="small text-muted mt-2 mb-0">Clocked in at <?= formatDateTime($rec['clock_in']) ?></p>
                    <?php else: ?>
                        <div class="p-3 bg-success-subtle text-success rounded-4 mb-2">
                            <i class="bi bi-check-circle-fill fs-4 d-block mb-1"></i>
                            <div class="fw-bold">Shift Completed</div>
                            <div class="small">In: <?= date('h:i A',strtotime($rec['clock_in'])) ?> · Out: <?= date('h:i A',strtotime($rec['clock_out'])) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Attendance Breakdown Chart -->
            <div class="report-card p-3">
                <div class="report-card-title">Monthly Attendance</div>
                <div class="report-card-subtitle">Presence distribution</div>
                <div id="myAttChart" class="w-100"></div>
            </div>
        </div>

        <!-- Growth & Balance -->
        <div class="col-lg-4">
            <!-- Latest Performance -->
            <div class="report-card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="report-card-title"><i class="bi bi-award text-warning me-2"></i>Latest Review</div>
                    <?php if ($roleData['latest_performance']): ?>
                        <span class="badge bg-primary"><?= $roleData['latest_performance']['overall_rating'] ?> / 5</span>
                    <?php endif; ?>
                </div>
                <?php if ($roleData['latest_performance']): ?>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-primary-subtle p-2 rounded-circle">
                            <i class="bi bi-star-fill text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-bold"><?= e($roleData['latest_performance']['review_period']) ?></div>
                            <div class="small text-muted">Reviewed by <?= e($roleData['latest_performance']['reviewer_name']) ?></div>
                        </div>
                    </div>
                    <div class="small text-muted mb-3 italic">"<?= truncate($roleData['latest_performance']['strengths'], 100) ?>"</div>
                    <a href="index.php?module=performance&action=view&id=<?= $roleData['latest_performance']['id'] ?>" class="btn btn-outline-primary btn-sm w-100">View Full Review</a>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-journal-check fs-2 text-muted mb-2"></i>
                        <p class="small text-muted">No approved reviews yet.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Leave Balance -->
            <div class="report-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="report-card-title"><i class="bi bi-calendar-event text-primary me-2"></i>Leave Balance</div>
                    <a href="index.php?module=leaves&action=request" class="btn btn-primary btn-sm rounded-pill px-3">+ Request</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0">
                        <thead><tr class="text-muted" style="font-size: .7rem"><th>TYPE</th><th class="text-center">USED</th><th class="text-center">REMAINING</th></tr></thead>
                        <tbody>
                        <?php foreach(($roleData['leave_balance']??[]) as $b): ?>
                        <tr class="align-middle">
                            <td><div class="fw-medium small"><?= e($b['leave_type_name']) ?></div></td>
                            <td class="text-center small"><?= $b['used'] ?> d</td>
                            <td class="text-center">
                                <span class="badge bg-<?= $b['remaining']>5?'success':($b['remaining']>0?'warning':'danger') ?>-subtle text-<?= $b['remaining']>5?'success':($b['remaining']>0?'warning':'danger') ?> rounded-pill px-2">
                                    <?= $b['remaining'] ?> left
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Payslips & Career -->
        <div class="col-lg-4">
            <div class="report-card p-3 mb-3">
                <div class="report-card-title mb-3"><i class="bi bi-receipt text-success me-2"></i>Recent Payslips</div>
                <div class="list-group list-group-flush">
                <?php foreach(array_slice($roleData['my_payslips']??[],0,3) as $ps): ?>
                    <a href="index.php?module=payroll&action=payslip&period_id=<?= $ps['period_id'] ?>&employee_id=<?= currentUser()['employee_id'] ?>" class="list-group-item list-group-item-action px-0 border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold small mb-0"><?= e($ps['period_name']) ?></div>
                                <div class="text-muted" style="font-size: .75rem">Paid on <?= formatDate($ps['pay_date']) ?></div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success"><?= formatCurrency($ps['net_pay']) ?></div>
                                <?= statusBadge($ps['period_status']) ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
                <?php if(empty($roleData['my_payslips'])): ?>
                    <div class="p-4 text-center text-muted small"><i class="bi bi-receipt-cutoff d-block fs-3 mb-2"></i>No payslips generated yet</div>
                <?php endif; ?>
                </div>
                <div class="mt-3">
                    <a href="index.php?module=payroll&action=my" class="btn btn-light btn-sm w-100">View All Payslips</a>
                </div>
            </div>

            <!-- Training Summary -->
            <div class="report-card p-3">
                <div class="report-card-title mb-3">Training Programs</div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-info-subtle p-2 rounded-circle">
                            <i class="bi bi-mortarboard text-info"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-5"><?= $roleData['training_stats']['total'] ?></div>
                            <div class="small text-muted">Total Enrolled</div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-success"><?= $roleData['training_stats']['completed'] ?></div>
                        <div class="small text-muted">Completed</div>
                    </div>
                </div>
                <a href="index.php?module=training" class="btn btn-outline-info btn-sm w-100">Browse Programs</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <!-- ── Department Manager (Enhanced) ──────────────────────────── -->
    <?php if ($role === ROLE_DEPT_MANAGER && !empty($roleData['dept_name'])): ?>
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="fw-bold mb-0 text-primary">Department: <?= e($roleData['dept_name']) ?></h4>
            <p class="text-muted small mb-0">Overview of your team's performance and attendance</p>
        </div>
        <div class="col-md-6 text-md-end">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                <i class="bi bi-people-fill me-1"></i> <?= $roleData['dept_employees_count'] ?> Total Team Members
            </span>
        </div>
    </div>

    <!-- 1. Highlights Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Daily Attendance</div>
                <div class="fs-4 fw-bold mb-0 text-success"><?= $roleData['dept_attendance_rate'] ?>%</div>
                <div class="small text-muted">Today's presence</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Pending Leaves</div>
                <div class="fs-4 fw-bold mb-0 <?= $roleData['dept_pending_leaves'] > 0 ? 'text-warning' : '' ?>">
                    <?= $roleData['dept_pending_leaves'] ?>
                </div>
                <div class="small text-muted">Requests to review</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Active Trainings</div>
                <div class="fs-4 fw-bold mb-0 text-info"><?= count($roleData['dept_trainings']??[]) ?></div>
                <div class="small text-muted">Programs scheduled</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Department Rating</div>
                <?php 
                $avg = 0; $total = 0;
                foreach($roleData['dept_perf_dist'] as $pd) { $avg += $pd['rating'] * $pd['cnt']; $total += $pd['cnt']; }
                $finalAvg = $total > 0 ? round($avg / $total, 1) : 'N/A';
                ?>
                <div class="fs-4 fw-bold mb-0 text-primary"><?= $finalAvg ?></div>
                <div class="small text-muted">Team avg rating</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Attendance Trend -->
        <div class="col-lg-8">
            <div class="report-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <div class="report-card-title">Attendance Trend</div>
                        <div class="report-card-subtitle">Presence over last 7 days</div>
                    </div>
                </div>
                <div id="deptTrendChart" style="min-height: 280px;"></div>
            </div>
        </div>
        <!-- Performance Distribution -->
        <div class="col-lg-4">
            <div class="report-card p-4 h-100">
                <div class="report-card-title mb-4">Performance Spread</div>
                <div id="deptPerfChart" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Pending Leaves -->
        <div class="col-lg-6">
            <div class="report-card p-0 h-100 overflow-hidden">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <div class="report-card-title mb-0">Action Required: Leaves</div>
                    <a href="index.php?module=leaves" class="btn btn-link btn-sm p-0">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light shadow-none"><tr style="font-size:.75rem" class="text-muted"><th>EMPLOYEE</th><th>TYPE</th><th>DATES</th><th>ACTION</th></tr></thead>
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
                            <td class="small"><?= formatDate($lv['start_date'],'M d') ?></td>
                            <td><a href="index.php?module=leaves&action=view&id=<?= $lv['id'] ?>" class="btn btn-primary btn-xs py-1 px-2" style="font-size:.7rem">Review</a></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($roleData['recent_dept_leaves'])): ?>
                        <tr><td colspan="4" class="text-center py-5 text-muted small">No pending leave requests found</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Upcoming Trainings -->
        <div class="col-lg-6">
            <div class="report-card p-0 h-100 overflow-hidden">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <div class="report-card-title mb-0">Goal: Training & Growth</div>
                    <a href="index.php?module=training" class="btn btn-link btn-sm p-0">Manage</a>
                </div>
                <div class="p-4 text-center py-5 <?php if(!empty($roleData['dept_trainings'])) echo 'd-none'; ?>">
                    <i class="bi bi-mortarboard text-muted fs-1 mb-3"></i>
                    <p class="text-muted small">No upcoming trainings scheduled for your department.</p>
                </div>
                <div class="list-group list-group-flush">
                <?php foreach(($roleData['dept_trainings']??[]) as $t): ?>
                    <div class="list-group-item p-4 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-bold"><?= e($t['title']) ?></h6>
                            <span class="badge bg-info-subtle text-info"><?= formatDate($t['start_date'], 'M d') ?></span>
                        </div>
                        <div class="text-muted small mb-3"><?= truncate($t['description'], 100) ?></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted"><i class="bi bi-geo-alt me-1"></i><?= e($t['location'] ?: 'Online') ?></span>
                            <div class="small fw-medium"><?= $t['enrolled_count'] ?> / <?= $t['max_participants'] ?> Enrolled</div>
                        </div>
                    </div>
                <?php endforeach; ?>
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

        // 6. Job Status Distribution (Recruitment)
        if (document.getElementById('jobStatusChart')) {
            try {
                <?php
                $jsd = $roleData['job_status_dist'] ?? [];
                $jsdKeys = array_keys($jsd);
                $jsdVals = array_values($jsd);
                if (empty($jsdVals)) {
                    $jsdKeys = ['No Jobs'];
                    $jsdVals = [1];
                    $jsdColors = ['#94a3b8'];
                } else {
                    $jsdKeys = array_map(function($k){ return ucwords(str_replace('_',' ',$k)); }, $jsdKeys);
                    $jsdColors = ['#10b981','#f59e0b','#64748b','#ef4444'];
                }
                ?>
                const chart = new ApexCharts(document.querySelector("#jobStatusChart"), {
                    ...commonOptions,
                    series: <?= json_encode(array_map('intval', $jsdVals)) ?>,
                    labels: <?= json_encode($jsdKeys) ?>,
                    chart: { ...commonOptions.chart, type: 'pie', height: 280 },
                    colors: <?= json_encode($jsdColors) ?>,
                    stroke: { width: 1, colors: [cardBg] }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("Job Status Chart Error:", e); }
        }

        // 7. Applicant Source Chart (Recruitment)
        if (document.getElementById('sourceChart')) {
            try {
                <?php
                $asd = $roleData['applicant_source_dist'] ?? [];
                $asdKeys = array_keys($asd);
                $asdVals = array_values($asd);
                if (empty($asdVals)) {
                    $asdKeys = ['No Data'];
                    $asdVals = [1];
                    $asdColors = ['#94a3b8'];
                } else {
                    $asdColors = ['#3b82f6','#8b5cf6','#ec4899','#f59e0b','#10b981'];
                }
                ?>
                const chart = new ApexCharts(document.querySelector("#sourceChart"), {
                    ...commonOptions,
                    series: <?= json_encode(array_map('intval', $asdVals)) ?>,
                    labels: <?= json_encode($asdKeys) ?>,
                    chart: { ...commonOptions.chart, type: 'donut', height: 280 },
                    colors: <?= json_encode($asdColors) ?>,
                    stroke: { width: 1, colors: [cardBg] }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("Source Chart Error:", e); }
        }

        // 8. Dept Attendance Trend (Manager)
        if (document.getElementById('deptTrendChart')) {
            try {
                <?php
                $trend = $roleData['dept_attendance_trend'] ?? [];
                $trendLabels = array_column($trend, 'date');
                $trendPresent = array_column($trend, 'present');
                $trendAbsent = array_column($trend, 'absent');
                ?>
                const chart = new ApexCharts(document.querySelector("#deptTrendChart"), {
                    ...commonOptions,
                    series: [
                        { name: 'Present', data: <?= json_encode(array_map('intval', $trendPresent)) ?> },
                        { name: 'Absent', data: <?= json_encode(array_map('intval', $trendAbsent)) ?> }
                    ],
                    chart: { ...commonOptions.chart, type: 'area', height: 280, stacked: false },
                    colors: ['#10b981', '#ef4444'],
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } },
                    xaxis: { 
                        ...commonOptions.xaxis, 
                        categories: <?= json_encode(array_map(fn($d)=>date('M d',strtotime($d)), $trendLabels)) ?>,
                        tooltip: { enabled: false }
                    },
                    tooltip: { x: { format: 'dd MMM' } }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("Dept Trend Chart Error:", e); }
        }

        // 8.1 Dept Performance Distribution (Manager)
        if (document.getElementById('deptPerfChart')) {
            try {
                <?php
                $dpd = [];
                foreach(($roleData['dept_perf_dist']??[]) as $p) { $dpd[(int)$p['rating']] = (int)$p['cnt']; }
                $dpdSeries = [(int)($dpd[5]??0), (int)($dpd[4]??0), (int)($dpd[3]??0), (int)($dpd[2]??0), (int)($dpd[1]??0)];
                ?>
                const chart = new ApexCharts(document.querySelector("#deptPerfChart"), {
                    ...commonOptions,
                    series: [{ name: 'Employees', data: <?= json_encode($dpdSeries) ?> }],
                    chart: { ...commonOptions.chart, type: 'bar', height: 280 },
                    plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '60%' } },
                    colors: ['#6366f1'],
                    xaxis: { ...commonOptions.xaxis, categories: ['5 Stars', '4 Stars', '3 Stars', '2 Stars', '1 Star'] }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("Dept Perf Chart Error:", e); }
        }

        // 9. Employee Attendance Donut
        if (document.getElementById('myAttChart')) {
            try {
                <?php
                $myAtt = [];
                foreach(($roleData['attendance_summary']??[]) as $ms) {
                    $myAtt[$ms['status']] = (int)$ms['cnt'];
                }
                $myAttVals = [(int)($myAtt['present']??0), (int)($myAtt['absent']??0), (int)($myAtt['late']??0), (int)($myAtt['on_leave']??0)];
                $myAttLabels = array_sum($myAttVals) === 0 ? ['No Data'] : ['Present','Absent','Late','On Leave'];
                $myAttColors = array_sum($myAttVals) === 0 ? ['#94a3b8'] : ['#10b981','#ef4444','#f59e0b','#3b82f6'];
                ?>
                const chart = new ApexCharts(document.querySelector("#myAttChart"), {
                    ...commonOptions,
                    series: <?= json_encode(array_sum($myAttVals) === 0 ? [1] : $myAttVals) ?>,
                    labels: <?= json_encode($myAttLabels) ?>,
                    chart: { ...commonOptions.chart, type: 'donut', height: 260 },
                    colors: <?= json_encode($myAttColors) ?>,
                    stroke: { width: 1, colors: [cardBg] },
                    legend: { position: 'bottom' }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("My Attendance Chart Error:", e); }
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
