<?php
/** Leave Management List */
$pageTitle  = 'Leave Management';
$breadcrumb = [['label'=>'Leaves','active'=>true]];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-700 mb-0"><i class="bi bi-calendar-check text-primary me-2"></i>Leave Management</h5>
        <?php if(currentUser()['employee_id']): ?>
        <a href="index.php?module=leaves&action=request" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Request Leave</a>
        <?php endif; ?>
    </div>
    <!-- Status filter pill -->
    <div class="d-flex gap-2 mb-3 flex-wrap">
        <?php 
        $currAction = get('action', 'index');
        foreach([''=> 'All','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected','cancelled'=>'Cancelled'] as $s=>$l): 
            $url = "index.php?module=leaves&action=$currAction" . ($s ? "&status=$s" : "");
        ?>
        <a href="<?= $url ?>" class="btn btn-sm <?= get('status')===$s ? 'btn-primary' : 'btn-outline-secondary' ?>"><?= $l ?></a>
        <?php endforeach; ?>
    </div>
    <div class="card table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Employee</th><th>Type</th><th>Period</th><th>Days</th><th>Status</th><th>Filed</th><?php if(can('leaves','approve')): ?><th>Actions</th><?php endif; ?></tr></thead>
                <tbody>
                <?php if(empty($leaves)): ?><tr><td colspan="7"><div class="empty-state"><i class="bi bi-calendar-x"></i>No leave records found.</div></td></tr><?php endif; ?>
                <?php foreach($leaves as $lv): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= avatarUrl($lv['avatar']) ?>" class="avatar-sm" alt="">
                            <div><div class="fw-medium small"><?= e($lv['full_name']) ?></div><div class="text-muted" style="font-size:.72rem"><?= e($lv['department_name']) ?></div></div>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark"><?= e($lv['leave_type_name']) ?></span> <?= $lv['is_paid'] ? '<span class="badge bg-success-subtle text-success">Paid</span>' : '' ?></td>
                    <td class="small"><?= formatDate($lv['start_date'],'M d') ?> – <?= formatDate($lv['end_date'],'M d, Y') ?></td>
                    <td class="fw-medium"><?= $lv['days_requested'] ?></td>
                    <td><?= statusBadge($lv['status']) ?></td>
                    <td class="small text-muted"><?= timeAgo($lv['created_at']) ?></td>
                    <td>
                        <a href="index.php?module=leaves&action=view&id=<?= $lv['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="View Details"><i class="bi bi-eye"></i></a>
                        <?php if(can('leaves','approve') && $lv['status']==='pending'): ?>
                        <button class="btn btn-sm btn-success me-1" onclick="reviewLeave(<?= $lv['id'] ?>,'approve')" title="Approve"><i class="bi bi-check-lg"></i></button>
                        <button class="btn btn-sm btn-danger me-1" onclick="reviewLeave(<?= $lv['id'] ?>,'reject')" title="Reject"><i class="bi bi-x-lg"></i></button>
                        <?php endif; ?>
                        
                        <?php if($lv['status']==='pending' && $lv['employee_id'] === currentUser()['employee_id']): ?>
                        <form method="POST" action="index.php?module=leaves&action=cancel" class="d-inline">
                            <?= csrfField() ?><input type="hidden" name="id" value="<?= $lv['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger" data-confirm="Cancel request?"><i class="bi bi-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if($total > RECORDS_PER_PAGE): ?>
        <div class="card-footer d-flex justify-content-between align-items-center bg-transparent">
            <small class="text-muted">Showing <?= count($leaves) ?> of <?= $total ?></small>
            <?= paginationLinks($pg,'index.php?module=leaves&status='.get('status')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<!-- Review Modal -->
<div class="modal fade" id="leaveReviewModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="reviewModalTitle">Review Leave</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" id="reviewLeaveId">
            <input type="hidden" id="reviewAction">
            <div class="mb-3"><label class="form-label">Remarks (optional)</label><textarea id="reviewRemarks" class="form-control" rows="3" placeholder="Add notes for the employee…"></textarea></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" id="reviewSubmitBtn">Submit</button>
            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
    </div></div>
</div>
<script>
function reviewLeave(id, action) {
    document.getElementById('reviewLeaveId').value = id;
    document.getElementById('reviewAction').value  = action;
    document.getElementById('reviewModalTitle').textContent = (action==='approve' ? 'Approve' : 'Reject') + ' Leave';
    document.getElementById('reviewSubmitBtn').className = 'btn btn-' + (action==='approve'?'success':'danger');
    new bootstrap.Modal(document.getElementById('leaveReviewModal')).show();
}
document.getElementById('reviewSubmitBtn')?.addEventListener('click', async () => {
    const id      = document.getElementById('reviewLeaveId').value;
    const action  = document.getElementById('reviewAction').value;
    const remarks = document.getElementById('reviewRemarks').value;
    const url     = `index.php?module=leaves&action=${action}`;
    const res     = await postJson(url, { id, remarks });
    showToast(res.success ? 'success' : 'danger', res.message);
    if (res.success) setTimeout(() => location.reload(), 1000);
});
</script>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
