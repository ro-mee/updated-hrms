<?php
/**
 * Public Job List View
 */
$pageTitle = 'Job Vacancies';
include APP_ROOT . '/views/layouts/public_header.php';
?>
<div class="hero-section text-center mb-5">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Build Your Career With Us</h1>
        <p class="lead">Explore our current job openings and find the perfect role for you.</p>
    </div>
</div>

<div class="container pb-5">
    <?php if(empty($jobs)): ?>
    <div class="text-center py-5">
        <div class="display-1 text-muted mb-4"><i class="bi bi-search"></i></div>
        <h3>No Open Positions</h3>
        <p class="text-muted">We don't have any open vacancies at the moment. Please check back later!</p>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <?php foreach($jobs as $job): ?>
        <div class="col-md-6">
            <div class="card h-100 p-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill"><?= ucwords(str_replace('_',' ',$job['employment_type'])) ?></span>
                        <?php if($job['deadline']): ?>
                        <small class="text-muted"><i class="bi bi-calendar-event me-1"></i>Apply by <?= formatDate($job['deadline']) ?></small>
                        <?php endif; ?>
                    </div>
                    <h4 class="fw-bold mb-1"><?= e($job['title']) ?></h4>
                    <p class="text-muted mb-3"><i class="bi bi-building me-1"></i><?= e($job['department_name']) ?></p>
                    
                    <?php if($job['salary_min'] || $job['salary_max']): ?>
                    <p class="text-success fw-medium"><i class="bi bi-cash me-1"></i><?= formatCurrency($job['salary_min']) ?> - <?= formatCurrency($job['salary_max']) ?></p>
                    <?php endif; ?>
                    
                    <p class="text-secondary small line-clamp-2"><?= e(mb_strimwidth($job['description'], 0, 150, "...")) ?></p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 mt-3">
                    <div class="row g-2">
                        <div class="col-6"><a href="index.php?module=jobs&action=view&id=<?= $job['id'] ?>" class="btn btn-outline-primary w-100">Details</a></div>
                        <div class="col-6"><a href="index.php?module=jobs&action=apply&id=<?= $job['id'] ?>" class="btn btn-primary w-100">Apply Now</a></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;  
    overflow: hidden;
}
</style>

<?php include APP_ROOT . '/views/layouts/public_footer.php'; ?>
