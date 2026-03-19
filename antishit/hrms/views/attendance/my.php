<?php
/** My Attendance (Employee self-service) */
$pageTitle  = 'My Attendance';
$breadcrumb = [['label'=>'My Attendance','active'=>true]];
include APP_ROOT . '/views/layouts/header.php';
// Summary totals
$summaryMap = [];
foreach($summary as $s) { $summaryMap[$s['status']] = $s; }
?>
<div class="container-fluid px-4 py-3">
    <h5 class="fw-700 mb-3"><i class="bi bi-clock-history text-primary me-2"></i>My Attendance</h5>
    <!-- Clock Widget -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card clock-card">
                <div class="clock-display" id="liveClock"></div>
                <div class="clock-date mb-3" id="liveDate"></div>
                <?php if (!$today || empty($today['clock_in'])): ?>
                <button class="clock-btn clock-in-btn mx-auto" onclick="handleClock('in')" id="clockInBtn"><i class="bi bi-play-circle d-block fs-3 mb-1"></i>Clock In</button>
                <?php elseif (empty($today['clock_out'])): ?>
                <button class="clock-btn clock-out-btn mx-auto" onclick="handleClock('out')" id="clockOutBtn"><i class="bi bi-stop-circle d-block fs-3 mb-1"></i>Clock Out</button>
                <div class="text-muted small mt-2">In: <?= date('h:i A',strtotime($today['clock_in'])) ?></div>
                <?php else: ?>
                <div class="text-success fw-600 fs-5"><i class="bi bi-check-circle-fill me-1"></i>Shift Completed</div>
                <div class="text-muted small mt-1">In: <?= date('h:i A',strtotime($today['clock_in'])) ?> · Out: <?= date('h:i A',strtotime($today['clock_out'])) ?></div>
                <div class="fw-medium mt-1"><?= $today['hours_worked'] ?> hrs worked</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card p-3 h-100">
                <h6 class="fw-700 mb-3">This Month Summary – <?= date('F Y') ?></h6>
                <div class="row g-2">
                <?php $statMap=[['status'=>'present','color'=>'success','icon'=>'check2'],['status'=>'late','color'=>'warning','icon'=>'clock'],['status'=>'absent','color'=>'danger','icon'=>'x-circle'],['status'=>'on_leave','color'=>'info','icon'=>'calendar-check']]; ?>
                <?php foreach($statMap as $sm): $cnt=$summaryMap[$sm['status']]['cnt']??0; $hrs=$summaryMap[$sm['status']]['total_hours']??0; ?>
                <div class="col-6 col-md-3">
                    <div class="border rounded-10 p-2 text-center">
                        <div class="fw-700 fs-4 text-<?= $sm['color'] ?>"><?= $cnt ?></div>
                        <div class="small text-muted"><?= ucwords(str_replace('_',' ',$sm['status'])) ?></div>
                        <?php if($hrs > 0): ?><div style="font-size:.72rem" class="text-muted"><?= round($hrs,1) ?>h</div><?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Records Table -->
    <div class="card table-card">
        <div class="card-header py-3"><i class="bi bi-table text-primary me-2"></i>Attendance Log – <?= date('F Y') ?></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Date</th><th>Clock In</th><th>Clock Out</th><th>Hours</th><th>Overtime</th><th>Status</th></tr></thead>
                <tbody>
                <?php if(empty($records)): ?><tr><td colspan="6"><div class="empty-state"><i class="bi bi-clock-history"></i>No records this month</div></td></tr><?php endif; ?>
                <?php foreach($records as $r): ?>
                <tr>
                    <td class="fw-medium"><?= formatDate($r['date'],'D, M d') ?></td>
                    <td class="small"><?= $r['clock_in'] ? date('h:i A',strtotime($r['clock_in'])) : '—' ?></td>
                    <td class="small"><?= $r['clock_out'] ? date('h:i A',strtotime($r['clock_out'])) : '—' ?></td>
                    <td><?= $r['hours_worked'] ? $r['hours_worked'].'h' : '—' ?></td>
                    <td><?= $r['overtime_hours'] > 0 ? '<span class="text-warning">+'.$r['overtime_hours'].'h</span>' : '—' ?></td>
                    <td><?= statusBadge($r['status']) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
