<?php
/**
 * Public Job Application Success Page
 */
$pageTitle = 'Application Submitted';
include APP_ROOT . '/views/layouts/public_header.php';
?>
<div class="container py-5 my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="p-5 bg-white shadow-sm rounded-4">
                <div class="display-1 text-success mb-4"><i class="bi bi-check-circle-fill"></i></div>
                <h1 class="fw-bold mb-3">Thank You!</h1>
                <p class="lead text-muted mb-4">Your application for <strong><?= e($job['title']) ?></strong> has been successfully submitted.</p>
                <p class="mb-5">Our recruitment team will review your application and contact you if your profile matches our requirements. Good luck!</p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="index.php?module=jobs" class="btn btn-primary btn-lg px-4 gap-3">Back to Careers</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/views/layouts/public_footer.php'; ?>
