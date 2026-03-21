<?php
/**
 * Public Job Application Form View
 */
$pageTitle = 'Apply: ' . e($job['title']);
include APP_ROOT . '/views/layouts/public_header.php';
?>
<div class="header-section py-5 bg-white border-bottom text-center mb-5">
    <div class="container py-2">
        <a href="index.php?module=jobs&action=view&id=<?= $job['id'] ?>" class="text-decoration-none small mb-3 d-inline-block"><i class="bi bi-arrow-left me-1"></i>Back to Job Details</a>
        <h1 class="fw-bold mb-2">Apply for <?= e($job['title']) ?></h1>
        <p class="text-muted">Fill out the form below to submit your application.</p>
    </div>
</div>

<div class="container mb-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-4">
                <div class="card-body">
                    <form method="POST" action="index.php?module=jobs&action=submit" enctype="multipart/form-data">
                        <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" value="<?= e(post('first_name')) ?>" required>
                                <?php if(isset($errors['first_name'])): ?><div class="invalid-feedback"><?= $errors['first_name'] ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" value="<?= e(post('last_name')) ?>" required>
                                <?php if(isset($errors['last_name'])): ?><div class="invalid-feedback"><?= $errors['last_name'] ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= e(post('email')) ?>" required>
                                <?php if(isset($errors['email'])): ?><div class="invalid-feedback"><?= $errors['email'] ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="<?= e(post('phone')) ?>" placeholder="e.g. 09301234567">
                            </div>
                            
                            <!-- Personal Information -->
                            <div class="col-12 mt-4"><h6 class="fw-bold text-primary border-bottom pb-2">Personal Information</h6></div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Birth Date</label>
                                <input type="date" name="birth_date" class="form-control" value="<?= e(post('birth_date')) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">-- Select --</option>
                                    <option value="male" <?= post('gender')==='male'?'selected':'' ?>>Male</option>
                                    <option value="female" <?= post('gender')==='female'?'selected':'' ?>>Female</option>
                                    <option value="prefer_not_to_say" <?= post('gender')==='prefer_not_to_say'?'selected':'' ?>>Prefer not to say</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Civil Status</label>
                                <select name="civil_status" class="form-select">
                                    <option value="">-- Select --</option>
                                    <option value="single" <?= post('civil_status')==='single'?'selected':'' ?>>Single</option>
                                    <option value="married" <?= post('civil_status')==='married'?'selected':'' ?>>Married</option>
                                    <option value="widowed" <?= post('civil_status')==='widowed'?'selected':'' ?>>Widowed</option>
                                    <option value="divorced" <?= post('civil_status')==='divorced'?'selected':'' ?>>Divorced</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Address</label>
                                <input type="text" name="address" class="form-control" value="<?= e(post('address')) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">City</label>
                                <input type="text" name="city" class="form-control" value="<?= e(post('city')) ?>">
                            </div>

                            <!-- Government IDs -->
                            <div class="col-12 mt-4"><h6 class="fw-bold text-primary border-bottom pb-2">Government IDs</h6></div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">SSS Number</label>
                                <input type="text" name="sss_number" class="form-control" value="<?= e(post('sss_number')) ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">PhilHealth No.</label>
                                <input type="text" name="philhealth_number" class="form-control" value="<?= e(post('philhealth_number')) ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Pag-IBIG No.</label>
                                <input type="text" name="pagibig_number" class="form-control" value="<?= e(post('pagibig_number')) ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">TIN Number</label>
                                <input type="text" name="tin_number" class="form-control" value="<?= e(post('tin_number')) ?>">
                            </div>

                            <!-- Emergency Contact -->
                            <div class="col-12 mt-4"><h6 class="fw-bold text-primary border-bottom pb-2">Emergency Contact</h6></div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Contact Name</label>
                                <input type="text" name="emergency_contact_name" class="form-control" value="<?= e(post('emergency_contact_name')) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Contact Phone</label>
                                <input type="text" name="emergency_contact_phone" class="form-control" value="<?= e(post('emergency_contact_phone')) ?>">
                            </div>

                            <div class="col-md-12 mt-4">
                                <label class="form-label fw-bold">Resume (PDF, DOC/X) <span class="text-danger">*</span></label>
                                <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
                                <input type="file" name="resume" class="form-control <?= isset($errors['resume']) ? 'is-invalid' : '' ?>" accept=".pdf,.doc,.docx" required onchange="checkFileSize(this)">
                                <div class="form-text">Please upload your latest resume (Max: 5MB).</div>
                                <?php if(isset($errors['resume'])): ?><div class="invalid-feedback"><?= $errors['resume'] ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Cover Letter</label>
                                <textarea name="cover_letter" class="form-control" rows="5" placeholder="Tell us why you're a great fit for this role..."><?= e(post('cover_letter')) ?></textarea>
                            </div>
                        </div>
                        
                        <div class="mt-4 d-grid">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-send me-2"></i>Submit Application</button>
                        </div>
                        <p class="text-muted small text-center mt-3">By clicking 'Submit Application', you agree to our privacy policy regarding recruitment.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkFileSize(input) {
    if (input.files && input.files[0]) {
        if (input.files[0].size > 5242880) { // 5MB
            alert("The file you selected is too large (" + (input.files[0].size / 1024 / 1024).toFixed(2) + " MB). The maximum allowed size is 5 MB. Please select a smaller file.");
            input.value = "";
        }
    }
}
</script>
<?php include APP_ROOT . '/views/layouts/public_footer.php'; ?>
