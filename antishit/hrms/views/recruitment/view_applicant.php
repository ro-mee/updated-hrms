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
            <?php if(in_array($applicant['status'], ['offered', 'hired']) && empty($applicant['user_id']) && hasRole(ROLE_SUPER_ADMIN, ROLE_HR_DIRECTOR)): ?>
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
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-ghost-sm text-primary" onclick="viewResume('<?= APP_URL ?>/uploads/resumes/<?= $applicant['resume'] ?>')">
                                <i class="bi bi-eye"></i>
                            </button>
                            <a href="<?= APP_URL ?>/uploads/resumes/<?= $applicant['resume'] ?>" target="_blank" class="btn btn-ghost-sm"><i class="bi bi-download"></i></a>
                        </div>
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
                            <select name="status" id="statusSelect" class="form-select form-select-sm" onchange="toggleInterviewFields()">
                                <?php foreach(['new','reviewing','interview','offered','hired','rejected'] as $s): ?>
                                    <?php if($s === 'hired' && !hasRole(ROLE_SUPER_ADMIN, ROLE_HR_DIRECTOR)) continue; ?>
                                    <option value="<?=$s?>" <?= $applicant['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Interview Details (Conditional) -->
                        <div id="interviewFields" style="<?= $applicant['status'] === 'interview' ? '' : 'display: none;' ?>">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Interview Date</label>
                                <input type="date" name="interview_date" class="form-control form-control-sm" value="<?= $applicant['interview_date'] ? substr($applicant['interview_date'], 0, 10) : '' ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Interview Time</label>
                                <input type="time" name="interview_time" class="form-control form-control-sm" value="<?= $applicant['interview_date'] ? substr($applicant['interview_date'], 11, 5) : '' ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Location / Meeting Link</label>
                                <input type="text" name="interview_location" class="form-control form-control-sm" placeholder="e.g. Google Meet Link or Office Address" value="<?= e($applicant['interview_location'] ?? '') ?>">
                            </div>
                        </div>

                        <script>
                        function toggleInterviewFields() {
                            const status = document.getElementById('statusSelect').value;
                            const fields = document.getElementById('interviewFields');
                            fields.style.display = (status === 'interview') ? 'block' : 'none';
                        }
                        </script>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resume Preview Modal -->
<div class="modal fade" id="resumeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="height: 90vh;">
        <div class="modal-content h-100 shadow">
            <div class="modal-header py-2 border-bottom">
                <h6 class="modal-title fw-bold text-primary">Resume Preview: <?= e($applicant['first_name'] . ' ' . $applicant['last_name']) ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-light h-100 overflow-auto">
                <div id="previewContainer" class="h-100 w-100 bg-white shadow-inner">
                    <!-- Preview content will be injected here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts for Docx Preview -->
<script src="https://unpkg.com/jszip/dist/jszip.min.js"></script>
<script src="https://unpkg.com/docx-preview/dist/docx-preview.js"></script>

<script>
function viewResume(url) {
    const container = document.getElementById('previewContainer');
    const ext = url.split('.').pop().toLowerCase();
    
    // Clear previous content and show modal
    container.innerHTML = '';
    const modal = new bootstrap.Modal(document.getElementById('resumeModal'));
    modal.show();

    if (ext === 'pdf') {
        container.innerHTML = `<iframe src="${url}" class="w-100 h-100" style="border:none;"></iframe>`;
    } else if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
        container.innerHTML = `<div class="d-flex align-items-center justify-content-center h-100"><img src="${url}" class="img-fluid shadow" style="max-height: 95%;"></div>`;
    } else if (ext === 'docx') {
        // Show loading spinner
        container.innerHTML = `
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <div class="text-muted small">Loading document preview...</div>
            </div>`;
        
        fetch(url)
            .then(res => res.arrayBuffer())
            .then(buffer => {
                container.innerHTML = ''; // Clear spinner
                docx.renderAsync(buffer, container, null, {
                    className: "docx", //class name/prefix for default and user defined objects
                    inWrapper: true, //enables rendering of wrapper around document content
                    ignoreWidth: false, //disables rendering of width from document
                    ignoreHeight: false, //disables rendering of height from document
                    ignoreFonts: false, //disables fonts rendering
                    breakPages: true, //enables page breaking on page breaks
                    ignoreLastRenderedPageBreak: true, //disables page breaking on lastRenderedPageBreak elements
                    experimental: false, //enables experimental features (for example: custom fonts)
                    trimXmlDeclaration: true, //if true, xml declaration will be removed from xml documents
                    debug: false, //enables logging to console
                });
            })
            .catch(err => {
                console.error("Preview error:", err);
                showDownloadFallback(url, ext, "Error loading preview. It might be due to security restrictions or file corruption.");
            });
    } else {
        showDownloadFallback(url, ext);
    }
}

function showDownloadFallback(url, ext, customMsg = null) {
    const container = document.getElementById('previewContainer');
    const msg = customMsg || `Browsers cannot display .${ext} files directly. Please download the file to view it.`;
    container.innerHTML = `
        <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center p-5">
            <i class="bi bi-file-earmark-word text-secondary display-1 mb-4"></i>
            <h4 class="fw-bold">Preview not available</h4>
            <p class="text-muted mb-4" style="max-width: 400px;">${msg}</p>
            <a href="${url}" class="btn btn-primary px-4 py-2 shadow-sm">
                <i class="bi bi-download me-2"></i>Download Resume
            </a>
        </div>`;
}

// Clear container on modal close to free memory
document.getElementById('resumeModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('previewContainer').innerHTML = '';
});
</script>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
