<?php /** Training Programs List */
$pageTitle='Training Programs'; $breadcrumb=[['label'=>'Training Programs','active'=>true]];
include APP_ROOT.'/views/layouts/header.php';?>

<style>
.training-card {
    border: 1px solid var(--hrms-border);
    border-radius: 14px;
    background: var(--hrms-card-bg);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.training-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.15);
}
.training-card-header {
    padding: 1rem 1.25rem 0.75rem;
    border-bottom: 1px solid var(--hrms-border);
    background: var(--hrms-card-bg);
}
.training-card-body {
    padding: 1rem 1.25rem;
    flex: 1;
}
.training-card-footer {
    padding: 0.85rem 1.25rem;
    border-top: 1px solid var(--hrms-border);
    background: var(--hrms-card-bg);
}
.training-title {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 0.15rem;
    color: var(--hrms-text-main);
}
.training-meta {
    font-size: 0.8rem;
    color: var(--hrms-text-muted);
    display: flex;
    align-items: center;
    gap: 0.35rem;
    margin-bottom: 0.4rem;
}
.training-meta i { font-size: 0.85rem; }
.training-stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.78rem;
    padding: 0.25rem 0.65rem;
    border-radius: 50px;
    font-weight: 600;
}
.stat-pill-date { background: rgba(var(--bs-primary-rgb), 0.12); color: var(--bs-primary); }
.stat-pill-cost { background: rgba(var(--bs-success-rgb), 0.12); color: var(--bs-success); }
.enroll-btn { border-radius: 8px; font-weight: 600; font-size: 0.85rem; }
.filter-bar .btn { font-size: 0.82rem; border-radius: 8px; }
.badge-ongoing { background-color: #e67e22 !important; color: #fff !important; }
.badge-completed { background-color: #5b8fa8 !important; color: #fff !important; }
</style>

<div class="container-fluid px-4 py-3">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-700 mb-0"><i class="bi bi-mortarboard text-primary me-2"></i>Training Programs</h5>
        <?php if(can('training','manage')):?>
        <a href="index.php?module=training&action=create" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Create Training
        </a>
        <?php endif;?>
    </div>

    <!-- Status Filter -->
    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
        <span class="text-muted small fw-600 me-1"><i class="bi bi-funnel me-1"></i>Filter by status:</span>
        <?php $activeStatus = sanitizeInput(get('status')); ?>
        <a href="index.php?module=training" class="btn btn-sm <?= empty($activeStatus) ? 'btn-primary' : 'btn-outline-secondary' ?>">All</a>
        <a href="index.php?module=training&status=scheduled" class="btn btn-sm <?= $activeStatus==='scheduled' ? 'btn-warning text-dark' : 'btn-outline-secondary' ?>">Scheduled</a>
        <a href="index.php?module=training&status=ongoing" class="btn btn-sm <?= $activeStatus==='ongoing' ? 'btn-success' : 'btn-outline-secondary' ?>">Ongoing</a>
        <a href="index.php?module=training&status=completed" class="btn btn-sm <?= $activeStatus==='completed' ? 'btn-secondary' : 'btn-outline-secondary' ?>">Completed</a>
    </div>

    <!-- Training Cards -->
    <div class="row g-3">
        <?php if(empty($trainings)):?>
        <div class="col-12">
            <div class="empty-state"><i class="bi bi-mortarboard"></i>No training programs found.</div>
        </div>
        <?php endif;?>
        <?php foreach($trainings as $t):
            $enrolled = in_array($t['id'], array_column($myEnrollments,'training_id'));
            $statusColors = ['scheduled'=>['bg'=>'warning','icon'=>'clock'],'ongoing'=>['bg'=>'success','icon'=>'play-circle'],'completed'=>['bg'=>'secondary','icon'=>'check-circle']];
            $sc = $statusColors[$t['status']] ?? ['bg'=>'secondary','icon'=>'circle'];
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="training-card">
                <div class="training-card-header">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="d-flex align-items-center gap-1">
                            <?= statusBadge($t['status']) ?>
                            <?php if(!empty($t['is_required'])): ?>
                            <span class="badge px-2" style="background:rgba(220,53,69,0.12);color:#dc3545;border:1px solid rgba(220,53,69,0.25);">
                                <i class="bi bi-shield-check me-1"></i>Required
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php if(!empty($t['department_name'])): ?>
                            <span class="badge bg-info-subtle border border-info-subtle text-info-emphasis">
                                <i class="bi bi-building me-1"></i><?=e($t['department_name'])?> Only
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle border border-secondary-subtle text-secondary-emphasis">
                                <i class="bi bi-globe me-1"></i>All Departments
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="training-title mt-2 mb-0"><?=e($t['title'])?></p>
                </div>
                <div class="training-card-body">
                    <div class="training-meta"><i class="bi bi-person-fill text-primary"></i><?=e($t['trainer']??'Trainer TBD')?></div>
                    <?php if($t['location']):?>
                    <div class="training-meta"><i class="bi bi-geo-alt-fill text-danger"></i><?=e($t['location'])?></div>
                    <?php endif;?>
                    <?php if(!empty($t['enrolled_count'])):?>
                    <div class="training-meta"><i class="bi bi-people-fill text-info"></i><?=$t['enrolled_count']?> enrolled</div>
                    <?php endif;?>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <span class="training-stat-pill stat-pill-date">
                            <i class="bi bi-calendar-event"></i>
                            <?php if(!empty($t['end_date']) && $t['end_date'] !== $t['start_date']): ?>
                                <?=formatDate($t['start_date'],'M d')?> – <?=formatDate($t['end_date'],'M d, Y')?>
                            <?php else: ?>
                                <?=formatDate($t['start_date'],'M d, Y')?>
                            <?php endif; ?>
                        </span>
                        <?php if(!empty($t['start_time'])): ?>
                        <span class="training-stat-pill bg-light text-dark border">
                            <i class="bi bi-clock me-1"></i>
                            <?= date('h:i A', strtotime($t['start_time'])) ?> 
                            <?= !empty($t['end_time']) ? ' - ' . date('h:i A', strtotime($t['end_time'])) : '' ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="training-card-footer">
                    <?php 
                    $isAdminManager = hasRole('super_admin','hr_director','hr_specialist','department_manager');
                    $myEnroll = null;
                    foreach($myEnrollments as $me) { if($me['training_id']==$t['id']) { $myEnroll=$me; break; } }
                    $hasFeedback = !empty($myEnroll['rating']);
                    ?>
                    <?php if($t['status']==='completed'):?>
                        <div class="d-flex flex-column gap-2">
                        <?php if($isAdminManager): ?>
                            <!-- Admin/HR/Managers always see View Feedbacks for completed programs -->
                            <a href="index.php?module=training&action=viewFeedback&id=<?=$t['id']?>" class="btn btn-sm w-100 fw-600"
                                style="background:rgba(var(--bs-primary-rgb),0.1);color:var(--bs-primary);border:1px solid rgba(var(--bs-primary-rgb),0.25);border-radius:8px;">
                                <i class="bi bi-bar-chart me-1"></i>View Feedbacks
                            </a>
                        <?php elseif($enrolled): ?>
                            <!-- Regular Employees see Leave Feedback if enrolled and not yet submitted -->
                            <?php if($hasFeedback):?>
                                <div class="d-flex align-items-center justify-content-center gap-2 small fw-600" style="color:#5b8fa8">
                                    <i class="bi bi-star-fill"></i>
                                    Feedback submitted (<?= $myEnroll['rating'] ?>/5)
                                </div>
                            <?php else:?>
                                <button class="btn btn-sm w-100 fw-600" style="background:rgba(91,143,168,0.12);color:#5b8fa8;border:1px solid rgba(91,143,168,0.3);border-radius:8px;"
                                    onclick="openFeedback(<?=$t['id']?>, '<?=e($t['title'])?>')"> 
                                    <i class="bi bi-chat-left-text me-1"></i>Leave Feedback
                                </button>
                            <?php endif;?>
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center gap-2 text-muted fw-600 small">
                                <i class="bi bi-check2-all"></i> Training Completed
                            </div>
                        <?php endif; ?>
                        </div>
                    <?php elseif($enrolled):?>
                        <div class="d-flex align-items-center justify-content-center gap-2 text-success fw-600 small">
                            <i class="bi bi-check-circle-fill"></i> You are enrolled
                        </div>
                    <?php elseif($t['status']==='scheduled' && currentUser()['employee_id']):?>
                        <button class="btn btn-primary btn-sm enroll-btn w-100" onclick="enrollTraining(<?=$t['id']?>)">
                            <i class="bi bi-person-plus me-1"></i>Enroll Now
                        </button>
                    <?php elseif($t['status']==='ongoing'):?>
                        <div class="d-flex align-items-center justify-content-center gap-2 text-warning fw-600 small">
                            <i class="bi bi-play-circle-fill"></i> Currently Ongoing
                        </div>
                    <?php else:?>
                        <div class="text-muted small text-center">Enrollment not available</div>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <?php endforeach;?>
    </div>
</div>

<form id="enrollForm" method="POST" action="index.php?module=training&action=enroll" class="d-none">
<?=csrfField()?><input type="hidden" name="training_id" id="enrollId">
</form>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;border:1px solid var(--hrms-border);background:var(--hrms-card-bg);">
      <div class="modal-header border-0 pb-0">
        <h6 class="fw-700 mb-0"><i class="bi bi-chat-left-text text-primary me-2"></i>Training Feedback</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted small mb-3" id="feedbackTrainingTitle"></p>
        <div class="mb-3">
          <label class="form-label fw-600">Rating <span class="text-danger">*</span></label>
          <div class="d-flex gap-2" id="starRating">
            <?php for($i=1;$i<=5;$i++):?>
            <button type="button" class="btn btn-sm star-btn" data-val="<?=$i?>" onclick="setRating(<?=$i?>)"
              style="font-size:1.4rem;padding:0;background:none;border:none;color:#ccc;transition:color 0.15s;">
              <i class="bi bi-star-fill"></i>
            </button>
            <?php endfor;?>
          </div>
          <input type="hidden" id="fbRating" value="">
        </div>
        <div class="mb-3">
          <label class="form-label fw-600">Comments <small class="text-muted fw-400">(optional)</small></label>
          <textarea id="fbText" class="form-control" rows="3" placeholder="Share your experience with this training..."></textarea>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="submitFeedback(event)">
          <i class="bi bi-send me-1"></i>Submit
        </button>
      </div>
    </div>
  </div>
</div>

<script>
let _fbTrainingId = null;
function openFeedback(id, title) {
    _fbTrainingId = id;
    document.getElementById('feedbackTrainingTitle').textContent = title;
    document.getElementById('fbRating').value = '';
    document.getElementById('fbText').value = '';
    document.querySelectorAll('.star-btn').forEach(b => b.style.color='#ccc');
    new bootstrap.Modal(document.getElementById('feedbackModal')).show();
}
function setRating(val) {
    document.getElementById('fbRating').value = val;
    document.querySelectorAll('.star-btn').forEach(b => {
        b.style.color = parseInt(b.dataset.val) <= val ? '#f4a819' : '#ccc';
    });
}
async function submitFeedback(event) {
    const btn = event.target.closest('button');
    const rating = document.getElementById('fbRating').value;
    if (!rating) { showToast('warning', 'Please select a rating.'); return; }
    
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Submitting...';

    try {
        const res = await postJson('index.php?module=training&action=feedback', {
            training_id: _fbTrainingId,
            rating: rating,
            feedback: document.getElementById('fbText').value
        });
        showToast(res.success ? 'success' : 'danger', res.message);
        if (res.success) { 
            bootstrap.Modal.getInstance(document.getElementById('feedbackModal')).hide(); 
            setTimeout(()=>location.reload(), 1000); 
        } else {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    } catch (e) {
        showToast('danger', 'Request failed. Please try again.');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }
}
async function enrollTraining(id){
    const res = await postJson('index.php?module=training&action=enroll',{training_id:id});
    showToast(res.success?'success':'danger',res.message);
    if(res.success) setTimeout(()=>location.reload(),1000);
}
</script>
<?php include APP_ROOT.'/views/layouts/footer.php';?>

