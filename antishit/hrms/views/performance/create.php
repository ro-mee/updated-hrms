<?php
$pageTitle = 'Create Performance Review';
$breadcrumb = [
    ['label' => 'Performance', 'url' => 'index.php?module=performance'],
    ['label' => 'Create Review', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="row justify-content-center mt-4">
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-white pb-0 border-0 mt-2">
                <h5 class="fw-bold"><i class="bi bi-star-half text-primary me-2"></i>Create Performance Review</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?module=performance&action=create">
                    <?= csrfField() ?>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Employee <span class="text-danger">*</span></label>
                            <select name="employee_id" class="form-select <?= isset($errors['employee_id']) ? 'is-invalid' : '' ?>" required>
                                <option value="">Select Employee</option>
                                <?php foreach($employees as $emp): ?>
                                    <option value="<?= $emp['id'] ?>"><?= e($emp['full_name']) ?> (<?= $emp['employee_number'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Review Period <span class="text-danger">*</span></label>
                            <input type="text" name="review_period" class="form-control <?= isset($errors['review_period']) ? 'is-invalid' : '' ?>" placeholder="e.g. Q1 2026, Year End 2025" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Review Date</label>
                            <input type="date" name="review_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">KPI Scores</h6>
                    <div class="row g-3 mb-4">
                        <?php if(empty($kpis)): ?><div class="col-12"><div class="alert alert-info">No KPIs defined in the system.</div></div><?php endif; ?>
                        <?php foreach($kpis as $kpi): ?>
                        <div class="col-md-6">
                            <label class="form-label d-block"><?= e($kpi['name']) ?> <small class="text-muted">(Weight: <?= $kpi['weight'] ?>)</small></label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="range" class="form-range flex-grow-1" name="kpi_scores[<?= $kpi['id'] ?>]" min="1" max="5" step="0.5" value="3" oninput="this.nextElementSibling.value=this.value">
                                <output class="fw-bold text-primary" style="width: 30px;">3</output>
                            </div>
                            <small class="text-muted d-block"><?= e($kpi['description']) ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">Qualitative Assessment</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Key Strengths & Achievements</label>
                            <textarea name="strengths" class="form-control" rows="3" placeholder="What did the employee do exceptionally well?"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Areas for Improvement</label>
                            <textarea name="improvements" class="form-control" rows="3" placeholder="Where can the employee grow or improve?"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Goals for Next Period</label>
                            <textarea name="goals_next_period" class="form-control" rows="3" placeholder="Set 3-5 specific, measurable goals..."></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="index.php?module=performance" class="btn btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Save Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
