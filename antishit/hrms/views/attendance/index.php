<?php
/** Admin Attendance View */
$pageTitle  = 'Attendance Management';
$breadcrumb = [['label'=>'Attendance','active'=>true]];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-700 mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Attendance Management</h5>
        <a href="index.php?module=reports&action=attendance" class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-1"></i>Export</a>
    </div>
    <!-- Filters -->
    <form class="card p-3 mb-3" method="GET" action="index.php">
        <input type="hidden" name="module" value="attendance">
        <div class="row g-2 align-items-end">
            <div class="col-md-3"><label class="form-label">Date From</label><input type="date" name="date_from" class="form-control" value="<?= e(get('date_from',date('Y-m-01'))) ?>"></div>
            <div class="col-md-3"><label class="form-label">Date To</label><input type="date" name="date_to" class="form-control" value="<?= e(get('date_to',date('Y-m-d'))) ?>"></div>
            <div class="col-md-2">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select">
                    <option value="">All</option>
                    <?php foreach($departments as $d): ?><option value="<?=$d['id']?>" <?= get('department_id')==$d['id']?'selected':'' ?>><?= e($d['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <?php foreach(['present','absent','late','half_day','on_leave'] as $s): ?><option value="<?=$s?>" <?= get('status')===$s?'selected':'' ?>><?= ucwords(str_replace('_',' ',$s)) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="index.php?module=attendance" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            </div>
        </div>
    </form>
    <div class="card table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Employee</th><th>Dept</th><th>Date</th><th>In</th><th>Out</th><th>Hours</th><th>OT</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php if(empty($records)): ?><tr><td colspan="9"><div class="empty-state"><i class="bi bi-clock-history"></i>No attendance records found</div></td></tr><?php endif; ?>
                <?php foreach($records as $r): ?>
                <tr>
                    <td><div class="fw-medium small"><?= e($r['full_name']) ?></div><div class="text-muted" style="font-size:.72rem"><?= e($r['employee_number']) ?></div></td>
                    <td class="small"><?= e($r['department_name']) ?></td>
                    <td class="small"><?= formatDate($r['date'],'M d, Y') ?></td>
                    <td class="small"><?= $r['clock_in'] ? date('h:i A',strtotime($r['clock_in'])) : '—' ?></td>
                    <td class="small"><?= $r['clock_out'] ? date('h:i A',strtotime($r['clock_out'])) : '—' ?></td>
                    <td class="small"><?= $r['hours_worked']??'—' ?></td>
                    <td class="small"><?= $r['overtime_hours'] > 0 ? '+'.($r['overtime_hours']) : '—' ?></td>
                    <td><?= statusBadge($r['status']) ?></td>
                    <td><a href="index.php?module=attendance&action=edit&employee_id=<?= $r['employee_id'] ?>&date=<?= $r['date'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if($total > RECORDS_PER_PAGE): ?>
        <div class="card-footer d-flex justify-content-between align-items-center bg-transparent">
            <small class="text-muted">Showing <?= count($records) ?> of <?= $total ?></small>
            <?= paginationLinks($pg,'index.php?module=attendance&date_from='.get('date_from').'&date_to='.get('date_to').'&department_id='.get('department_id').'&status='.get('status')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
