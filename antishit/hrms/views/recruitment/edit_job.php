<?php
/**
 * Recruitment - Edit Job View
 */
$pageTitle = 'Edit Job: ' . e($job['title']);
$breadcrumb = [
    ['label' => 'Recruitment', 'url' => 'index.php?module=recruitment'],
    ['label' => 'Edit Job', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="row justify-content-center mt-4">
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-white pb-0 border-0 mt-2">
                <h5 class="fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Job Vacancy</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?module=recruitment&action=editJob&id=<?= $job['id'] ?>">
                    <?= csrfField() ?>
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Select Position <span class="text-danger">*</span></label>
                            <select name="position_id" id="position_id" class="form-select <?= isset($errors['position_id']) ? 'is-invalid' : '' ?>" required>
                                <option value="">-- Select Position --</option>
                                <?php foreach($positions as $p): ?>
                                    <option value="<?= $p['id'] ?>" 
                                            <?= $job['position_id'] == $p['id'] ? 'selected' : '' ?>
                                            data-title="<?= e($p['title']) ?>" 
                                            data-dept="<?= $p['department_id'] ?>"
                                            data-min="<?= $p['salary_min'] ?>"
                                            data-max="<?= $p['salary_max'] ?>"
                                            data-description="<?= e($p['description'] ?? '') ?>"
                                            data-requirements="<?= e($p['requirements'] ?? '') ?>">
                                        <?= e($p['title']) ?> (<?= e($p['department_name'] ?? 'No Dept') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Job Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="job_title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" value="<?= e($job['title']) ?>" required>
                            <?php if(isset($errors['title'])): ?><div class="invalid-feedback"><?= $errors['title'] ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select name="department_id" id="department_id" class="form-select <?= isset($errors['department_id']) ? 'is-invalid' : '' ?>" required>
                                <option value="">-- Select Department --</option>
                                <?php foreach($departments as $d): ?>
                                    <option value="<?= $d['id'] ?>" <?= $job['department_id'] == $d['id'] ? 'selected' : '' ?>><?= e($d['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="job_description" class="form-control" rows="5"><?= e($job['description']) ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Requirements</label>
                            <textarea name="requirements" id="job_requirements" class="form-control" rows="5"><?= e($job['requirements']) ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Employment Type</label>
                            <select name="employment_type" class="form-select">
                                <?php foreach(['full_time'=>'Full Time','part_time'=>'Part Time','contract'=>'Contract','intern'=>'Internship'] as $k=>$v): ?>
                                    <option value="<?= $k ?>" <?= $job['employment_type'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Min Salary</label>
                            <input type="number" name="salary_min" class="form-control" step="0.01" value="<?= $job['salary_min'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Max Salary</label>
                            <input type="number" name="salary_max" class="form-control" step="0.01" value="<?= $job['salary_max'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Vacancies</label>
                            <input type="number" name="vacancies" class="form-control" value="<?= $job['vacancies'] ?>" min="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Deadline</label>
                            <input type="date" name="deadline" class="form-control" value="<?= $job['deadline'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="open" <?= $job['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                                <option value="on_hold" <?= $job['status'] === 'on_hold' ? 'selected' : '' ?>>On Hold</option>
                                <option value="closed" <?= $job['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <a href="index.php?module=recruitment&action=viewJob&id=<?= $job['id'] ?>" class="btn btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Update Job Posting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('position_id').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (opt.value) {
        document.getElementById('job_title').value = opt.dataset.title;
        document.getElementById('department_id').value = opt.dataset.dept;
        document.getElementsByName('salary_min')[0].value = opt.dataset.min;
        document.getElementsByName('salary_max')[0].value = opt.dataset.max;
        document.getElementById('job_description').value = opt.dataset.description;
        document.getElementById('job_requirements').value = opt.dataset.requirements;
    }
});
</script>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
