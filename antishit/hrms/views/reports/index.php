<?php /** Reports Dashboard */
$pageTitle='Reports Dashboard'; $breadcrumb=[['label'=>'Reports','active'=>true]];
include APP_ROOT.'/views/layouts/header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

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
    <!-- Summary Cards -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="report-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-medium">Total Employees</span>
                    <i class="bi bi-people text-secondary"></i>
                </div>
                <h4 class="mb-1 fw-bold"><?= $stats['total_employees'] ?></h4>
                <div class="small <?= $stats['emp_trend_color'] ?>"><?= $stats['emp_trend'] ?> from last month</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="report-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-medium">New Hires (MTD)</span>
                    <i class="bi bi-graph-up-arrow text-secondary"></i>
                </div>
                <h4 class="mb-1 fw-bold"><?= $stats['new_hires_mtd'] ?></h4>
                <div class="small <?= $stats['hire_trend_color'] ?>"><?= $stats['new_hires_trend'] ?> from last month</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="report-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-medium">Avg Attendance</span>
                    <i class="bi bi-calendar2-check text-secondary"></i>
                </div>
                <h4 class="mb-1 fw-bold"><?= $stats['avg_attendance'] ?></h4>
                <div class="small <?= $stats['att_trend_color'] ?>"><?= $stats['att_trend'] ?> from last month</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="report-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-medium">Avg Salary</span>
                    <i class="bi bi-currency-exchange text-secondary"></i>
                </div>
                <h4 class="mb-1 fw-bold"><?= $stats['avg_salary'] ?></h4>
                <div class="small text-secondary">Per employee/year</div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 (Payroll Trends - Priority) -->
    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Monthly Payroll Trend</div>
                <div class="report-card-subtitle">Gross pay and deductions breakdown</div>
                <div id="chart-payroll-trend"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Department Payroll Cost</div>
                <div class="report-card-subtitle">Current period breakdown by department</div>
                <div id="chart-dept-payroll-cost"></div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Employee Growth Trend</div>
                <div class="report-card-subtitle">Last 6 months</div>
                <div id="chart-growth"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Department Distribution</div>
                <div class="report-card-subtitle">Employee count by department</div>
                <div id="chart-department" class="d-flex justify-content-center align-items-center" style="min-height:300px"></div>
            </div>
        </div>
    </div>

    <!-- Charts Row 3 -->
    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Leave Request Status by Month</div>
                <div class="report-card-subtitle">Approved, pending, and rejected requests</div>
                <div id="chart-leave-status"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="report-card p-3 h-100">
                <div class="report-card-title">Weekly Attendance Overview</div>
                <div class="report-card-subtitle">This week's status</div>
                <div id="chart-attendance"></div>
            </div>
        </div>
    </div>
</div>

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
        const legendColor = isDark ? '#c7d2fe' : '#475569';

        const commonOptions = {
            chart: {
                toolbar: { show: true },
                fontFamily: 'Inter, sans-serif',
                background: 'transparent',
                animations: { enabled: true }
            },
            theme: { mode: isDark ? 'dark' : 'light' },
            dataLabels: { enabled: false },
            grid: { borderColor: gridColor, strokeDashArray: 4 },
            xaxis: { labels: { style: { colors: textColor } }, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { labels: { style: { colors: textColor } } },
            legend: { position: 'bottom', labels: { colors: legendColor } }
        };

        // 1. Employee Growth Trend (Area)
        if (document.getElementById('chart-growth')) {
            try {
                const chart = new ApexCharts(document.querySelector("#chart-growth"), {
                    ...commonOptions,
                    series: [{ name: "Total Employees", data: <?= json_encode($growthSeries) ?> }],
                    chart: { ...commonOptions.chart, type: 'area', height: 320 },
                    colors: ['#3b82f6'],
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [50, 100] } },
                    stroke: { curve: 'smooth', width: 2 },
                    xaxis: { ...commonOptions.xaxis, categories: <?= json_encode($growthMonths) ?> },
                    yaxis: { ...commonOptions.yaxis, labels: { ...commonOptions.yaxis.labels, formatter: (val) => Math.floor(val) } }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("Growth Chart Error:", e); }
        }

        // 2. Department Distribution (Pie)
        if (document.getElementById('chart-department')) {
            try {
                const chart = new ApexCharts(document.querySelector("#chart-department"), {
                    ...commonOptions,
                    series: <?= json_encode($deptSeries) ?>,
                    labels: <?= json_encode($deptLabels) ?>,
                    chart: { ...commonOptions.chart, type: 'pie', height: 320 },
                    colors: ['#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#3b82f6', '#14b8a6', '#f43f5e'],
                    stroke: { width: 1, colors: [cardBg] },
                    legend: { ...commonOptions.legend, position: 'right', offsetY: 0, height: 230 }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("Department Chart Error:", e); }
        }

        // 3. Leave Request Status (Bar)
        if (document.getElementById('chart-leave-status')) {
            try {
                const chart = new ApexCharts(document.querySelector("#chart-leave-status"), {
                    ...commonOptions,
                    series: [
                        { name: "Approved", data: <?= json_encode($leaveSeriesDB['approved']) ?> },
                        { name: "Pending", data: <?= json_encode($leaveSeriesDB['pending']) ?> },
                        { name: "Rejected", data: <?= json_encode($leaveSeriesDB['rejected']) ?> }
                    ],
                    chart: { ...commonOptions.chart, type: 'bar', height: 300 },
                    colors: ['#10b981', '#f59e0b', '#ef4444'],
                    plotOptions: { bar: { borderRadius: 2, columnWidth: '55%' } },
                    xaxis: { ...commonOptions.xaxis, categories: <?= json_encode($leaveMonths) ?> }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("Leave Status Chart Error:", e); }
        }

        // 4. Weekly Attendance Overview (Stacked Bar)
        if (document.getElementById('chart-attendance')) {
            try {
                const chart = new ApexCharts(document.querySelector("#chart-attendance"), {
                    ...commonOptions,
                    series: [
                        { name: "Present", data: <?= json_encode($attSeriesDB['present']) ?> },
                        { name: "On Leave", data: <?= json_encode($attSeriesDB['on_leave']) ?> },
                        { name: "Absent", data: <?= json_encode($attSeriesDB['absent']) ?> }
                    ],
                    chart: { ...commonOptions.chart, type: 'bar', height: 300, stacked: true },
                    colors: ['#10b981', '#f59e0b', '#ef4444'],
                    plotOptions: { bar: { borderRadius: 2, columnWidth: '50%' } },
                    xaxis: { ...commonOptions.xaxis, categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'] }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("Attendance Chart Error:", e); }
        }

        // 5. Monthly Payroll Trend (Area)
        if (document.getElementById('chart-payroll-trend')) {
            try {
                const chart = new ApexCharts(document.querySelector("#chart-payroll-trend"), {
                    ...commonOptions,
                    series: [
                        { name: "Gross Pay", data: <?= json_encode($payrollGross) ?> },
                        { name: "Deductions", data: <?= json_encode($payrollDeductions) ?> }
                    ],
                    chart: { ...commonOptions.chart, type: 'area', height: 350 },
                    colors: ['#6366f1', '#ef4444'],
                    fill: { type: 'solid', opacity: 0.6 },
                    stroke: { curve: 'smooth', width: 2 },
                    xaxis: { ...commonOptions.xaxis, categories: <?= json_encode($payrollMonths) ?> },
                    yaxis: { ...commonOptions.yaxis, labels: { ...commonOptions.yaxis.labels, formatter: (val) => '₱' + val.toLocaleString() } }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("Payroll Trend Chart Error:", e); }
        }

        // 6. Department Payroll Cost (Bar)
        if (document.getElementById('chart-dept-payroll-cost')) {
            try {
                const chart = new ApexCharts(document.querySelector("#chart-dept-payroll-cost"), {
                    ...commonOptions,
                    series: [
                        { name: "Total Cost", data: <?= json_encode($deptTotalCost) ?> },
                        { name: "Avg Salary", data: <?= json_encode($deptAvgSalary) ?> }
                    ],
                    chart: { ...commonOptions.chart, type: 'bar', height: 400 },
                    colors: ['#3b82f6', '#10b981'],
                    plotOptions: { bar: { horizontal: false, columnWidth: '45%', borderRadius: 4 } },
                    xaxis: { ...commonOptions.xaxis, categories: <?= json_encode($deptPayrollLabels) ?> },
                    yaxis: { ...commonOptions.yaxis, labels: { ...commonOptions.yaxis.labels, formatter: (val) => '₱' + val.toLocaleString() } },
                    tooltip: { y: { formatter: (val) => '₱' + val.toLocaleString() } }
                });
                chart.render();
                chartInstances.push(chart);
            } catch (e) { console.error("Dept Payroll Chart Error:", e); }
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

<?php include APP_ROOT.'/views/layouts/footer.php'; ?>
