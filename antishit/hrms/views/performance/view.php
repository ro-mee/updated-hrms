<?php
$pageTitle = 'Performance Review: ' . e($review['review_period']);
$breadcrumb = [
    ['label' => 'Performance', 'url' => 'index.php?module=performance'],
    ['label' => 'Review Details', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Performance Review: <?= e($review['employee_name']) ?></h4>
            <div class="text-muted small"><strong>Period:</strong> <?= e($review['review_period']) ?> &nbsp;&bull;&nbsp; <strong>Date:</strong> <?= formatDate($review['review_date']) ?></div>
        </div>
        <div>
            <?= statusBadge($review['status']) ?>
        </div>
    </div>

    <div class="row border-top pt-4">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100 border-0 bg-light">
                <div class="card-body text-center p-4">
                    <h6 class="text-muted text-uppercase fw-bold mb-3">Overall Rating</h6>
                    <div class="display-3 fw-bold text-primary mb-2"><?= $review['overall_rating'] ? number_format($review['overall_rating'], 1) : 'N/A' ?></div>
                    <div class="text-muted">Out of 5.0</div>
                    <hr class="my-4">
                    <div class="text-start small">
                        <p class="mb-1 text-muted">Reviewer:</p>
                        <p class="fw-medium mb-3"><i class="bi bi-person me-2"></i><?= e($review['reviewer_name']) ?></p>
                        <p class="mb-1 text-muted">Employee No:</p>
                        <p class="fw-medium mb-0"><i class="bi bi-hash me-2"></i><?= e($review['employee_number']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h6 class="fw-bold text-muted border-bottom pb-2 mb-3"><i class="bi bi-chat-quote text-primary me-2"></i>Qualitative Assessment</h6>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mt-2">Key Strengths & Achievements</h6>
                    <p class="text-muted text-pre-wrap"><?= e($review['strengths'] ?: 'No details provided.') ?></p>
                    
                    <h6 class="fw-bold mt-4">Areas for Improvement</h6>
                    <p class="text-muted text-pre-wrap"><?= e($review['improvements'] ?: 'No details provided.') ?></p>
                    
                    <h6 class="fw-bold mt-4">Goals for Next Period</h6>
                    <p class="text-muted text-pre-wrap"><?= e($review['goals_next_period'] ?: 'No details provided.') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
