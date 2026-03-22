<?php
$pageTitle = 'Create Performance Review';
$breadcrumb = [
    ['label' => 'Performance', 'url' => 'index.php?module=performance'],
    ['label' => 'Create Review', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';
?>

<style>
.review-form-card {
    border-radius: 16px;
    border: 1px solid var(--hrms-border);
    background: var(--hrms-card-bg);
    overflow: hidden;
}
.review-form-header {
    background: linear-gradient(135deg, rgba(var(--bs-primary-rgb),0.1) 0%, rgba(var(--bs-primary-rgb),0.02) 100%);
    border-bottom: 1px solid var(--hrms-border);
    padding: 1.4rem 1.75rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.review-form-header .icon-wrap {
    width: 48px; height: 48px;
    background: rgba(var(--bs-primary-rgb),0.15);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; color: var(--bs-primary);
}
.section-heading {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--hrms-text-muted);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--hrms-border);
    display: flex; align-items: center; gap: 0.5rem;
}
.section-heading i { color: var(--bs-primary); font-size: 1rem; }
.kpi-item {
    background: var(--hrms-card-bg);
    border: 1px solid var(--hrms-card-border);
    border-radius: 12px;
    padding: 1.25rem;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.kpi-item:hover {
    border-color: var(--bs-primary);
    box-shadow: 0 8px 24px rgba(var(--bs-primary-rgb), 0.12);
    transform: translateY(-2px);
}
.kpi-item .kpi-label {
    font-weight: 700;
    font-size: 0.95rem;
    margin-bottom: 0.25rem;
}
.kpi-item .kpi-desc {
    font-size: 0.82rem;
    color: var(--hrms-text-muted);
    margin-bottom: 1rem;
    line-height: 1.4;
}
.kpi-item .kpi-weight {
    font-size: 0.73rem;
    background: rgba(var(--bs-primary-rgb), 0.1);
    color: var(--bs-primary);
    border-radius: 50px;
    padding: 0.1rem 0.5rem;
    font-weight: 600;
}
.score-display {
    width: 40px; height: 40px;
    background: var(--bs-primary);
    color: #fff;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.form-range::-webkit-slider-thumb { background: var(--bs-primary); }
.qual-area {
    background: var(--hrms-card-bg);
    border: 1px solid var(--hrms-card-border);
    border-left: 5px solid var(--bs-primary);
    border-radius: 12px;
    padding: 1.25rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    margin-bottom: 1rem;
}
.qual-area textarea {
    background: transparent !important;
    border: 1px solid var(--hrms-card-border) !important;
    border-radius: 8px;
    padding: 12px !important;
    margin-top: 10px;
    font-size: 0.9rem;
}
.qual-area textarea::placeholder {
    color: #999;
    font-style: italic;
}
.qual-area .form-label {
    font-weight: 600;
    font-size: 0.88rem;
}
</style>

<div class="container-fluid px-4 py-3">
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="review-form-card shadow-sm">

            <!-- Header -->
            <div class="review-form-header">
                <div class="icon-wrap"><i class="bi bi-star-half"></i></div>
                <div>
                    <h5 class="fw-700 mb-0">Create Performance Review</h5>
                    <p class="text-muted small mb-0">Evaluate employee KPIs and qualitative performance</p>
                </div>
            </div>

            <div class="p-4">
                <form method="POST" action="index.php?module=performance&action=create">
                    <?= csrfField() ?>

                    <!-- Basic Info -->
                    <div class="section-heading"><i class="bi bi-info-circle-fill"></i> Review Details</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-600">Employee <span class="text-danger">*</span></label>
                            <select name="employee_id" class="form-select <?= isset($errors['employee_id']) ? 'is-invalid' : '' ?>" required>
                                <option value="">Select Employee</option>
                                <?php foreach($employees as $emp): ?>
                                    <option value="<?= $emp['id'] ?>"><?= e($emp['full_name']) ?> (<?= $emp['employee_number'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600">Review Period <span class="text-danger">*</span></label>
                            <input type="text" name="review_period" class="form-control <?= isset($errors['review_period']) ? 'is-invalid' : '' ?>" placeholder="e.g. Q1 2026, Year End 2025" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600">Review Date</label>
                            <input type="date" name="review_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <!-- KPI Scores -->
                    <div class="section-heading"><i class="bi bi-bar-chart-fill"></i> KPI Scores</div>
                    <div class="row g-3 mb-4">
                        <?php if(empty($kpis)): ?>
                        <div class="col-12"><div class="alert alert-info rounded-3">No KPIs defined in the system.</div></div>
                        <?php endif; ?>
                        <?php foreach($kpis as $kpi): ?>
                        <div class="col-md-6">
                            <div class="kpi-item">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                    <span class="kpi-label"><?= e($kpi['name']) ?></span>
                                    <span class="kpi-weight">Weight <?= $kpi['weight'] ?></span>
                                </div>
                                <p class="kpi-desc mb-2"><?= e($kpi['description']) ?></p>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="range" class="form-range flex-grow-1"
                                        name="kpi_scores[<?= $kpi['id'] ?>]"
                                        min="1" max="5" step="0.5" value="3"
                                        oninput="this.closest('.d-flex').querySelector('.score-display').textContent=this.value">
                                    <div class="score-display">3</div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted" style="font-size:0.72rem">Poor (1)</small>
                                    <small class="text-muted" style="font-size:0.72rem">Excellent (5)</small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Qualitative Assessment -->
                    <div class="section-heading"><i class="bi bi-chat-square-text-fill"></i> Qualitative Assessment</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <div class="qual-area">
                                <label class="form-label"><i class="bi bi-trophy text-warning me-1"></i>Key Strengths & Achievements</label>
                                <textarea name="strengths" class="form-control border-0 bg-transparent ps-0" rows="3" placeholder="What did the employee do exceptionally well?"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="qual-area">
                                <label class="form-label"><i class="bi bi-arrow-up-circle text-info me-1"></i>Areas for Improvement</label>
                                <textarea name="improvements" class="form-control border-0 bg-transparent ps-0" rows="3" placeholder="Where can the employee grow or improve?"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="qual-area">
                                <label class="form-label"><i class="bi bi-flag text-primary me-1"></i>Goals for Next Period</label>
                                <textarea name="goals_next_period" class="form-control border-0 bg-transparent ps-0" rows="3" placeholder="Set 3-5 specific, measurable goals..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <a href="index.php?module=performance" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-x-lg me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-save me-2"></i>Save Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
