<?php
$pageTitle = 'Payroll Report';
$breadcrumb = [
    ['label' => 'Reports', 'url' => 'index.php?module=reports'],
    ['label' => 'Payroll Report', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-cash-stack text-primary me-2"></i>Payroll Summary Report</h6>
            <a href="index.php?module=reports" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Back to Reports</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-graph-up text-primary me-2"></i>Payroll Trends (Last 12 Periods)</h6>
            <div style="height: 300px;">
                <canvas id="payrollTrendChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="table-responsive" style="max-height: 500px">
            <table class="table table-hover table-sm align-middle mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>Period Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Pay Date</th>
                        <th class="text-center">Employees</th>
                        <th class="text-end">Total Gross</th>
                        <th class="text-end">Total Deductions</th>
                        <th class="text-end">Total Net</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($periods)): ?><tr><td colspan="9">No records found.</td></tr><?php endif; ?>
                <?php foreach($periods as $p): ?>
                <tr>
                    <td class="fw-medium"><?= e($p['period_name']) ?></td>
                    <td><?= formatDate($p['start_date']) ?></td>
                    <td><?= formatDate($p['end_date']) ?></td>
                    <td><?= formatDate($p['pay_date']) ?></td>
                    <td class="text-center"><?= $p['employees']??0 ?></td>
                    <td class="text-end text-primary"><?= formatCurrency($p['total_gross']??0) ?></td>
                    <td class="text-end text-danger">-<?= formatCurrency($p['total_deductions']??0) ?></td>
                    <td class="text-end fw-bold text-success"><?= formatCurrency($p['total_net']??0) ?></td>
                    <td><?= statusBadge($p['status']) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('payrollTrendChart').getContext('2d');
    
    // Prepare data from PHP
    <?php
        $reversed = array_reverse($periods);
        $labels = array_map(function($p) { return $p['period_name']; }, $reversed);
        $gross  = array_map(function($p) { return (float)($p['total_gross']??0); }, $reversed);
        $net    = array_map(function($p) { return (float)($p['total_net']??0); }, $reversed);
    ?>
    
    const labels = <?= json_encode($labels) ?>;
    const grossData = <?= json_encode($gross) ?>;
    const netData = <?= json_encode($net) ?>;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total Gross Pay',
                    data: grossData,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Total Net Pay',
                    data: netData,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
