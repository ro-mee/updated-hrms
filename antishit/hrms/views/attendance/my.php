<?php
/** My Attendance (Employee self-service) */
$pageTitle  = 'My Attendance';
$breadcrumb = [['label'=>'My Attendance','active'=>true]];
include APP_ROOT . '/views/layouts/header.php';

$lunchDur = (int)$settings->get('lunch_duration', 60);
$break1Dur = (int)$settings->get('break1_duration', 15);
$break2Dur = (int)$settings->get('break2_duration', 15);
$eBreakDur = (int)$settings->get('emergency_break_duration', 2);
// Summary totals
$summaryMap = [];
foreach($summary as $s) { $summaryMap[$s['status']] = $s; }
?>
<div class="container-fluid px-4 py-3">
    <h5 class="fw-700 mb-3"><i class="bi bi-clock-history text-primary me-2"></i>My Attendance</h5>
    <!-- Clock Widget -->
    <style>
    .circular-progress { width: 140px; height: 140px; border-radius: 50%; background: conic-gradient(#0d6efd var(--progress), #e9ecef 0deg); display: flex; align-items: center; justify-content: center; position: relative; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .circular-progress::before { content: ""; width: 120px; height: 120px; border-radius: 50%; background-color: #fff; position: absolute; box-shadow: inset 0 2px 5px rgba(0,0,0,0.02); }
    .progress-value { position: relative; font-size: 1.6rem; font-weight: 800; color: #333; }
    .activity-timeline { padding-left: 20px; position: relative; }
    .timeline-item { position: relative; padding-bottom: 25px; padding-left: 25px; border-left: 2px solid #e9ecef; }
    .timeline-item:last-child { border-left: 2px solid transparent; padding-bottom: 0; }
    .timeline-icon { position: absolute; left: -14px; top: -2px; width: 26px; height: 26px; border-radius: 50%; border: 3px solid #fff; background: #2bd99b; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; box-shadow: 0 0 0 1px #e9ecef; }
    .timeline-content { position: relative; top: -3px; }
    </style>

    <!-- Dashboard Stats Grid -->
    <div class="row g-3 mb-4">
        <!-- Timesheet Card -->
        <div class="col-md-4">
            <div class="card h-100 p-3 shadow-sm border-0 position-relative">
                <h6 class="fw-bold mb-3 d-flex justify-content-between text-primary">
                    <span>Timesheet</span>
                    <span class="text-muted fw-normal" style="font-size: 0.8rem;"><?= date('d M Y') ?></span>
                </h6>
                <div class="border rounded-3 p-2 mb-4 d-flex align-items-center bg-light">
                    <div class="me-3 ps-2"><i class="bi bi-box-arrow-in-right fs-4 text-muted"></i></div>
                    <div>
                        <div class="fw-bold fs-6">Punch In at</div>
                        <div class="text-muted" style="font-size: 0.75rem;"><?= ($today && !empty($today['clock_in'])) ? date('D, jS M Y h.i A', strtotime($today['clock_in'])) : 'Not Clocked In' ?></div>
                    </div>
                </div>
                
                <!-- Circular Progress -->
                <?php 
                    $todayProgress = 0;
                    $todayHoursVal = 0;
                    if ($today && !empty($today['clock_in'])) {
                        $in = new DateTime($today['clock_in']);
                        $out = empty($today['clock_out']) ? new DateTime() : new DateTime($today['clock_out']);
                        $totalSeconds = $out->getTimestamp() - $in->getTimestamp();
                        $unpaidSeconds = 0;
                        if (!empty($today['break1_start']) && !empty($today['break1_end'])) $unpaidSeconds += (new DateTime($today['break1_end']))->getTimestamp() - (new DateTime($today['break1_start']))->getTimestamp();
                        if (!empty($today['break2_start']) && !empty($today['break2_end'])) $unpaidSeconds += (new DateTime($today['break2_end']))->getTimestamp() - (new DateTime($today['break2_start']))->getTimestamp();
                        if (!empty($today['emergency_break_start']) && !empty($today['emergency_break_end'])) $unpaidSeconds += (new DateTime($today['emergency_break_end']))->getTimestamp() - (new DateTime($today['emergency_break_start']))->getTimestamp();
                        $workedSeconds = max(0, $totalSeconds - $unpaidSeconds);
                        $todayHoursVal = round($workedSeconds / 3600, 2);
                        $todayProgress = min(100, ($todayHoursVal / 8) * 100);
                    }
                ?>
                <div class="d-flex justify-content-center mb-4">
                    <div class="circular-progress" style="--progress: <?= $todayProgress ?>%;">
                        <div class="progress-value"><?= $todayHoursVal ?> <span class="fs-6 text-muted fw-normal">hrs</span></div>
                    </div>
                </div>

                <!-- Action Button / Break Handlers -->
                <div class="text-center mb-4">
                    <?php if (!$today || empty($today['clock_in'])): ?>
                        <button class="btn btn-success rounded-pill px-5 fw-bold" onclick="handleClock('in')" id="clockInBtn" style="background:#2bd99b;border:none;">Punch In</button>
                    <?php elseif (empty($today['clock_out'])): ?>
                        
                        <?php if (!empty($today['lunch_start']) && empty($today['lunch_end'])): ?>
                            <div class="text-primary fw-600 mb-1"><i class="bi bi-cup me-1"></i>On Lunch Break</div>
                            <div id="breakTimer" class="fs-4 fw-bold mb-2 text-primary" data-start="<?= strtotime($today['lunch_start']) ?>" data-duration="<?= $lunchDur * 60 ?>"><?= $lunchDur ?>:00</div>
                            <button class="btn btn-primary rounded-pill px-5 fw-bold" onclick="handleBreak('endLunch')">End Lunch</button>
                        <?php elseif (!empty($today['break1_start']) && empty($today['break1_end'])): ?>
                            <div class="text-warning fw-600 mb-1"><i class="bi bi-cup-hot me-1"></i>On 1st Break</div>
                            <div id="breakTimer" class="fs-4 fw-bold mb-2 text-warning" data-start="<?= strtotime($today['break1_start']) ?>" data-duration="<?= $break1Dur * 60 ?>"><?= $break1Dur ?>:00</div>
                            <button class="btn btn-warning rounded-pill px-5 fw-bold text-white" onclick="handleBreak('endBreak1')">End 1st Break</button>
                        <?php elseif (!empty($today['break2_start']) && empty($today['break2_end'])): ?>
                            <div class="text-warning fw-600 mb-1"><i class="bi bi-cup-hot me-1"></i>On 2nd Break</div>
                            <div id="breakTimer" class="fs-4 fw-bold mb-2 text-warning" data-start="<?= strtotime($today['break2_start']) ?>" data-duration="<?= $break2Dur * 60 ?>"><?= $break2Dur ?>:00</div>
                            <button class="btn btn-warning rounded-pill px-5 fw-bold text-white" onclick="handleBreak('endBreak2')">End 2nd Break</button>
                        <?php elseif (!empty($today['emergency_break_start']) && empty($today['emergency_break_end'])): ?>
                            <div class="text-danger fw-600 mb-1"><i class="bi bi-exclamation-triangle me-1"></i>E-Break</div>
                            <div id="breakTimer" class="fs-4 fw-bold mb-2 text-danger" data-start="<?= strtotime($today['emergency_break_start']) ?>" data-duration="<?= $eBreakDur * 60 ?>"><?= str_pad($eBreakDur, 2, '0', STR_PAD_LEFT) ?>:00</div>
                            <button class="btn btn-danger rounded-pill px-5 fw-bold" onclick="handleBreak('endEmergencyBreak')">End E-Break</button>
                        <?php else: ?>
                            <button class="btn btn-success btn-lg rounded-pill px-5 fw-bold w-100 shadow-sm mb-3" onclick="handleClock('out')" id="clockOutBtn" style="background:#2bd99b;border:none;">Punch Out</button>
                            <div class="row g-2 px-1">
                                <?php if (empty($today['lunch_start'])): ?><div class="col-6"><button class="btn btn-sm btn-outline-primary w-100 rounded-pill" onclick="handleBreak('startLunch')">Lunch (<?= $lunchDur ?>m)</button></div><?php endif; ?>
                                <?php if (empty($today['break1_start'])): ?><div class="col-6"><button class="btn btn-sm btn-outline-warning w-100 rounded-pill" onclick="handleBreak('startBreak1')">Break 1 (<?= $break1Dur ?>m)</button></div><?php endif; ?>
                                <?php if (empty($today['break2_start'])): ?><div class="col-6"><button class="btn btn-sm btn-outline-warning w-100 rounded-pill" onclick="handleBreak('startBreak2')">Break 2 (<?= $break2Dur ?>m)</button></div><?php endif; ?>
                                <?php if (empty($today['emergency_break_start'])): ?><div class="col-6"><button class="btn btn-sm btn-outline-danger w-100 rounded-pill" onclick="handleBreak('startEmergencyBreak')">E-Break (<?= $eBreakDur ?>m)</button></div><?php endif; ?>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="text-success fw-bold fs-5 mt-2 mb-1"><i class="bi bi-check-circle-fill me-2"></i>Shift Completed</div>
                        <div class="text-muted" style="font-size:0.8rem;">Punch Out at <?= date('h:i A', strtotime($today['clock_out'])) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Footer Summary (Break & OT) -->
                <?php 
                    $todayOt = $today ? $today['overtime_hours'] : 0;
                    $totalBreakSecs = 0;
                    if ($today) {
                        if (!empty($today['break1_start']) && !empty($today['break1_end'])) $totalBreakSecs += (new DateTime($today['break1_end']))->getTimestamp() - (new DateTime($today['break1_start']))->getTimestamp();
                        if (!empty($today['break2_start']) && !empty($today['break2_end'])) $totalBreakSecs += (new DateTime($today['break2_end']))->getTimestamp() - (new DateTime($today['break2_start']))->getTimestamp();
                        if (!empty($today['lunch_start']) && !empty($today['lunch_end'])) $totalBreakSecs += (new DateTime($today['lunch_end']))->getTimestamp() - (new DateTime($today['lunch_start']))->getTimestamp();
                        if (!empty($today['emergency_break_start']) && !empty($today['emergency_break_end'])) $totalBreakSecs += (new DateTime($today['emergency_break_end']))->getTimestamp() - (new DateTime($today['emergency_break_start']))->getTimestamp();
                    }
                    $btHrs = round($totalBreakSecs / 3600, 2);
                ?>
                <div class="row text-center mt-auto border-top pt-3">
                    <div class="col border-end">
                        <div class="text-muted fw-bold" style="font-size:0.75rem;">BREAK</div>
                        <div class="fw-bold"><?= $btHrs ?> hrs</div>
                    </div>
                    <div class="col">
                        <div class="text-muted fw-bold" style="font-size:0.75rem;">OVERTIME</div>
                        <div class="fw-bold"><?= $todayOt ?> hrs</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="col-md-4">
            <div class="card h-100 p-4 shadow-sm border-0">
                <h6 class="fw-bold mb-4 text-primary">Statistics</h6>
                
                <?php
                    $monthHrs = round($monthlyStats['hours'], 2);
                    $monthOt = round($monthlyStats['ot'], 2);
                    $weekHrs = round($weeklyHours, 2);
                    
                    $dayLimit = (int)$settings->get('work_hours_per_day', 8);
                    $weekLimit = $dayLimit * 5;
                    $monthLimit = $dayLimit * 20;

                    $todayPct = min(100, ($todayHoursVal / $dayLimit) * 100);
                    $weekPct = min(100, ($weekHrs / $weekLimit) * 100);
                    $monthPct = min(100, ($monthHrs / $monthLimit) * 100);
                    $remPct = min(100, max(0, (($monthLimit - $monthHrs) / $monthLimit) * 100));
                ?>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1" style="font-size:0.85rem;">
                        <span class="text-muted fw-bold">Today</span>
                        <span class="fw-bold text-dark"><?= $todayHoursVal ?> <span class="text-muted fw-normal">/ <?= $dayLimit ?> hrs</span></span>
                    </div>
                    <div class="progress" style="height:6px; background:#e9ecef;"><div class="progress-bar" style="width: <?= $todayPct ?>%; background:#2bd99b; border-radius:10px;"></div></div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1" style="font-size:0.85rem;">
                        <span class="text-muted fw-bold">This Week</span>
                        <span class="fw-bold text-dark"><?= $weekHrs ?> <span class="text-muted fw-normal">/ <?= $weekLimit ?> hrs</span></span>
                    </div>
                    <div class="progress" style="height:6px; background:#e9ecef;"><div class="progress-bar" style="width: <?= $weekPct ?>%; background:#6366f1; border-radius:10px;"></div></div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1" style="font-size:0.85rem;">
                        <span class="text-muted fw-bold">This Month</span>
                        <span class="fw-bold text-dark"><?= $monthHrs ?> <span class="text-muted fw-normal">/ <?= $monthLimit ?> hrs</span></span>
                    </div>
                    <div class="progress" style="height:6px; background:#e9ecef;"><div class="progress-bar" style="width: <?= $monthPct ?>%; background:#00bcd4; border-radius:10px;"></div></div>
                </div>

                <div class="mb-4 pb-3 border-bottom">
                    <div class="d-flex justify-content-between mb-1" style="font-size:0.85rem;">
                        <span class="text-muted fw-bold">Remaining</span>
                        <span class="fw-bold text-dark"><?= max(0, $monthLimit - $monthHrs) ?> <span class="text-muted fw-normal">/ <?= $monthLimit ?> hrs</span></span>
                    </div>
                    <div class="progress" style="height:6px; background:#e9ecef;"><div class="progress-bar" style="width: <?= $remPct ?>%; background:#0d6efd; border-radius:10px;"></div></div>
                </div>

                <div class="mt-2">
                    <div class="d-flex justify-content-between mb-1" style="font-size:0.85rem;">
                        <span class="text-muted fw-bold">Overtime</span>
                        <span class="fw-bold text-dark"><?= $monthOt ?> hrs</span>
                    </div>
                    <div class="progress" style="height:6px; background:#e9ecef;"><div class="progress-bar bg-warning" style="width: <?= min(100, ($monthOt/20)*100) ?>%; border-radius:10px;"></div></div>
                </div>
            </div>
        </div>

        <!-- Today Activity Card -->
        <div class="col-md-4">
            <div class="card h-100 p-4 shadow-sm border-0">
                <h6 class="fw-bold mb-4 text-primary">Today Activity</h6>
                <div class="activity-timeline pt-2" style="overflow-y:auto; max-height: 380px; padding-right:10px;">
                    <?php if (!$today || empty($today['clock_in'])): ?>
                        <div class="text-center text-muted mt-5 pb-5"><i class="bi bi-clock-history fs-1 d-block mb-3 opacity-50"></i>No activity recorded today</div>
                    <?php else:
                        $events = [];
                        if (!empty($today['clock_in'])) $events[] = ['time' => strtotime($today['clock_in']), 'type' => 'Punch In', 'icon' => 'box-arrow-in-right', 'color' => '#2bd99b'];
                        if (!empty($today['clock_out'])) $events[] = ['time' => strtotime($today['clock_out']), 'type' => 'Punch Out', 'icon' => 'box-arrow-right', 'color' => '#ff6b6b'];
                        
                        if (!empty($today['lunch_start'])) $events[] = ['time' => strtotime($today['lunch_start']), 'type' => 'Lunch Start', 'icon' => 'cup', 'color' => '#0d6efd'];
                        if (!empty($today['lunch_end'])) $events[] = ['time' => strtotime($today['lunch_end']), 'type' => 'Lunch End', 'icon' => 'cup', 'color' => '#0d6efd'];
                        
                        if (!empty($today['break1_start'])) $events[] = ['time' => strtotime($today['break1_start']), 'type' => 'Break 1 Start', 'icon' => 'cup-hot', 'color' => '#fca311'];
                        if (!empty($today['break1_end'])) $events[] = ['time' => strtotime($today['break1_end']), 'type' => 'Break 1 End', 'icon' => 'cup-hot', 'color' => '#fca311'];
                        
                        if (!empty($today['break2_start'])) $events[] = ['time' => strtotime($today['break2_start']), 'type' => 'Break 2 Start', 'icon' => 'cup-hot', 'color' => '#fca311'];
                        if (!empty($today['break2_end'])) $events[] = ['time' => strtotime($today['break2_end']), 'type' => 'Break 2 End', 'icon' => 'cup-hot', 'color' => '#fca311'];
                        
                        if (!empty($today['emergency_break_start'])) $events[] = ['time' => strtotime($today['emergency_break_start']), 'type' => 'Emergency Break Start', 'icon' => 'exclamation-triangle', 'color' => '#dc3545'];
                        if (!empty($today['emergency_break_end'])) $events[] = ['time' => strtotime($today['emergency_break_end']), 'type' => 'Emergency Break End', 'icon' => 'exclamation-triangle', 'color' => '#dc3545'];
                        
                        usort($events, function($a, $b) { return $a['time'] <=> $b['time']; });
                        
                        foreach ($events as $e):
                    ?>
                        <div class="timeline-item">
                            <i class="bi bi-<?= $e['icon'] ?> timeline-icon" style="background: <?= $e['color'] ?>;"></i>
                            <div class="timeline-content">
                                <div class="fw-bold" style="font-size:0.9rem; color:#444;"><?= $e['type'] ?> <span class="fw-normal text-muted" style="font-size:0.8rem;">at</span></div>
                                <div class="text-muted" style="font-size:0.8rem;"><i class="bi bi-clock me-1 opacity-75"></i><?= date('h.i A', $e['time']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Records Table -->
    <div class="card table-card">
        <div class="card-header py-3"><i class="bi bi-table text-primary me-2"></i>Attendance Log – <?= date('F Y') ?></div>
        <div class="table-responsive">
            <table class="table table-hover table-lg mb-0">
                <thead><tr><th>Date</th><th>Clock In</th><th>Clock Out</th><th>Breaks</th><th>Hours</th><th>Overtime</th><th>Status</th></tr></thead>
                <tbody>
                <?php if(empty($records)): ?><tr><td colspan="6"><div class="empty-state"><i class="bi bi-clock-history"></i>No records found</div></td></tr><?php endif; ?>
                <?php foreach($records as $r): ?>
                <tr>
                    <td class="fw-medium"><?= formatDate($r['date'],'D, M d') ?></td>
                    <td class="small"><?= $r['clock_in'] ? date('h:i A',strtotime($r['clock_in'])) : '—' ?></td>
                    <td class="small"><?= $r['clock_out'] ? date('h:i A',strtotime($r['clock_out'])) : '—' ?></td>
                    <td class="small" style="min-width: 130px;">
                        <?php if ($r['lunch_start']) echo "<div class='text-primary' style='font-size:0.75rem'>Lunch: ".date('h:iA',strtotime($r['lunch_start']))."-".($r['lunch_end']?date('h:iA',strtotime($r['lunch_end'])):'—')."</div>"; ?>
                        <?php if ($r['break1_start']) echo "<div class='text-warning' style='font-size:0.75rem'>B1: ".date('h:iA',strtotime($r['break1_start']))."-".($r['break1_end']?date('h:iA',strtotime($r['break1_end'])):'—')."</div>"; ?>
                        <?php if ($r['break2_start']) echo "<div class='text-warning' style='font-size:0.75rem'>B2: ".date('h:iA',strtotime($r['break2_start']))."-".($r['break2_end']?date('h:iA',strtotime($r['break2_end'])):'—')."</div>"; ?>
                        <?php if ($r['emergency_break_start']) echo "<div class='text-danger' style='font-size:0.75rem'>E-Brk: ".date('h:iA',strtotime($r['emergency_break_start']))."-".($r['emergency_break_end']?date('h:iA',strtotime($r['emergency_break_end'])):'—')."</div>"; ?>
                        <?php if (!$r['lunch_start'] && !$r['break1_start'] && !$r['break2_start'] && !$r['emergency_break_start']) echo "<span class='text-muted'>—</span>"; ?>
                    </td>
                    <td><?= $r['hours_worked'] ? $r['hours_worked'].'h' : '—' ?></td>
                    <td><?= $r['overtime_hours'] > 0 ? '<span class="text-warning">+'.$r['overtime_hours'].'h</span>' : '—' ?></td>
                    <td><?= statusBadge($r['status']) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($pg['total_pages'] > 1): ?>
        <div class="card-footer d-flex justify-content-between align-items-center bg-transparent py-3">
            <small class="text-muted">Showing <?= count($records) ?> of <?= $pg['total'] ?> records</small>
            <?= paginationLinks($pg, 'index.php?module=attendance&action=my') ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
