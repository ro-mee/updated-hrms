<?php
$pageTitle = 'My Profile';
$breadcrumb = [
    ['label' => 'My Profile', 'active' => true]
];
include APP_ROOT . '/views/layouts/header.php';

// Collect login points with coordinates for Mapbox
$mapPoints = [];
if (!empty($loginHistory)) {
    foreach ($loginHistory as $log) {
        if (!empty($log['latitude']) && !empty($log['longitude'])) {
            $mapPoints[] = [
                'lng'         => (float)$log['longitude'],
                'lat'         => (float)$log['latitude'],
                'ip'          => $log['ip_address'],
                'city'        => $log['city'] ?? '',
                'country'     => $log['country'] ?? '',
                'device'      => $log['device'] ?? '',
                'time'        => $log['login_time'],
                'suspicious'  => (bool)($log['is_suspicious'] || $log['is_new_ip']),
            ];
        }
    }
}
?>
<div class="container-fluid px-4 py-3">

    <?php if ($lastLogin): ?>
    <!-- Last Login Banner -->
    <div class="alert alert-<?= ($lastLogin['is_suspicious'] || $lastLogin['is_new_ip']) ? 'warning border-warning' : 'info border-info' ?> border-start border-4 d-flex align-items-start gap-3 py-3 mb-4 shadow-sm" role="alert">
        <i class="bi bi-shield-check fs-4 mt-1 <?= ($lastLogin['is_suspicious'] || $lastLogin['is_new_ip']) ? 'text-warning' : 'text-info' ?>"></i>
        <div class="flex-grow-1">
            <div class="fw-bold mb-1">
                <?= ($lastLogin['is_suspicious'] || $lastLogin['is_new_ip']) ? '⚠️ Unusual Login Detected' : '✅ Last Login Info' ?>
            </div>
            <div class="small text-muted">
                <span class="me-3"><i class="bi bi-clock me-1"></i><?= formatDateTime($lastLogin['login_time']) ?></span>
                <span class="me-3"><i class="bi bi-geo-alt me-1"></i><?= e(trim(($lastLogin['city'] ?? '') . ', ' . ($lastLogin['country'] ?? ''), ', ') ?: 'Unknown Location') ?></span>
                <span class="me-3"><i class="bi bi-display me-1"></i><?= e($lastLogin['device'] ?? 'Unknown Device') ?></span>
                <code class="small"><i class="bi bi-hdd-network me-1"></i><?= e($lastLogin['ip_address']) ?></code>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Profile Picture & Quick Info -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body p-4">
                    <img src="<?= avatarUrl($user['avatar']) ?>" alt="Avatar" class="rounded-circle mb-3 border border-3 border-white shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                    <h5 class="fw-bold mb-1"><?= e($user['first_name'] . ' ' . $user['last_name']) ?></h5>
                    <div class="text-muted small mb-3"><?= e(ucwords(str_replace('_', ' ', $user['role']))) ?></div>
                    
                    <?php if($employee): ?>
                    <hr class="my-3">
                    <ul class="list-group list-group-flush text-start small">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Employee No.</span> <strong><?= e($employee['employee_number']) ?></strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Department</span> <strong><?= e($employee['department_name']) ?></strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Position</span> <strong><?= e($employee['position_title']) ?></strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Status</span> <div><?= statusBadge($employee['status']) ?></div>
                        </li>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Update Profile Form -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white pt-3 pb-2 border-0">
                    <h6 class="fw-bold text-muted"><i class="bi bi-person-gear text-primary me-2"></i>Edit Account Details</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?module=profile&action=update" enctype="multipart/form-data">
                        <?= csrfField() ?>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="<?= e($user['first_name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" value="<?= e($user['last_name']) ?>" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Update Profile Picture</label>
                                <input type="file" name="avatar" class="form-control" accept="image/*">
                                <div class="form-text small">Accepted formats: JPG, PNG. Max size: 2MB.</div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Save Changes</button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div><!-- end .row -->

    <!-- Login History Section -->
    <div class="row g-4 mt-0">
        <div class="col-12">
            <div class="card shadow-sm border-0 mt-2">
                <div class="card-header bg-white pt-3 pb-2 border-0 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-muted mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Login History <span class="badge bg-primary ms-1"><?= count($loginHistory ?? []) ?></span></h6>
                    <?php if (!empty($mapPoints)): ?>
                    <button class="btn btn-sm btn-outline-secondary" id="toggleMapBtn" onclick="toggleMap()">
                        <i class="bi bi-map me-1"></i>Show Map
                    </button>
                    <?php endif; ?>
                </div>

                <!-- Mapbox Map -->
                <?php if (!empty($mapPoints)): ?>
                <div id="loginMapContainer" style="display:none;">
                    <div id="loginMap" style="height: 320px; border-radius: 0;"></div>
                </div>
                <?php endif; ?>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="ps-4" style="min-width:150px">Date &amp; Time</th>
                                    <th style="min-width:130px">IP Address</th>
                                    <th style="min-width:160px">Location</th>
                                    <th style="min-width:220px">Device</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($loginHistory)): ?>
                                    <?php foreach ($loginHistory as $log): 
                                        $isFlagged = $log['is_suspicious'] || $log['is_new_ip'];
                                    ?>
                                    <tr class="<?= $isFlagged ? 'table-warning' : '' ?>">
                                        <td class="ps-4">
                                            <div class="fw-medium text-dark small"><?= formatDateTime($log['login_time']) ?></div>
                                            <div class="text-muted" style="font-size:.75rem"><?= timeAgo($log['login_time']) ?></div>
                                        </td>
                                        <td><code class="small"><?= e($log['ip_address']) ?></code></td>
                                        <td>
                                            <?php if (!empty($log['city']) || !empty($log['country'])): ?>
                                                <i class="bi bi-geo-alt-fill text-danger me-1 small"></i>
                                                <?= e(($log['city'] ? $log['city'] . ', ' : '') . ($log['country'] ?? '')) ?>
                                            <?php else: ?>
                                                <span class="text-muted fst-italic small">Unknown</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="small text-truncate d-inline-block" style="max-width:220px;" title="<?= e($log['device']) ?>">
                                                <?= e($log['device'] ?: '—') ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($log['is_suspicious']): ?>
                                                <span class="badge bg-danger"><i class="bi bi-exclamation-octagon-fill me-1"></i>Suspicious</span>
                                            <?php elseif ($log['is_new_ip']): ?>
                                                <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle-fill me-1"></i>New IP</span>
                                            <?php else: ?>
                                                <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Normal</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="bi bi-clock-history fs-3 d-block mb-2 text-muted opacity-50"></i>
                                            No login history recorded yet.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($mapPoints)): ?>
<!-- Leaflet.js + OpenStreetMap (100% free, no token required) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const MAP_POINTS = <?= json_encode($mapPoints, JSON_UNESCAPED_UNICODE) ?>;
let mapInitialized = false;
let leafletMap     = null;

function toggleMap() {
    const container = document.getElementById('loginMapContainer');
    const btn       = document.getElementById('toggleMapBtn');
    if (container.style.display === 'none') {
        container.style.display = 'block';
        btn.innerHTML = '<i class="bi bi-map me-1"></i>Hide Map';
        if (!mapInitialized) initMap();
        else if (leafletMap) leafletMap.invalidateSize();
    } else {
        container.style.display = 'none';
        btn.innerHTML = '<i class="bi bi-map me-1"></i>Show Map';
    }
}

function initMap() {
    const valid = MAP_POINTS.filter(p => p.lat && p.lng);
    const first = valid[0] || { lat: 14.5, lng: 121.0 };

    leafletMap = L.map('loginMap', { zoomControl: true }).setView([first.lat, first.lng], 4);

    // OpenStreetMap tiles — completely free
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 18,
    }).addTo(leafletMap);

    const bounds = [];
    valid.forEach(function(pt) {
        const color   = pt.suspicious ? '#f59e0b' : '#10b981';
        const glyph   = pt.suspicious ? '⚠️' : '✅';
        const loc     = [pt.city, pt.country].filter(Boolean).join(', ') || 'Unknown';
        const flagHtml = pt.suspicious
            ? '<div style="color:#d97706;margin-top:6px;font-weight:600;">⚠️ Flagged Activity</div>' : '';

        const icon = L.divIcon({
            className: '',
            html: `<div style="
                width:14px;height:14px;border-radius:50%;
                background:${color};border:3px solid #fff;
                box-shadow:0 0 0 3px ${color}88;"></div>`,
            iconSize: [14, 14],
            iconAnchor: [7, 7],
        });

        const marker = L.marker([pt.lat, pt.lng], { icon })
            .bindPopup(`
                <div style="font-family:system-ui;font-size:13px;min-width:180px;">
                    <div style="font-weight:700;margin-bottom:4px;">${glyph} ${loc}</div>
                    <div style="color:#555;margin-bottom:2px;"><b>IP:</b> ${pt.ip}</div>
                    <div style="color:#555;margin-bottom:2px;"><b>Device:</b> ${pt.device || 'Unknown'}</div>
                    <div style="color:#555;"><b>Time:</b> ${pt.time}</div>
                    ${flagHtml}
                </div>`, { maxWidth: 260 })
            .addTo(leafletMap);

        bounds.push([pt.lat, pt.lng]);
    });

    // Auto-fit to show all markers
    if (bounds.length > 1) {
        leafletMap.fitBounds(bounds, { padding: [40, 40], maxZoom: 8 });
    }
    mapInitialized = true;
}
</script>
<?php endif; ?>


<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
