<?php
/**
 * View Applicant Profile
 */
$pageTitle = 'Applicant Profile: ' . e($applicant['first_name'] . ' ' . $applicant['last_name']);
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?module=recruitment&action=viewJob&id=<?= $applicant['job_id'] ?>" class="text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Back to Applicants</a>
            <h2 class="fw-bold mb-0 mt-2"><?= e($applicant['first_name'] . ' ' . $applicant['last_name']) ?></h2>
            <div class="mt-2"><?= statusBadge($applicant['status']) ?></div>
        </div>
        <div class="d-flex gap-2">
            <?php if(in_array($applicant['status'], ['offered', 'hired']) && empty($applicant['user_id'])): ?>
            <a href="index.php?module=employees&action=add&from_applicant=<?= $applicant['id'] ?>" class="btn btn-success">
                <i class="bi bi-person-plus-fill me-1"></i>Finalize Hiring
            </a>
            <?php endif; ?>
            <form action="index.php?module=recruitment&action=archiveApplicant" method="POST" onsubmit="return confirm('Archive this applicant?');" class="d-inline">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= $applicant['id'] ?>">
                <input type="hidden" name="job_id" value="<?= $applicant['job_id'] ?>">
                <button type="submit" class="btn btn-outline-danger"><i class="bi bi-archive me-1"></i>Archive</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Personal Info -->
            <div class="card mb-4">
                <div class="card-header bg-white fw-bold"><i class="bi bi-person me-2 text-primary"></i>Personal Information</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small text-muted d-block">Full Name</label>
                            <span class="fw-bold"><?= e($applicant['first_name'] . ' ' . $applicant['last_name']) ?></span>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted d-block">Email Address</label>
                            <span class="fw-bold"><?= e($applicant['email']) ?></span>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted d-block">Phone Number</label>
                            <span class="fw-bold"><?= e($applicant['phone'] ?: 'N/A') ?></span>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted d-block">Birth Date</label>
                            <span class="fw-bold"><?= $applicant['birth_date'] ? formatDate($applicant['birth_date']) : 'N/A' ?></span>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted d-block">Gender</label>
                            <span class="fw-bold"><?= ucfirst($applicant['gender'] ?: 'N/A') ?></span>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted d-block">Civil Status</label>
                            <span class="fw-bold"><?= ucfirst($applicant['civil_status'] ?: 'N/A') ?></span>
                        </div>
                        <div class="col-12">
                            <label class="small text-muted d-block">Address</label>
                            <span class="fw-bold"><?= e($applicant['address'] ?: 'N/A') ?><?= $applicant['city'] ? ', ' . e($applicant['city']) : '' ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ID Numbers -->
            <div class="card mb-4">
                <div class="card-header bg-white fw-bold"><i class="bi bi-card-list me-2 text-primary"></i>Government IDs</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="small text-muted d-block">SSS Number</label>
                            <span class="fw-bold"><?= e($applicant['sss_number'] ?: 'N/A') ?></span>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted d-block">PhilHealth No.</label>
                            <span class="fw-bold"><?= e($applicant['philhealth_number'] ?: 'N/A') ?></span>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted d-block">Pag-IBIG No.</label>
                            <span class="fw-bold"><?= e($applicant['pagibig_number'] ?: 'N/A') ?></span>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted d-block">TIN Number</label>
                            <span class="fw-bold"><?= e($applicant['tin_number'] ?: 'N/A') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="card mb-4">
                <div class="card-header bg-white fw-bold"><i class="bi bi-telephone-outbound me-2 text-primary"></i>Emergency Contact</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small text-muted d-block">Contact Name</label>
                            <span class="fw-bold"><?= e($applicant['emergency_contact_name'] ?: 'N/A') ?></span>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted d-block">Contact Phone</label>
                            <span class="fw-bold"><?= e($applicant['emergency_contact_phone'] ?: 'N/A') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cover Letter -->
            <?php if($applicant['cover_letter']): ?>
            <div class="card mb-4">
                <div class="card-header bg-white fw-bold"><i class="bi bi-file-text me-2 text-primary"></i>Cover Letter</div>
                <div class="card-body">
                    <p class="text-pre-wrap small"><?= e($applicant['cover_letter']) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <!-- Job Info -->
            <div class="card mb-4">
                <div class="card-header bg-white fw-bold">Applied Position</div>
                <div class="card-body">
                    <h5 class="fw-bold mb-1"><?= e($applicant['job_title']) ?></h5>
                    <p class="text-muted small mb-3">Applied on <?= formatDate(substr($applicant['created_at'],0,10)) ?></p>
                    <div class="d-grid mt-3">
                        <a href="index.php?module=recruitment&action=viewJob&id=<?= $applicant['job_id'] ?>" class="btn btn-outline-primary btn-sm">View Job Details</a>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="card mb-4">
                <div class="card-header bg-white fw-bold">Submitted Documents</div>
                <div class="card-body p-0">
                    <?php if($applicant['resume']): ?>
                    <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-file-earmark-pdf fs-3 text-danger me-2"></i>
                            <div>
                                <div class="fw-bold small">Resume / CV</div>
                                <div class="text-muted" style="font-size:0.7rem;">PDF/DOC Format</div>
                            </div>
                        </div>
                        <a href="<?= APP_URL ?>/uploads/resumes/<?= $applicant['resume'] ?>" target="_blank" class="btn btn-ghost-sm"><i class="bi bi-download"></i></a>
                    </div>
                    <?php else: ?>
                    <div class="p-3 text-center text-muted small">No resume uploaded.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header bg-white fw-bold">Update Status</div>
                <div class="card-body">
                    <form action="index.php?module=recruitment&action=updateApplicant" method="POST">
                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= $applicant['id'] ?>">
                        <input type="hidden" name="job_id" value="<?= $applicant['job_id'] ?>">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Current Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <?php foreach(['new','reviewing','interview','offered','hired','rejected'] as $s): ?>
                                <option value="<?=$s?>" <?= $applicant['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
