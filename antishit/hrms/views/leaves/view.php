<?php
$pageTitle = 'View Leave Request';
$breadcrumb = [
    ['label' => 'Leaves', 'url' => 'index.php?module=leaves'],
    ['label' => 'Leave Details', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white pt-4 pb-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-file-earmark-text text-primary me-2"></i>Leave Request #<?= $leave['id'] ?>
                </h5>
                <div><?= statusBadge($leave['status']) ?></div>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded mb-4">
                    <img src="<?= avatarUrl($leave['avatar'] ?? null) ?>" class="rounded-circle border border-2 border-white shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                    <div>
                        <div class="fw-bold fs-6"><?= e($leave['full_name']) ?></div>
                        <div class="text-muted small"><?= e($leave['department_name'] ?? 'General') ?> &bull; <?= e($leave['employee_number'] ?? 'N/A') ?></div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Leave Type</div>
                        <div class="fw-bold"><span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle me-2"><?= e($leave['leave_type_code']) ?></span><?= e($leave['leave_type_name']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Duration</div>
                        <div class="fw-bold"><?= $leave['days_requested'] ?> Day(s)</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Start Date</div>
                        <div class="fw-bold"><?= formatDate($leave['start_date'], 'D, M d, Y') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">End Date</div>
                        <div class="fw-bold"><?= formatDate($leave['end_date'], 'D, M d, Y') ?></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="text-muted small mb-2">Reason for Leave</div>
                    <div class="p-3 bg-light rounded text-pre-wrap"><?= e($leave['reason'] ?: 'No specific reason provided.') ?></div>
                </div>

                <?php if ($leave['attachment']): ?>
                <div class="mb-4">
                    <div class="text-muted small mb-2">Attachment / Supporting Document</div>
                    <?php 
                    $ext = strtolower(pathinfo($leave['attachment'], PATHINFO_EXTENSION));
                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    $fileUrl = APP_URL . '/uploads/documents/' . urlencode($leave['attachment']);
                    if ($isImage): ?>
                        <div class="mb-2">
                            <img src="<?= $fileUrl ?>" class="img-fluid rounded border shadow-sm" style="max-height: 400px; cursor: pointer;" onclick="window.open(this.src, '_blank')" title="Click to view full screen" alt="Supporting Document">
                        </div>
                        <a href="<?= $fileUrl ?>" target="_blank" class="btn btn-outline-primary btn-sm mt-1">
                            <i class="bi bi-arrows-fullscreen me-1"></i> View Full Screen
                        </a>
                    <?php else: ?>
                        <a href="<?= $fileUrl ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-file-earmark-pdf me-1"></i> View Document (<?= strtoupper($ext) ?>)
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($leave['status'] !== 'pending'): ?>
                <hr class="my-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Reviewed By</div>
                        <div class="fw-medium"><?= e($leave['reviewed_by_name'] ?? 'System') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Reviewed At</div>
                        <div class="fw-medium"><?= $leave['reviewed_at'] ? formatDateTime($leave['reviewed_at']) : 'N/A' ?></div>
                    </div>
                    <?php if ($leave['remarks']): ?>
                    <div class="col-12 mt-3">
                        <div class="text-muted small mb-1">Reviewer Remarks</div>
                        <div class="text-dark"><em>"<?= e($leave['remarks']) ?>"</em></div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                <a href="index.php?module=leaves" class="btn btn-light">Back to List</a>
                
                <div>
                    <?php if (can('leaves', 'approve') && $leave['status'] === 'pending'): ?>
                        <button class="btn btn-success me-2" onclick="reviewLeave(<?= $leave['id'] ?>, 'approve')"><i class="bi bi-check-lg me-1"></i>Approve</button>
                        <button class="btn btn-danger" onclick="reviewLeave(<?= $leave['id'] ?>, 'reject')"><i class="bi bi-x-lg me-1"></i>Reject</button>
                    <?php endif; ?>
                    
                    <?php if ($leave['status'] === 'pending' && currentRole() === ROLE_EMPLOYEE): ?>
                        <form method="POST" action="index.php?module=leaves&action=cancel" class="d-inline">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="<?= $leave['id'] ?>">
                            <button class="btn btn-outline-danger" data-confirm="Are you sure you want to cancel this request?">Cancel Request</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (can('leaves', 'approve') && $leave['status'] === 'pending'): ?>
<!-- Review Modal -->
<div class="modal fade" id="leaveReviewModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="reviewModalTitle">Review Leave</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" id="reviewLeaveId">
            <input type="hidden" id="reviewAction">
            <div class="mb-3"><label class="form-label">Remarks (optional)</label><textarea id="reviewRemarks" class="form-control" rows="3" placeholder="Add notes for the employee…"></textarea></div>
        </div>
        <div class="modal-footer border-0">
            <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-primary" id="reviewSubmitBtn">Submit</button>
        </div>
    </div></div>
</div>
<script>
function reviewLeave(id, action) {
    document.getElementById('reviewLeaveId').value = id;
    document.getElementById('reviewAction').value  = action;
    document.getElementById('reviewModalTitle').textContent = (action === 'approve' ? 'Approve' : 'Reject') + ' Leave';
    document.getElementById('reviewSubmitBtn').className = 'btn btn-' + (action === 'approve' ? 'success' : 'danger');
    new bootstrap.Modal(document.getElementById('leaveReviewModal')).show();
}
document.getElementById('reviewSubmitBtn')?.addEventListener('click', async () => {
    const id      = document.getElementById('reviewLeaveId').value;
    const action  = document.getElementById('reviewAction').value;
    const remarks = document.getElementById('reviewRemarks').value;
    const res     = await postJson(`index.php?module=leaves&action=${action}`, { id, remarks });
    showToast(res.success ? 'success' : 'danger', res.message);
    if (res.success) setTimeout(() => window.location.href = 'index.php?module=leaves', 1000);
});
</script>
<?php endif; ?>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
