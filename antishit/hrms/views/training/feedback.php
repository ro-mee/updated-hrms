<?php
$pageTitle = 'Training Feedback: ' . e($training['title']);
$breadcrumb = [
    ['label' => 'Training Programs', 'url' => 'index.php?module=training'],
    ['label' => 'Feedback', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';

// Star rendering helper
function renderStars(int $rating): string {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        $html .= '<i class="bi bi-star-fill" style="color:' . ($i <= $rating ? '#f4a819' : '#ddd') . ';font-size:0.9rem;"></i>';
    }
    return $html;
}

// Rating breakdown
$breakdown = [5=>0,4=>0,3=>0,2=>0,1=>0];
foreach ($feedbacks as $f) { $breakdown[(int)$f['rating']]++; }
$total = count($feedbacks);
?>

<style>
.fb-card { border:1px solid var(--hrms-border); border-radius:14px; background:var(--hrms-card-bg); padding:1.2rem 1.5rem; margin-bottom:1rem; }
.avg-score { font-size:3.5rem; font-weight:800; line-height:1; color:var(--hrms-text-main); }
.rating-bar-wrap { height:8px; background:var(--hrms-border); border-radius:4px; overflow:hidden; flex:1; }
.rating-bar-fill { height:100%; border-radius:4px; background:#f4a819; transition:width 0.4s; }
.dept-pill { font-size:0.75rem; background:rgba(var(--bs-info-rgb),0.1); color:var(--bs-info); border:1px solid rgba(var(--bs-info-rgb),0.25); border-radius:50px; padding:0.15rem 0.6rem; }
</style>

<div class="container-fluid px-4 py-3">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-700 mb-0"><i class="bi bi-chat-left-text text-primary me-2"></i><?= e($training['title']) ?></h5>
            <p class="text-muted small mb-0 mt-1">
                <?= statusBadge($training['status']) ?>
                &nbsp;<?= formatDate($training['start_date'],'M d') ?> – <?= formatDate($training['end_date'],'M d, Y') ?>
            </p>
        </div>
        <a href="index.php?module=training" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <?php if(empty($feedbacks)): ?>
    <div class="empty-state"><i class="bi bi-chat-square"></i>No feedback has been submitted for this training yet.</div>
    <?php else: ?>

    <div class="row g-3 mb-4">
        <!-- Average Score Card -->
        <div class="col-md-4">
            <div class="fb-card text-center">
                <p class="text-muted small fw-600 mb-1">Average Rating</p>
                <div class="avg-score"><?= $avgRating ?></div>
                <div class="my-2"><?= renderStars((int)round($avgRating)) ?></div>
                <p class="text-muted small mb-0">Based on <strong><?= $total ?></strong> review<?= $total !== 1 ? 's' : '' ?></p>
            </div>
        </div>

        <!-- Breakdown Card -->
        <div class="col-md-8">
            <div class="fb-card h-100">
                <p class="text-muted small fw-600 mb-3">Rating Breakdown</p>
                <?php foreach([5,4,3,2,1] as $star): ?>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="small fw-600" style="width:50px;color:#f4a819;">
                        <?= $star ?> <i class="bi bi-star-fill" style="font-size:0.75rem;"></i>
                    </span>
                    <div class="rating-bar-wrap">
                        <div class="rating-bar-fill" style="width:<?= $total ? round($breakdown[$star]/$total*100) : 0 ?>%"></div>
                    </div>
                    <span class="small text-muted" style="width:30px;"><?= $breakdown[$star] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Individual Feedbacks -->
    <h6 class="fw-700 text-muted mb-3" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:0.08em;">
        <i class="bi bi-person-lines-fill me-1 text-primary"></i>Individual Reviews
    </h6>
    <?php foreach($feedbacks as $f): ?>
    <div class="fb-card">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <p class="fw-700 mb-0 small"><?= e($f['employee_name']) ?></p>
                <?php if(!empty($f['department_name'])): ?>
                <span class="dept-pill"><?= e($f['department_name']) ?></span>
                <?php endif; ?>
            </div>
            <div class="text-end">
                <div><?= renderStars((int)$f['rating']) ?></div>
                <p class="text-muted" style="font-size:0.75rem;margin-top:2px;"><?= date('M d, Y', strtotime($f['enrolled_at'])) ?></p>
            </div>
        </div>
        <?php if(!empty($f['feedback'])): ?>
        <p class="mb-0 mt-2 small" style="color:var(--hrms-text-main);line-height:1.6;">
            "<?= nl2br(e($f['feedback'])) ?>"
        </p>
        <?php else: ?>
        <p class="mb-0 mt-2 small text-muted fst-italic">No written comments provided.</p>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
