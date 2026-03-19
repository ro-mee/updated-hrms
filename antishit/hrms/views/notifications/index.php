<?php
/**
 * Notifications View
 */
$pageTitle  = 'Notifications';
$breadcrumb = [['label'=>'Notifications','active'=>true]];
include APP_ROOT . '/views/layouts/header.php';
?>
<div class="container-fluid px-4 py-3">
<h5 class="fw-700 mb-3"><i class="bi bi-bell text-primary me-2"></i>Notifications</h5>
<div class="card">
<?php if(empty($notifications)):?>
<div class="empty-state"><i class="bi bi-bell-slash"></i>No notifications</div>
<?php else:?>
<div class="list-group list-group-flush">
<?php foreach($notifications as $n):?>
<a href="index.php?module=notifications&action=read&id=<?= $n['id'] ?>" class="list-group-item list-group-item-action px-4 py-3 <?=$n['is_read']==0?'bg-primary bg-opacity-05':'';?> text-decoration-none">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <div class="fw-600 mb-1 text-dark"><?=e($n['title'])?></div>
            <div class="text-muted small"><?=e($n['message'])?></div>
        </div>
        <span class="text-muted small ms-3 text-nowrap"><?=timeAgo($n['created_at'])?></span>
    </div>
</a>
<?php endforeach;?>
</div>
<?php endif;?>
</div>
</div>
<?php include APP_ROOT.'/views/layouts/footer.php';?>
