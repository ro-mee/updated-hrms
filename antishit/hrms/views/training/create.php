<?php
$pageTitle = 'Create Training';
$breadcrumb = [
    ['label' => 'Training', 'url' => 'index.php?module=training'],
    ['label' => 'Create Training', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white pb-0 border-0 mt-2">
                <h5 class="fw-bold"><i class="bi bi-mortarboard-fill text-primary me-2"></i>Create Training Session</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?module=training&action=create">
                    <?= csrfField() ?>
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Training Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trainer / Instructor</label>
                            <input type="text" name="trainer" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Location / Platform</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Conference Room A or Zoom Link">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control <?= isset($errors['start_date']) ? 'is-invalid' : '' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control <?= isset($errors['end_date']) ? 'is-invalid' : '' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Participants</label>
                            <input type="number" name="max_participants" class="form-control" min="1" placeholder="Leave blank for unlimited">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cost per Participant ($)</label>
                            <input type="number" name="cost" class="form-control" step="0.01" value="0.00">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <a href="index.php?module=training" class="btn btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-calendar-check me-2"></i>Schedule Training</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
