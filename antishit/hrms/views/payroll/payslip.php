<?php
/** Payslip View - printable */
$pageTitle='Payslip';
$settings2 = new Setting();
$companyName2 = $settings2->get('company_name','HRMS Pro');
$companyAddr  = $settings2->get('company_address','');
include APP_ROOT . '/views/layouts/header.php';
?>

<div class="container py-5" id="payslipContainer">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print" style="max-width:800px;margin:auto">
        <h4 class="fw-bold mb-0 text-main"><i class="bi bi-file-earmark-spreadsheet me-2 text-primary"></i>Employee Payslip</h4>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary shadow-sm"><i class="bi bi-printer me-2"></i>Print / Save PDF</button>
            <a href="index.php?module=payroll&action=myPayslips" class="btn btn-light border shadow-sm">Back to List</a>
        </div>
    </div>

    <div class="payslip-premium-card shadow-lg" style="max-width:800px;margin:auto">
        <!-- Premium Header -->
        <div class="payslip-premium-header">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="d-flex align-items-center">
                        <div class="brand-icon-box me-3" style="width:50px;height:50px;font-size:1.5rem;"><i class="bi bi-buildings"></i></div>
                        <div>
                            <h3 class="fw-800 mb-0 text-primary" style="letter-spacing:-0.5px"><?= e($companyName2) ?></h3>
                            <div class="text-secondary small fw-500"><?= e($companyAddr) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0 border-start-md ps-md-4">
                    <div class="badge bg-primary text-uppercase px-3 py-2 mb-2" style="font-size:0.7rem;letter-spacing:1px">Official Payment Advice</div>
                    <h2 class="fw-900 mb-0 text-main">PAYSLIP</h2>
                    <div class="text-secondary small mt-1">Period ID: <span class="text-main fw-600">#<?= $payslip['period_id'] ?></span></div>
                </div>
            </div>
        </div>

        <div class="payslip-premium-body">
            <!-- Summary Info -->
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="p-3 rounded-12 bg-light border border-dashed h-100" style="background: rgba(255,255,255,0.02) !important;">
                        <h6 class="text-uppercase text-primary fw-700 small mb-3" style="letter-spacing:1px">Employee Details</h6>
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?= avatarUrl($payslip['avatar'] ?? null) ?>" class="rounded-circle border me-3" width="54" height="54" style="object-fit:cover">
                            <div>
                                <div class="h5 fw-700 mb-0 text-main"><?= e($payslip['full_name']) ?></div>
                                <div class="text-secondary small"><?= e($payslip['employee_number']) ?> &bull; <?= e($payslip['position_title']) ?></div>
                            </div>
                        </div>
                        <div class="small text-secondary mt-2">Department: <span class="text-main"><?= e($payslip['department_name']) ?></span></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-12 bg-light border border-dashed h-100" style="background: rgba(255,255,255,0.02) !important;">
                        <h6 class="text-uppercase text-primary fw-700 small mb-3" style="letter-spacing:1px">Pay Information</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="text-secondary small">Pay Date</div>
                                <div class="fw-600 text-main"><?= formatDate($payslip['pay_date']) ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-secondary small">Period Name</div>
                                <div class="fw-600 text-main"><?= e($payslip['period_name']) ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-secondary small">Days Worked</div>
                                <div class="fw-600 text-main"><?= number_format($payslip['days_worked'], 2) ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-secondary small">OT Hours</div>
                                <div class="fw-600 text-main"><?= number_format($payslip['overtime_hours'], 2) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="row g-5 mb-5">
                <!-- Earnings Column -->
                <div class="col-md-6 border-end">
                    <h6 class="text-uppercase text-main fw-800 small mb-3 d-flex align-items-center">
                        <i class="bi bi-plus-circle-fill text-success me-2"></i>Earnings
                    </h6>
                    <ul class="list-group list-group-flush bg-transparent">
                        <li class="list-group-item d-flex justify-content-between px-0 bg-transparent border-0 py-2">
                            <span class="text-secondary">Basic Pay</span>
                            <span class="fw-600 text-main"><?= formatCurrency($payslip['basic_salary']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0 bg-transparent border-0 py-2">
                            <span class="text-secondary">Overtime Pay</span>
                            <span class="fw-600 text-main"><?= formatCurrency($payslip['overtime_pay']) ?></span>
                        </li>
                        <?php if($payslip['allowances']>0): ?>
                        <li class="list-group-item d-flex justify-content-between px-0 bg-transparent border-0 py-2">
                            <span class="text-secondary">Allowances</span>
                            <span class="fw-600 text-main"><?= formatCurrency($payslip['allowances']) ?></span>
                        </li>
                        <?php endif; ?>
                        <?php if($payslip['bonuses']>0): ?>
                        <li class="list-group-item d-flex justify-content-between px-0 bg-transparent border-0 py-2">
                            <span class="text-secondary">Bonuses</span>
                            <span class="fw-600 text-main"><?= formatCurrency($payslip['bonuses']) ?></span>
                        </li>
                        <?php endif; ?>
                        <li class="list-group-item d-flex justify-content-between px-0 bg-transparent mt-2 border-top-0 py-3" style="border-top: 1px solid var(--hrms-card-border)">
                            <span class="fw-800 text-main">GROSS PAY</span>
                            <span class="fw-800 text-success" style="font-size:1.1rem"><?= formatCurrency($payslip['gross_pay']) ?></span>
                        </li>
                    </ul>
                </div>

                <!-- Deductions Column -->
                <div class="col-md-6">
                    <h6 class="text-uppercase text-main fw-800 small mb-3 d-flex align-items-center">
                        <i class="bi bi-dash-circle-fill text-danger me-2"></i>Deductions
                    </h6>
                    <ul class="list-group list-group-flush bg-transparent">
                        <li class="list-group-item d-flex justify-content-between px-0 bg-transparent border-0 py-2">
                            <span class="text-secondary">SSS Contribution</span>
                            <span class="fw-600 text-danger"><?= formatCurrency($payslip['sss_deduction']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0 bg-transparent border-0 py-2">
                            <span class="text-secondary">PhilHealth</span>
                            <span class="fw-600 text-danger"><?= formatCurrency($payslip['philhealth_deduction']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0 bg-transparent border-0 py-2">
                            <span class="text-secondary">Pag-IBIG</span>
                            <span class="fw-600 text-danger"><?= formatCurrency($payslip['pagibig_deduction']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0 bg-transparent border-0 py-2">
                            <span class="text-secondary">Withholding Tax</span>
                            <span class="fw-600 text-danger"><?= formatCurrency($payslip['tax_deduction']) ?></span>
                        </li>
                        <?php if($payslip['other_deductions']>0): ?>
                        <li class="list-group-item d-flex justify-content-between px-0 bg-transparent border-0 py-2">
                            <span class="text-secondary">Other Deductions</span>
                            <span class="fw-600 text-danger"><?= formatCurrency($payslip['other_deductions']) ?></span>
                        </li>
                        <?php endif; ?>
                        <li class="list-group-item d-flex justify-content-between px-0 bg-transparent mt-2 border-top-0 py-3" style="border-top: 1px solid var(--hrms-card-border)">
                            <span class="fw-800 text-main">TOTAL DEDUCTIONS</span>
                            <span class="fw-800 text-danger" style="font-size:1.1rem"><?= formatCurrency($payslip['total_deductions']) ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Premium Net Pay Card -->
            <div class="net-pay-premium">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <div class="text-white-50 fw-600 text-uppercase small mb-1" style="letter-spacing:2px">Total Take-Home Pay</div>
                        <h5 class="mb-0 fw-500">Net Salary for this Period</h5>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div style="font-size:3.2rem; font-weight:900; line-height:1; letter-spacing:-1.5px">
                            <?= formatCurrency($payslip['net_pay']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 text-center">
                <p class="text-muted small mb-1">Generated by HRMS Pro System on <?= date('M d, Y h:i A') ?></p>
                <div class="text-secondary" style="font-size:0.65rem; opacity:0.6">ELECTRONICALLY GENERATED PAYSLIP - NO SIGNATURE REQUIRED</div>
            </div>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
