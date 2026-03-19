<?php
$pageTitle = 'Documents';
$breadcrumb = [['label' => 'Documents', 'active' => true]];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-700 mb-0"><i class="bi bi-folder-fill text-primary me-2"></i>Document Center</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="bi bi-cloud-upload me-1"></i>Upload Document
        </button>
    </div>

    <div class="card table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <?php if (currentRole() !== ROLE_EMPLOYEE): ?>
                        <th>Employee</th>
                        <?php endif; ?>
                        <th>Uploaded By</th>
                        <th>Visibility</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($documents)): ?>
                    <tr><td colspan="7"><div class="empty-state"><i class="bi bi-folder-x"></i>No documents found</div></td></tr>
                <?php endif; ?>
                <?php foreach($documents as $doc): ?>
                    <tr>
                        <td class="fw-medium">
                            <i class="bi bi-file-earmark-text text-muted me-2"></i>
                            <a href="<?= e(APP_URL . '/uploads/documents/' . $doc['filename']) ?>" target="_blank" class="text-decoration-none">
                                <?= e($doc['title']) ?>
                            </a>
                        </td>
                        <td><span class="badge border text-muted"><?= ucfirst($doc['category']) ?></span></td>
                        <?php if (currentRole() !== ROLE_EMPLOYEE): ?>
                        <td class="small"><?= e($doc['employee_name'] ?? 'Company Form') ?></td>
                        <?php endif; ?>
                        <td class="small text-muted"><?= e($doc['uploaded_by_name']) ?></td>
                        <td>
                            <?php if($doc['is_public']): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-globe me-1"></i>Public</span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"><i class="bi bi-lock me-1"></i>Private</span>
                            <?php endif; ?>
                        </td>
                        <td class="small"><?= formatDate($doc['created_at']) ?></td>
                        <td>
                            <a href="<?= APP_URL . '/uploads/documents/' . urlencode($doc['filename']) ?>" download class="btn btn-outline-primary btn-sm me-1" title="Download">
                                <i class="bi bi-download"></i>
                            </a>
                            <?php if (in_array(currentRole(), [ROLE_SUPER_ADMIN, ROLE_HR_DIRECTOR, ROLE_HR_SPECIALIST])): ?>
                                <form method="POST" action="index.php?module=documents&action=delete" class="d-inline" onsubmit="return confirm('Delete this document?');">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= $doc['id'] ?>">
                                    <button class="btn btn-outline-danger btn-sm" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="index.php?module=documents&action=upload" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Upload Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?= csrfField() ?>
                <div class="mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required placeholder="Document Title">
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="contract">Employment Contract</option>
                        <option value="id">Identification Card / Gov ID</option>
                        <option value="certificate">Certificate / Diploma</option>
                        <option value="policy">Company Policy</option>
                        <option value="other" selected>Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">File <span class="text-danger">*</span></label>
                    <input type="file" name="file" class="form-control" required>
                    <div class="form-text small">Accepted: PDF, DOC, DOCX, JPG, PNG</div>
                </div>
                <?php if (currentRole() !== ROLE_EMPLOYEE): ?>
                <div class="mb-3">
                    <label class="form-label">Assign to Employee</label>
                    <select name="employee_id" class="form-select">
                        <option value="">-- Company Wide Document (No Specific Employee) --</option>
                        <?php $allEmps = (new Employee())->all(); foreach($allEmps as $emp): ?>
                            <option value="<?= $emp['id'] ?>"><?= e($emp['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-check form-switch custom-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_public" value="1" id="isPublicCheck">
                    <label class="form-check-label fw-medium ms-2" for="isPublicCheck">Make Public (Visible to all employees)</label>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
