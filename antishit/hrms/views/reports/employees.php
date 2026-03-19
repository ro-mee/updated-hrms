<?php
$pageTitle = 'Employee Report';
$breadcrumb = [
    ['label' => 'Reports', 'url' => 'index.php?module=reports'],
    ['label' => 'Employee Report', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-cloud-download text-primary me-2"></i>Export Employee Data</h6>
            <form method="GET" action="index.php" class="d-flex gap-2">
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="employees">
                <button type="submit" name="export" value="csv" class="btn btn-success"><i class="bi bi-filetype-csv me-2"></i>Download Full CSV Report</button>
                <a href="index.php?module=reports" class="btn btn-light">Back to Reports</a>
            </form>
        </div>
    </div>

    <div class="card table-card">
        <div class="table-responsive" style="max-height: 500px">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>Employee No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Date Hired</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($employees)): ?><tr><td colspan="8">No records found.</td></tr><?php endif; ?>
                <?php foreach($employees as $emp): ?>
                <tr>
                    <td><?= e($emp['employee_number']) ?></td>
                    <td><?= e($emp['full_name']) ?></td>
                    <td><?= e($emp['email']) ?></td>
                    <td><?= e($emp['department_name']) ?></td>
                    <td><?= e($emp['position_title']) ?></td>
                    <td><?= ucfirst(str_replace('_',' ',$emp['employment_type'])) ?></td>
                    <td><?= statusBadge($emp['status']) ?></td>
                    <td><?= formatDate($emp['date_hired']) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
