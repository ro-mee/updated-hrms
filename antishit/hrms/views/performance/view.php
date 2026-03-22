<?php
$pageTitle = 'Performance Review: ' . e($review['review_period']);
$breadcrumb = [
    ['label' => 'Performance', 'url' => 'index.php?module=performance'],
    ['label' => 'Review Details', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>
    <div class="mb-3">
        <a href="index.php?module=performance" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>

    <!-- Header with Profile & Score Combined -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius:16px; background: var(--hrms-card-bg);">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <img src="<?=avatarUrl($review['avatar'])?>" class="rounded-circle me-3 border shadow-sm" width="60" height="60" style="object-fit:cover;">
                <div>
                    <h5 class="fw-bold mb-0"><?= e($review['employee_name']) ?></h5>
                    <div class="text-muted small">#<?= e($review['employee_number']) ?> &nbsp;&bull;&nbsp; <?= e($review['review_period']) ?></div>
                    <div class="mt-1"><?= statusBadge($review['status']) ?></div>
                </div>
            </div>
            
            <div class="d-flex gap-4 border-start ps-4">
                <div class="text-center">
                    <div class="text-muted" style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:1px;">Average Score</div>
                    <div class="h3 fw-bold text-primary mb-0"><?= $review['overall_rating'] ? number_format($review['overall_rating'], 1) : '—' ?></div>
                </div>
                <div class="text-start">
                    <div class="text-muted small"><i class="bi bi-person-check me-1"></i><?= e($review['reviewer_name']) ?></div>
                    <div class="text-muted small"><i class="bi bi-calendar3 me-1"></i><?= formatDate($review['review_date']) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- KPI Grid (Left/Middle) -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                <div class="card-header bg-transparent border-0 pt-3 pb-0">
                    <h6 class="fw-bold text-muted small mb-0"><i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>KPI SCORES</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <?php foreach($scores as $s): ?>
                        <div class="col-md-6">
                            <div class="p-2 h-100 bg-light rounded-3 border" style="border-color: var(--hrms-card-border) !important;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="min-w-0 me-2">
                                        <div class="fw-bold text-truncate" style="font-size:0.8rem;"><?= e($s['name']) ?></div>
                                        <div class="text-muted" style="font-size:0.65rem;">W: <?= $s['weight'] ?></div>
                                    </div>
                                    <div class="fw-bold text-primary" style="font-size:1.1rem;"><?= number_format($s['score'], 1) ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if(empty($scores)): ?>
                            <div class="col-12 text-center py-4 text-muted small">No scores records found.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Qualitative (Right) -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                <div class="card-header bg-transparent border-0 pt-3 pb-0">
                    <h6 class="fw-bold text-muted small mb-0"><i class="bi bi-clipboard-data-fill text-primary me-2"></i>QUALITATIVE ASSESSMENT</h6>
                </div>
                <div class="card-body p-3 d-flex flex-column gap-2">
                    <div class="p-2 border-start border-4 border-success bg-success-subtle rounded-3" style="border-radius: 4px 8px 8px 4px;">
                        <div class="fw-bold text-success" style="font-size:0.75rem;"><i class="bi bi-star-fill me-1"></i>STRENGTHS</div>
                        <div class="py-1 text-pre-wrap" style="font-size:0.82rem; line-height:1.4;"><?= e($review['strengths'] ?: 'None recorded.') ?></div>
                    </div>
                    <div class="p-2 border-start border-4 border-warning bg-warning-subtle rounded-3" style="border-radius: 4px 8px 8px 4px;">
                        <div class="fw-bold text-warning-emphasis" style="font-size:0.75rem;"><i class="bi bi-arrow-up-circle-fill me-1"></i>IMPROVEMENTS</div>
                        <div class="py-1 text-pre-wrap" style="font-size:0.82rem; line-height:1.4;"><?= e($review['improvements'] ?: 'None recorded.') ?></div>
                    </div>
                    <div class="p-2 border-start border-4 border-info bg-info-subtle rounded-3" style="border-radius: 4px 8px 8px 4px;">
                        <div class="fw-bold text-info" style="font-size:0.75rem;"><i class="bi bi-flag-fill me-1"></i>GOALS</div>
                        <div class="py-1 text-pre-wrap" style="font-size:0.82rem; line-height:1.4;"><?= e($review['goals_next_period'] ?: 'None recorded.') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
