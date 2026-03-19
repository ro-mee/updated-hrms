<?php
$pageTitle = 'Post Job Vacancy';
$breadcrumb = [
    ['label' => 'Recruitment', 'url' => 'index.php?module=recruitment'],
    ['label' => 'Post Job', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white pb-0 border-0 mt-2">
                <h5 class="fw-bold"><i class="bi bi-briefcase-fill text-primary me-2"></i>Post New Job Vacancy</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?module=recruitment&action=addJob">
                    <?= csrfField() ?>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Job Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" required>
                            <?php if(isset($errors['title'])): ?><div class="invalid-feedback"><?= $errors['title'] ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select name="department_id" class="form-select <?= isset($errors['department_id']) ? 'is-invalid' : '' ?>" required>
                                <option value="">-- Select Department --</option>
                                <?php foreach($departments as $d): ?>
                                    <option value="<?= $d['id'] ?>"><?= e($d['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Requirements</label>
                            <textarea name="requirements" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Employment Type</label>
                            <select name="employment_type" class="form-select">
                                <option value="full_time">Full Time</option>
                                <option value="part_time">Part Time</option>
                                <option value="contract">Contract</option>
                                <option value="intern">Internship</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Min Salary</label>
                            <input type="number" name="salary_min" class="form-control" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Max Salary</label>
                            <input type="number" name="salary_max" class="form-control" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Vacancies</label>
                            <input type="number" name="vacancies" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Deadline</label>
                            <input type="date" name="deadline" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="open">Open</option>
                                <option value="on_hold">On Hold</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <a href="index.php?module=recruitment" class="btn btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-cloud-arrow-up me-2"></i>Post Job</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
