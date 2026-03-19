<?php /** Performance Reviews List */
$pageTitle='Performance Reviews'; $breadcrumb=[['label'=>'Performance','active'=>true]];
include APP_ROOT.'/views/layouts/header.php';?>
<div class="container-fluid px-4 py-3">
<div class="d-flex justify-content-between align-items-center mb-3">
<h5 class="fw-700 mb-0"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Performance Reviews</h5>
<?php if(can('performance','create')):?><a href="index.php?module=performance&action=create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Create Review</a><?php endif;?>
</div>
<div class="card table-card"><div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th>Employee</th><th>Period</th><th>Reviewer</th><th>Score</th><th>Status</th><th>Date</th><th></th></tr></thead>
<tbody>
<?php if(empty($reviews)):?><tr><td colspan="7"><div class="empty-state"><i class="bi bi-graph-up-arrow"></i>No performance reviews yet.</div></td></tr><?php endif;?>
<?php foreach($reviews as $r):?>
<tr>
    <td class="fw-medium small"><?=e($r['employee_name']??'—')?></td>
    <td class="small"><?=e($r['review_period'])?></td>
    <td class="small"><?=e($r['reviewer_name']??'—')?></td>
    <td><?php if($r['overall_score']): ?><span class="badge bg-<?=$r['overall_score']>=4?'success':($r['overall_score']>=3?'warning':'danger')?>"><?=number_format($r['overall_score'],1)?>/5</span><?php else: ?>—<?php endif;?></td>
    <td><?=statusBadge($r['status'])?></td>
    <td class="small"><?=formatDate($r['review_date'])?></td>
    <td><a href="index.php?module=performance&action=view&id=<?=$r['id']?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a></td>
</tr>
<?php endforeach;?>
</tbody>
</table></div></div>
</div>
<?php include APP_ROOT.'/views/layouts/footer.php';?>
