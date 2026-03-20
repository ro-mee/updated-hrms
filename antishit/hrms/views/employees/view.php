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
                    <li class="nav-item"><button class="nav-link text-danger" data-bs-toggle="tab" data-bs-target="#tabSessions"><i class="bi bi-shield-lock me-1"></i>Sessions</button></li>
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
                <!-- Sessions -->
                <div class="tab-pane fade" id="tabSessions">
                    <div class="alert alert-info py-2 small">
                        <i class="bi bi-info-circle me-2"></i>These are the devices that are currently logged into your account. You can revoke any session to log it out immediately.
                    </div>
                    <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light"><tr><th>Device / Browser</th><th>IP & Location</th><th>Last Activity</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach($activeSessions as $s): 
                            $isCurrent = ($s['session_id'] === session_id());
                        ?>
                        <tr class="<?= $isCurrent ? 'table-primary-subtle' : '' ?>">
                            <td>
                                <div class="fw-700 small"><?= e($s['device'] ?: 'Unknown Device') ?></div>
                                <div class="text-muted" style="font-size:.7rem"><?= truncate($s['user_agent'], 60) ?></div>
                                <?php if($isCurrent): ?><span class="badge bg-primary px-2" style="font-size:.65rem">Current Session</span><?php endif; ?>
                            </td>
                            <td>
                                <div class="small"><?= e($s['ip_address']) ?></div>
                                <div class="text-muted" style="font-size:.7rem"><?= e($s['location'] ?: 'Unknown Location') ?></div>
                            </td>
                            <td class="small text-muted"><?= timeAgo($s['last_activity']) ?></td>
                            <td class="text-end">
                                <?php if(!$isCurrent): ?>
                                <button type="button" class="btn btn-outline-danger btn-xs revoke-session-btn" 
                                        data-id="<?= $s['id'] ?>" data-emp-id="<?= $employee['id'] ?>">
                                    Revoke
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Revoke Session Confirmation Modal -->
<div class="modal fade" id="revokeSessionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-shield-lock-fill text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold mb-2">Logout this Device?</h5>
                <p class="text-muted small px-3">Are you sure you want to log out this device? The user will be immediately disconnected.</p>
                
                <div class="d-grid gap-2 mt-4">
                    <button type="button" class="btn btn-danger py-2 fw-bold" id="confirmRevokeBtn">Yes, Logout Device</button>
                    <button type="button" class="btn btn-light py-2 text-muted" data-bs-dismiss="modal">Cancel</button>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revoke Session Handler (MODAL)
    let sessionToRevoke = null;
    let empIdToRevoke = null;
    let rowToRevoke = null;
    const revokeModal = new bootstrap.Modal(document.getElementById('revokeSessionModal'));
    const confirmBtn = document.getElementById('confirmRevokeBtn');

    document.querySelectorAll('.revoke-session-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            sessionToRevoke = this.dataset.id;
            empIdToRevoke = this.dataset.empId;
            rowToRevoke = this.closest('tr');
            revokeModal.show();
        });
    });

    confirmBtn.addEventListener('click', function() {
        if (!sessionToRevoke) return;
        
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Logging out...';
        
        const formData = new FormData();
        formData.append('session_db_id', sessionToRevoke);
        formData.append('employee_id', empIdToRevoke);
        formData.append('csrf_token', '<?= csrfToken() ?>');

        fetch('index.php?module=employees&action=revokeSession', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                revokeModal.hide();
                rowToRevoke.style.transition = 'all 0.5s ease';
                rowToRevoke.style.background = '#ffebeb';
                rowToRevoke.style.opacity = '0';
                rowToRevoke.style.transform = 'translateX(20px)';
                setTimeout(() => rowToRevoke.remove(), 500);
            } else {
                alert(data.message || 'Failed to revoke session.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = 'Yes, Logout Device';
            sessionToRevoke = null;
        });
    });
});
</script>
