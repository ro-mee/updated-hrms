<?php
$pageTitle = 'View Job: ' . e($job['title']);
$breadcrumb = [
    ['label' => 'Recruitment', 'url' => 'index.php?module=recruitment'],
    ['label' => 'Job Details', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><?= e($job['title']) ?></h4>
            <div class="text-muted"><i class="bi bi-building me-1"></i><?= e($job['department_name']) ?> &nbsp;&bull;&nbsp; <i class="bi bi-briefcase me-1"></i><?= ucfirst(str_replace('_',' ',$job['employment_type'])) ?></div>
        </div>
        <span class="badge bg-<?= $job['status']==='open'?'success':($job['status']==='closed'?'secondary':'warning') ?> fs-6"><?= ucfirst(str_replace('_',' ',$job['status'])) ?></span>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
    <div class="card-header pt-3 pb-2 border-0"><h6 class="fw-bold"><i class="bi bi-info-circle text-primary me-2"></i>Job Details</h6></div>
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex justify-content-between"><span>Vacancies</span> <strong><?= $job['vacancies'] ?></strong></li>
                        <li class="list-group-item px-0 d-flex justify-content-between"><span>Salary Range</span> <strong><?= $job['salary_min'] ? formatCurrency($job['salary_min']) . ' - ' . formatCurrency($job['salary_max']) : 'Negotiable' ?></strong></li>
                        <li class="list-group-item px-0 d-flex justify-content-between"><span>Deadline</span> <strong class="<?= strtotime($job['deadline']) < time() ? 'text-danger' : '' ?>"><?= $job['deadline'] ? formatDate($job['deadline']) : 'N/A' ?></strong></li>
                        <li class="list-group-item px-0 d-flex justify-content-between"><span>Posted On</span> <strong><?= formatDate(substr($job['created_at'],0,10)) ?></strong></li>
                    </ul>
                </div>
            </div>
            <div class="card shadow-sm">
    <div class="card-header pt-3 pb-2 border-0"><h6 class="fw-bold"><i class="bi bi-file-text text-primary me-2"></i>Description & Req.</h6></div>
                <div class="card-body pt-0 small">
                    <p class="fw-medium mb-1">Description:</p>
                    <p class="text-muted text-pre-wrap"><?= e($job['description'] ?: 'No description provided.') ?></p>
                    <p class="fw-medium mb-1 mt-3">Requirements:</p>
                    <p class="text-muted text-pre-wrap"><?= e($job['requirements'] ?: 'No requirements provided.') ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
    <div class="card-header pt-3 pb-2 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Applicants (<?= $total ?>)</h6>
                    <form method="GET" action="index.php" class="d-flex gap-2">
                        <input type="hidden" name="module" value="recruitment">
                        <input type="hidden" name="action" value="viewJob">
                        <input type="hidden" name="id" value="<?= $job['id'] ?>">
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="new" <?= get('status')==='new'?'selected':'' ?>>New</option>
                            <option value="reviewing" <?= get('status')==='reviewing'?'selected':'' ?>>Reviewing</option>
                            <option value="interview" <?= get('status')==='interview'?'selected':'' ?>>Interview</option>
                            <option value="offered" <?= get('status')==='offered'?'selected':'' ?>>Offered</option>
                            <option value="hired" <?= get('status')==='hired'?'selected':'' ?>>Hired</option>
                            <option value="rejected" <?= get('status')==='rejected'?'selected':'' ?>>Rejected</option>
                        </select>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
    <thead><tr><th>Applicant</th><th>Contact</th><th>Applied</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                        <?php if(empty($applicants)): ?><tr><td colspan="5"><div class="empty-state"><i class="bi bi-emoji-frown"></i>No applicants found</div></td></tr><?php endif; ?>
                        <?php foreach($applicants as $app): ?>
                        <tr>
                            <td>
                                <div class="fw-medium"><?= e($app['first_name'].' '.$app['last_name']) ?></div>
                                <div class="small text-muted"><a href="mailto:<?= e($app['email']) ?>"><?= e($app['email']) ?></a></div>
                            </td>
                            <td class="small"><?= e($app['phone'] ?: 'N/A') ?></td>
                            <td class="small"><?= formatDate($app['created_at']) ?></td>
                            <td><?= statusBadge($app['status']) ?></td>
                            <td>
                                <button class="btn btn-outline-primary btn-sm btn-update-status" data-id="<?= $app['id'] ?>" data-status="<?= $app['status'] ?>" title="Update Status"><i class="bi bi-pencil-square"></i></button>
                                <?php if($app['resume']): ?>
                                <a href="<?= APP_URL.'/uploads/resumes/'.urlencode($app['resume']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm" title="View Resume"><i class="bi bi-file-earmark-person"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if($pg['total_pages']>1): ?>
<div class="card-footer border-top-0"><?= renderPagination($pg) ?></div>
<?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="statusForm" class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Update Applicant Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="statusAppId">
                <div class="mb-3">
                    <label class="form-label">New Status</label>
                    <select name="status" id="statusSelect" class="form-select">
                        <option value="new">New</option>
                        <option value="reviewing">Reviewing</option>
                        <option value="interview">Interview</option>
                        <option value="offered">Offered</option>
                        <option value="hired">Hired</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div id="interviewFields" class="d-none">
                    <div class="mb-3">
                        <label class="form-label">Interview Date & Time</label>
                        <input type="datetime-local" name="interview_date" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea name="interview_notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.btn-update-status').forEach(btn => {
    btn.addEventListener('click', e => {
        document.getElementById('statusAppId').value = btn.dataset.id;
        document.getElementById('statusSelect').value = btn.dataset.status;
        document.getElementById('interviewFields').classList.toggle('d-none', btn.dataset.status !== 'interview');
        new bootstrap.Modal(document.getElementById('statusModal')).show();
    });
});
document.getElementById('statusSelect').addEventListener('change', e => {
    document.getElementById('interviewFields').classList.toggle('d-none', e.target.value !== 'interview');
});
document.getElementById('statusForm').addEventListener('submit', async e => {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    const origText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
    btn.disabled = true;
    
    const res = await postJson('index.php?module=recruitment&action=updateApplicant', Object.fromEntries(new FormData(e.target)));
    if (res.success) {
        showToast('success', res.message);
        setTimeout(() => location.reload(), 1000);
    } else {
        showToast('danger', res.message || 'Error updating status');
        btn.innerHTML = origText;
        btn.disabled = false;
    }
});
</script>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
