<?php /** KPI Management */
$pageTitle = 'Manage KPIs';
$breadcrumb = [['label' => 'Performance', 'url' => 'index.php?module=performance'], ['label' => 'KPIs', 'active' => true]];
include APP_ROOT . '/views/layouts/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-700 mb-0"><i class="bi bi-list-check text-primary me-2"></i>Performance KPIs</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKpiModal">
            <i class="bi bi-plus-lg me-1"></i>Add KPI
        </button>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>KPI Name</th>
                                <th>Description</th>
                                <th>Weight</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($kpis)): ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted small italic">No KPIs defined yet.</td></tr>
                            <?php endif; ?>
                            <?php foreach($kpis as $k): ?>
                            <tr>
                                <td class="fw-600 small"><?= e($k['name']) ?></td>
                                <td class="small text-muted"><?= e($k['description']) ?></td>
                                <td class="small"><?= $k['weight'] ?></td>
                                <td class="small">
                                    <?php if($k['department_id']): ?>
                                        <?php 
                                        $dName = 'Unknown';
                                        foreach($departments as $dept) { if($dept['id'] == $k['department_id']) { $dName = $dept['name']; break; } }
                                        echo e($dName);
                                        ?>
                                    <?php else: ?>
                                        <span class="text-muted">Global</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $k['is_active'] ? 'success' : 'secondary' ?>-subtle text-<?= $k['is_active'] ? 'success' : 'secondary' ?>-emphasis border">
                                        <?= $k['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="index.php?module=performance&action=deleteKpi" onsubmit="return confirm('Are you sure you want to delete this KPI?')">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id" value="<?= $k['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm border-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add KPI Modal -->
<div class="modal fade" id="addKpiModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-header border-0 pb-0">
                <h6 class="fw-700 mb-0">Add New KPI</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?module=performance&action=kpis">
                <div class="modal-body">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label class="form-label small fw-600">KPI Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Code Quality, Punctuality" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-600">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Briefly describe what this KPI measures..."></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-600">Weight</label>
                            <input type="number" name="weight" class="form-control" step="0.1" value="1.0" min="0.1">
                            <div class="form-text mt-1" style="font-size:0.65rem">Relative importance in scoring.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-600">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">Global (All)</option>
                                <?php foreach($departments as $dept): ?>
                                    <option value="<?= $dept['id'] ?>"><?= e($dept['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save KPI</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
