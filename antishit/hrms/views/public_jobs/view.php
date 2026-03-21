<?php
/**
 * Public Job View Page
 */

$pageTitle = e($job['title']);
include APP_ROOT . '/views/layouts/public_header.php';
?>
<div class="header-section py-5 bg-white border-bottom text-center mb-5">
    <div class="container py-2">
        <a href="index.php?module=jobs" class="text-decoration-none small mb-3 d-inline-block"><i class="bi bi-arrow-left me-1"></i>Back to All Vacancies</a>
        <h1 class="fw-bold mb-2"><?= e($job['title']) ?></h1>
        <div class="d-flex justify-content-center gap-3 align-items-center flex-wrap">
            <span class="text-muted"><i class="bi bi-building me-1"></i><?= e($job['department_name']) ?></span>
            <span class="text-muted border-start ps-3"><i class="bi bi-briefcase me-1"></i><?= ucwords(str_replace('_',' ',$job['employment_type'])) ?></span>
            <?php if($job['salary_min']): ?>
            <span class="text-success border-start ps-3 fw-medium"><i class="bi bi-cash me-1"></i><?= formatCurrency($job['salary_min']) ?> - <?= formatCurrency($job['salary_max']) ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container mb-5 pb-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card p-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-4 border-bottom pb-2 text-primary">Job Description</h5>
                    <p class="text-pre-wrap"><?= e($job['description']) ?></p>
                    
                    <h5 class="fw-bold mb-4 border-bottom pb-2 text-primary mt-5">Requirements</h5>
                    <p class="text-pre-wrap"><?= e($job['requirements']) ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card p-4 mb-4 sticky-top" style="top: 2rem;">
                <h5 class="fw-bold mb-3">Apply for this position</h5>
                <p class="text-muted small mb-4">Interested in this role? Submit your contact info and resume to apply.</p>
                <div class="d-grid">
                    <a href="index.php?module=jobs&action=apply&id=<?= $job['id'] ?>" class="btn btn-primary btn-lg">Apply Now</a>
                </div>
                <hr class="my-4">
                <p class="small text-muted mb-1">Posted On</p>
                <p class="mb-3 fw-bold"><?= formatDate(substr($job['created_at'],0,10)) ?></p>
                
                <?php if($job['deadline']): ?>
                <p class="small text-muted mb-1">Application Deadline</p>
                <p class="mb-0 fw-bold <?= strtotime($job['deadline']) < time() ? 'text-danger' : 'text-primary' ?>"><?= formatDate($job['deadline']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/views/layouts/public_footer.php'; ?>
