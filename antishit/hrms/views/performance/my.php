<?php /** My Performance Reviews */
$pageTitle='My Performance'; $breadcrumb=[['label'=>'Performance','active'=>true],['label'=>'My reviews','active'=>true]];
include APP_ROOT.'/views/layouts/header.php';?>
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-700 mb-0"><i class="bi bi-graph-up text-primary me-2"></i>My Performance Reviews</h5>
        <div class="text-muted small">Viewing all reviews for your profile</div>
    </div>

    <?php if(empty($reviews)):?>
    <div class="card p-5 text-center shadow-sm">
        <div class="py-4">
            <i class="bi bi-graph-up-arrow display-1 text-light mb-3 d-block"></i>
            <h5 class="fw-600">No reviews yet</h5>
            <p class="text-muted">Once your manager completes a performance review, it will appear here.</p>
        </div>
    </div>
    <?php endif;?>

    <div class="row g-4">
    <?php foreach($reviews as $r):?>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border-0 transition-hover">
                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <span class="badge bg-light text-dark fw-500"><?=e($r['review_period'])?></span>
                    <span class="small text-muted"><?=formatDate($r['review_date'])?></span>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <div class="text-muted small mb-1">Overall Rating</div>
                            <div class="d-flex align-items-center">
                                <span class="fs-2 fw-bold me-2"><?=number_format($r['overall_rating'],1)?></span>
                                <div class="text-warning">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <i class="bi bi-star<?= $i <= round($r['overall_rating']) ? '-fill' : '' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <img src="<?=avatarUrl($r['avatar'])?>" class="rounded-circle shadow-sm" width="48" height="48" alt="">
                            <div class="small fw-600 mt-1"><?=e($r['reviewer_name'])?></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-600 small text-uppercase letter-spacing-1 mb-2">Strengths</h6>
                        <p class="small text-muted mb-0"><?=nl2br(e($r['strengths'] ?? 'No comments provided.'))?></p>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-600 small text-uppercase letter-spacing-1 mb-2">Areas for Growth</h6>
                        <p class="small text-muted mb-0"><?=nl2br(e($r['improvements'] ?? 'No comments provided.'))?></p>
                    </div>

                    <?php if($r['goals_next_period']): ?>
                    <div class="p-2 bg-light rounded-2 border border-light">
                        <strong class="small d-block mb-1">Goals for next period:</strong>
                        <div class="small fst-italic text-secondary"><?=e($r['goals_next_period'])?></div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <a href="index.php?module=performance&action=view&id=<?=$r['id']?>" class="btn btn-sm btn-outline-primary">View Full Details</a>
                    <?php if($r['status'] === 'published' && !$r['employee_ack']): ?>
                        <button class="btn btn-sm btn-primary" onclick="acknowledgeReview(<?=$r['id']?>, this)">
                            <i class="bi bi-check-circle me-1"></i>Acknowledge
                        </button>
                    <?php elseif($r['employee_ack']): ?>
                        <span class="text-success small fw-600"><i class="bi bi-check2-all me-1"></i>Acknowledged</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach;?>
    </div>
</div>

<script>
async function acknowledgeReview(id, btn) {
    if (!confirm('By acknowledging, you confirm that you have read and discussed this review with your manager.')) return;
    
    btn.disabled = true;
    const res = await postJson('index.php?module=performance&action=acknowledge', { id: id });
    if (res.success) {
        showToast('success', res.message);
        setTimeout(() => location.reload(), 1000);
    } else {
        showToast('danger', res.message);
        btn.disabled = false;
    }
}
</script>
<?php include APP_ROOT.'/views/layouts/footer.php';?>
