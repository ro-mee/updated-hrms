<?php
/** Employee View/Profile */
$pageTitle  = 'Employee: ' . e($employee['full_name']);
$breadcrumb = [['label'=>'Employees','url'=>'index.php?module=employees','active'=>false],['label'=>$employee['full_name'],'active'=>true]];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
<div class="row g-3">
    <!-- Left: Profile Card -->
    <div class="col-lg-4">
        <div class="card text-center p-4">
            <img src="<?= avatarUrl($employee['avatar']) ?>" class="rounded-circle mx-auto mb-3 avatar-lg" style="width:90px;height:90px" alt="">
            <h5 class="fw-700 mb-0"><?= e($employee['full_name']) ?></h5>
            <p class="text-muted small mb-1"><?= e($employee['position_title']) ?></p>
            <p class="text-primary small mb-2"><?= e($employee['department_name']) ?></p>
            <?= statusBadge($employee['status']) ?>
            <hr>
            <div class="text-start small">
                <div class="mb-2"><i class="bi bi-envelope text-muted me-2"></i><?= e($employee['email']) ?></div>
                <div class="mb-2"><i class="bi bi-telephone text-muted me-2"></i><?= e($employee['phone']??'—') ?></div>
                <div class="mb-2"><i class="bi bi-card-list text-muted me-2"></i><?= e($employee['employee_number']) ?></div>
                <div class="mb-2"><i class="bi bi-calendar text-muted me-2"></i>Hired: <?= formatDate($employee['date_hired']) ?></div>
                <div class="mb-2"><i class="bi bi-person-badge text-muted me-2"></i>Role: <?= e($employee['role_name']) ?></div>
                <div class="mb-2"><i class="bi bi-briefcase text-muted me-2"></i><?= ucwords(str_replace('_',' ',$employee['employment_type'])) ?></div>
                <?php if ($employee['basic_salary'] && can('payroll','view')): ?>
                <div class="mb-2"><i class="bi bi-cash text-muted me-2"></i><?= formatCurrency($employee['basic_salary']) ?>/mo</div>
                <?php endif; ?>
            </div>
            <?php if (can('employees','edit')): ?>
            <a href="index.php?module=employees&action=edit&id=<?= $employee['id'] ?>" class="btn btn-outline-primary btn-sm mt-2 w-100"><i class="bi bi-pencil me-1"></i>Edit Employee</a>
            <?php endif; ?>
        </div>
        <!-- Gov IDs -->
        <div class="card mt-3 p-3">
            <h6 class="fw-700 mb-2 small text-muted text-uppercase">Government IDs</h6>
            <div class="small"><div class="mb-1"><span class="text-muted">SSS:</span> <?= e($employee['sss_number']??'—') ?></div>
            <div class="mb-1"><span class="text-muted">PhilHealth:</span> <?= e($employee['philhealth_number']??'—') ?></div>
            <div class="mb-1"><span class="text-muted">Pag-IBIG:</span> <?= e($employee['pagibig_number']??'—') ?></div>
            <div><span class="text-muted">TIN:</span> <?= e($employee['tin_number']??'—') ?></div></div>
        </div>
    </div>
    <!-- Right: Tabs -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header p-0">
                <ul class="nav nav-tabs card-header-tabs px-3" id="empTabs">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabLeaves">Leaves</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabAtt">Attendance</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabDocs">Documents</button></li>
                </ul>
            </div>
            <div class="tab-content p-3">
                <!-- Leaves -->
                <div class="tab-pane fade show active" id="tabLeaves">
                    <h6 class="fw-700 mb-3">Leave Balance (<?= date('Y') ?>)</h6>
                    <div class="row g-2 mb-3">
                    <?php foreach($leaveBalance as $b): ?>
                    <div class="col-6 col-md-4">
                        <div class="border rounded-10 p-2 text-center">
                            <div class="fw-700 fs-5 text-<?= $b['remaining']>5?'success':($b['remaining']>0?'warning':'danger') ?>"><?= $b['remaining'] ?></div>
                            <div class="small text-muted"><?= e($b['leave_type_name']) ?></div>
                            <div style="font-size:.7rem" class="text-muted"><?= $b['used'] ?>/<?= $b['allocated'] ?> used</div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    </div>
                    <h6 class="fw-700 mb-2">Recent Leave Requests</h6>
                    <table class="table table-sm table-hover">
                        <thead><tr><th>Type</th><th>Period</th><th>Days</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach($leaves as $lv): ?>
                        <tr><td class="small"><?= e($lv['leave_type_name']) ?></td><td class="small"><?= formatDate($lv['start_date'],'M d') ?>–<?= formatDate($lv['end_date'],'M d, Y') ?></td><td><?= $lv['days_requested'] ?></td><td><?= statusBadge($lv['status']) ?></td></tr>
                        <?php endforeach; ?>
                        <?php if(empty($leaves)): ?><tr><td colspan="4" class="text-center text-muted py-3">No leave records</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Attendance -->
                <div class="tab-pane fade" id="tabAtt">
                    <h6 class="fw-700 mb-3">Attendance This Month</h6>
                    <div class="row g-2 mb-3">
                    <?php foreach($attSummary as $s): ?>
                    <div class="col-4 col-md-3">
                        <div class="border rounded-10 p-2 text-center">
                            <div class="fw-700 fs-5"><?= $s['cnt'] ?></div>
                            <div class="small text-muted"><?= ucwords(str_replace('_',' ',$s['status'])) ?></div>
                            <?php if($s['total_hours']): ?><div style="font-size:.7rem" class="text-muted"><?= round($s['total_hours'],1) ?>h</div><?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if(empty($attSummary)): ?><p class="text-muted">No attendance records this month.</p><?php endif; ?>
                    </div>
                </div>
                <!-- Documents -->
                <div class="tab-pane fade" id="tabDocs">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="fw-700">Documents</h6>
                        <?php if(can('documents','upload')): ?>
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadDocModal"><i class="bi bi-upload me-1"></i>Upload</button>
                        <?php endif; ?>
                    </div>
                    <table class="table table-sm table-hover">
                        <thead><tr><th>Title</th><th>Category</th><th>Date</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach($documents as $doc): ?>
                        <tr>
                            <td class="small"><i class="bi bi-file-earmark me-1"></i><?= e($doc['title']) ?></td>
                            <td><span class="badge bg-light text-dark"><?= ucwords($doc['category']) ?></span></td>
                            <td class="small text-muted"><?= formatDate($doc['created_at'],'M d, Y') ?></td>
                            <td><a href="<?= APP_URL ?>/uploads/documents/<?= $doc['filename'] ?>" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-download"></i></a></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($documents)): ?><tr><td colspan="4" class="text-center text-muted py-3">No documents uploaded</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- Upload Doc Modal -->
<div class="modal fade" id="uploadDocModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Upload Document</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="index.php?module=documents&action=upload" enctype="multipart/form-data">
            <?= csrfField() ?>
            <input type="hidden" name="employee_id" value="<?= $employee['id'] ?>">
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Category</label><select name="category" class="form-select"><option value="contract">Contract</option><option value="id">ID</option><option value="certificate">Certificate</option><option value="policy">Policy</option><option value="other" selected>Other</option></select></div>
                <div class="mb-3"><label class="form-label">File (PDF, Word, Image)</label><input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Upload</button><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button></div>
        </form>
    </div></div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
