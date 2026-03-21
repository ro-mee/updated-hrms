<?php /** Settings View */
$pageTitle='System Settings'; $breadcrumb=[['label'=>'Settings','active'=>true]];
include APP_ROOT.'/views/layouts/header.php';?>
<div class="container-fluid px-4 py-3">
<h5 class="fw-700 mb-3"><i class="bi bi-gear text-primary me-2"></i>System Settings</h5>
<form method="POST" action="index.php?module=settings&action=update">
<?=csrfField()?>
<div class="row g-3">
<div class="col-lg-6">
<div class="card">
    <div class="card-header py-3">Company Information</div>
    <div class="card-body">
        <div class="mb-3"><label class="form-label">Company Name</label><input type="text" name="company_name" class="form-control" value="<?=e($settings->get('company_name','NexaHR'))?>"></div>
        <div class="mb-3"><label class="form-label">Company Address</label><input type="text" name="company_address" class="form-control" value="<?=e($settings->get('company_address',''))?>"></div>
        <div class="mb-3"><label class="form-label">Company Phone</label><input type="text" name="company_phone" class="form-control" value="<?=e($settings->get('company_phone',''))?>"></div>
        <div class="mb-3"><label class="form-label">Company Email</label><input type="email" name="company_email" class="form-control" value="<?=e($settings->get('company_email',''))?>"></div>
    </div>
</div>
</div>
<div class="col-lg-6">
<div class="card">
    <div class="card-header py-3">Payroll Settings</div>
    <div class="card-body">
        <div class="mb-3"><label class="form-label">Work Hours/Day</label><input type="number" name="work_hours_per_day" class="form-control" value="<?=e($settings->get('work_hours_per_day',8))?>"></div>
        <div class="mb-3"><label class="form-label">Overtime Rate (multiplier)</label><input type="number" step="0.01" name="overtime_rate" class="form-control" value="<?=e($settings->get('overtime_rate',1.25))?>"></div>
        <div class="mb-3"><label class="form-label">Late Deduction Rate (₱/min)</label><input type="number" step="0.01" name="late_deduction_rate" class="form-control" value="<?=e($settings->get('late_deduction_rate',0))?>"></div>
        <div class="mb-3"><label class="form-label">SSS Employer Share (%)</label><input type="number" step="0.01" name="sss_employer_rate" class="form-control" value="<?=e($settings->get('sss_employer_rate',8))?>"></div>
    </div>
</div>
</div>
<div class="col-12">
<div class="card">
    <div class="card-header py-3">HR Settings</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Office Start Time</label><input type="time" name="office_start_time" class="form-control" value="<?=e($settings->get('office_start_time','08:00'))?>"></div>
            <div class="col-md-4"><label class="form-label">Office End Time</label><input type="time" name="office_end_time" class="form-control" value="<?=e($settings->get('office_end_time','17:00'))?>"></div>
            <div class="col-md-4"><label class="form-label">Late Grace Period (mins)</label><input type="number" name="late_grace_period" class="form-control" value="<?=e($settings->get('late_grace_period',15))?>"></div>
        </div>
    </div>
</div>
</div>
</div>
<div class="mt-3"><button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save Settings</button></div>
</form>

<h5 class="fw-700 mt-4 mb-3"><i class="bi bi-database-fill text-primary me-2"></i>Database Management</h5>
<div class="row g-3">
<div class="col-lg-7">
<div class="card h-100">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
        <span><i class="bi bi-cloud-arrow-up-fill me-2"></i>Database Backups</span>
        <a href="index.php?module=settings&action=backup" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>Create Backup</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3 border-0">Backup Name</th>
                        <th class="border-0">Date & Time</th>
                        <th class="border-0">Size</th>
                        <th class="text-end pe-3 border-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($backups)): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">No backups found.</td></tr>
                    <?php else: foreach ($backups as $file): 
                        $basename = basename($file);
                        $parts = explode('_', $basename);
                        $date = $parts[1] ?? 'Unknown';
                        $time = isset($parts[2]) ? str_replace('.sql', '', str_replace('-', ':', $parts[2])) : '';
                    ?>
                        <tr>
                            <td class="ps-3 fw-500"><?=e($basename)?></td>
                            <td><?=e($date)?> <?=e($time)?></td>
                            <td><?=round(filesize($file)/1024, 2)?> KB</td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?module=settings&action=downloadBackup&file=<?=urlencode($basename)?>" class="btn btn-outline-info" title="Download"><i class="bi bi-download"></i></a>
                                    <a href="index.php?module=settings&action=deleteBackup&file=<?=urlencode($basename)?>" class="btn btn-outline-danger" onclick="return confirm('Delete this backup?')" title="Delete"><i class="bi bi-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<div class="col-lg-5">
<div class="card h-100">
    <div class="card-header py-3">
        <i class="bi bi-arrow-repeat me-2"></i>Restore Database
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">Restore your database from an uploaded SQL backup file. <span class="text-danger fw-bold">Warning: This will overwrite your current database.</span></p>
        <form action="index.php?module=settings&action=restore" method="POST" enctype="multipart/form-data">
            <?=csrfField()?>
            <div class="mb-3">
                <label class="form-label">Select Backup File (.sql)</label>
                <input type="file" name="backup_file" class="form-control" accept=".sql" required>
            </div>
            <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Are you sure you want to restore the database? Current data will be lost.')">
                <i class="bi bi-arrow-repeat me-1"></i>Restore Database
            </button>
        </form>
    </div>
</div>
</div>
</div>
</div>
<?php include APP_ROOT.'/views/layouts/footer.php';?>
