<?php
$pageTitle = 'Attendance Report';
$breadcrumb = [
    ['label' => 'Reports', 'url' => 'index.php?module=reports'],
    ['label' => 'Attendance Report', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-3 align-items-end">
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="attendance">
                
                <div class="col-md-3">
                    <label class="form-label small text-muted">From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="<?= e(get('date_from', date('Y-m-01'))) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="<?= e(get('date_to', date('Y-m-d'))) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Department</label>
                    <select name="department_id" class="form-select form-select-sm">
                        <option value="">All Departments</option>
                        <?php foreach($departments as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= get('department_id')==$d['id']?'selected':'' ?>><?= e($d['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="bi bi-filter me-1"></i>Filter</button>
                    <button type="submit" name="export" value="csv" class="btn btn-success btn-sm flex-grow-1"><i class="bi bi-download me-1"></i>CSV</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card table-card">
        <div class="table-responsive" style="max-height: 500px">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>Date</th>
                        <th>Employee No</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Hours</th>
                        <th>OT</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($records)): ?><tr><td colspan="9">No records found.</td></tr><?php endif; ?>
                <?php foreach($records as $rec): ?>
                <tr>
                    <td><?= formatDate($rec['date']) ?></td>
                    <td><?= e($rec['employee_number']) ?></td>
                    <td><?= e($rec['full_name']) ?></td>
                    <td><?= e($rec['department_name']) ?></td>
                    <td><?= statusBadge($rec['status']) ?></td>
                    <td><?= $rec['clock_in'] ? date('h:i A', strtotime($rec['clock_in'])) : '--' ?></td>
                    <td><?= $rec['clock_out'] ? date('h:i A', strtotime($rec['clock_out'])) : '--' ?></td>
                    <td class="text-center"><?= $rec['hours_worked'] ?></td>
                    <td class="text-center"><?= $rec['overtime_hours'] ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
