/**
 * NexaHR - Main JavaScript
 */
'use strict';

/* ── CSRF Helper ─────────────────────────────────────────── */
function getCsrf() {
    const el = document.querySelector('input[name="csrf_token"]');
    return el ? el.value : '';
}

/* ── AJAX POST Helper ────────────────────────────────────── */
async function postJson(url, data = {}) {
    data.csrf_token = getCsrf();
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams(data),
    });
    return res.json();
}

/* ── Sidebar Toggle ──────────────────────────────────────── */
const sidebar  = document.getElementById('sidebar');
const mainWrap = document.getElementById('mainWrapper');

function createOverlay() {
    let ov = document.getElementById('sidebarOverlay');
    if (!ov) {
        ov = document.createElement('div');
        ov.className = 'sidebar-overlay';
        ov.id = 'sidebarOverlay';
        ov.addEventListener('click', closeMobileSidebar);
        document.body.appendChild(ov);
    }
    return ov;
}

function closeMobileSidebar() {
    sidebar?.classList.remove('open');
    document.getElementById('sidebarOverlay')?.classList.remove('show');
}

document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    if (window.innerWidth < 992) {
        sidebar?.classList.toggle('open');
        createOverlay().classList.toggle('show', sidebar?.classList.contains('open'));
    } else {
        const isCollapsed = sidebar?.classList.toggle('collapsed');
        mainWrap?.classList.toggle('expanded');
        // Set cookie for server-side persistence (lasts 1 year)
        document.cookie = `hrms_sidebar_collapsed=${isCollapsed ? '1' : '0'};path=/;max-age=31536000`;
        localStorage.setItem('sidebarCollapsed', isCollapsed ? '1' : '0');
    }
});

document.getElementById('closeSidebar')?.addEventListener('click', closeMobileSidebar);

// Restore sidebar state immediately if JS loads late (already handled by PHP but good for sync)
if (window.innerWidth >= 992 && localStorage.getItem('sidebarCollapsed') === '1') {
    sidebar?.classList.add('collapsed');
    mainWrap?.classList.add('expanded');
}

/* ── Dark Mode Toggle ────────────────────────────────────── */
const themeBtn = document.getElementById('themeToggle');
function applyTheme(dark) {
    document.documentElement.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
    document.cookie = `hrms_theme=${dark ? 'dark' : 'light'};path=/;max-age=31536000`;
    if (themeBtn) themeBtn.querySelector('i').className = dark ? 'bi bi-sun fs-5' : 'bi bi-moon-stars fs-5';
}
const savedTheme = document.cookie.match(/hrms_theme=(dark|light)/)?.[1];
if (savedTheme) applyTheme(savedTheme === 'dark');

themeBtn?.addEventListener('click', () => {
    applyTheme(document.documentElement.getAttribute('data-bs-theme') !== 'dark');
});

/* ── Notification Dropdown JS removed to prioritize native PHP rendering ── */

/* ── XSS-safe HTML escape ────────────────────────────────── */
function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ── Simple "time ago" ───────────────────────────────────── */
function timeAgo(dateStr) {
    const now = new Date(), d = new Date(dateStr);
    const diff = Math.floor((now - d) / 1000);
    if (diff < 60)  return 'just now';
    if (diff < 3600) return Math.floor(diff/60) + 'm ago';
    if (diff < 86400) return Math.floor(diff/3600) + 'h ago';
    return Math.floor(diff/86400) + 'd ago';
}

/* ── Live Clock ──────────────────────────────────────────── */
function updateClock() {
    const el = document.getElementById('liveClock');
    if (!el) return;
    const now = new Date();
    el.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const dateEl = document.getElementById('liveDate');
    if (dateEl) dateEl.textContent = now.toLocaleDateString('en-US', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
}
setInterval(updateClock, 1000);
updateClock();

/* ── Break Timer ─────────────────────────────────────────── */
function updateBreakTimer() {
    const timerEl = document.getElementById('breakTimer');
    if (!timerEl) return;
    const startTimestamp = parseInt(timerEl.dataset.start, 10);
    const durationSeconds = parseInt(timerEl.dataset.duration, 10);
    // Use server-synced time ideally, but local time is close enough for display
    const nowTimestamp = Math.floor(Date.now() / 1000);
    
    const elapsed = nowTimestamp - startTimestamp;
    const remaining = durationSeconds - elapsed;
    
    const isNegative = remaining < 0;
    const absRemaining = Math.abs(remaining);
    
    const m = Math.floor(absRemaining / 60).toString().padStart(2, '0');
    const s = (absRemaining % 60).toString().padStart(2, '0');
    
    let timeString = (isNegative ? '-' : '') + m + ':' + s;
    timerEl.textContent = timeString;
    
    if (isNegative) {
        timerEl.classList.remove('text-warning');
        timerEl.classList.add('text-danger');
        timerEl.style.animation = 'flash 1s infinite alternate';
        // Add quick inline keyframes if not present
        if (!document.getElementById('flashAnim')) {
            const style = document.createElement('style');
            style.id = 'flashAnim';
            style.innerHTML = `@keyframes flash { from { opacity: 1; } to { opacity: 0.5; } }`;
            document.head.appendChild(style);
        }

        // Show centered popup once
        if (!window.breakAlertShown) {
            window.breakAlertShown = true;
            let modalEl = document.getElementById('breakOverModal');
            if (!modalEl) {
                document.body.insertAdjacentHTML('beforeend', `
                <div class="modal fade" id="breakOverModal" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-danger shadow-lg">
                      <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="bi bi-exclamation-octagon-fill me-2"></i>Break Time Over</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body text-center py-4">
                        <i class="bi bi-alarm-fill text-danger mb-2" style="font-size: 3rem;"></i>
                        <h4 class="mt-2 text-dark">Your break time is up!</h4>
                        <p class="text-muted mb-0 mt-2">Please end your break and return to work as soon as possible.</p>
                      </div>
                      <div class="modal-footer justify-content-center border-0 pb-4">
                        <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Acknowledge</button>
                      </div>
                    </div>
                  </div>
                </div>`);
                modalEl = document.getElementById('breakOverModal');
            }
            if (typeof bootstrap !== 'undefined') {
                new bootstrap.Modal(modalEl).show();
            } else {
                alert("Your break time is up! Please end your break.");
            }
        }
    }
}
setInterval(updateBreakTimer, 1000);
updateBreakTimer();

/* ── Clock In / Out ──────────────────────────────────────── */
async function handleClock(action) {
    const btn = document.getElementById(action === 'in' ? 'clockInBtn' : 'clockOutBtn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>'; }
    try {
        const res = await postJson(`index.php?module=attendance&action=clock${action === 'in' ? 'In' : 'Out'}`);
        showToast(res.success ? 'success' : 'danger', res.message);
        if (res.success) setTimeout(() => location.reload(), 1200);
    } catch(e) {
        showToast('danger', 'Request failed. Please try again.');
    }
    if (btn) { btn.disabled = false; }
}

/* ── Break Tracking ──────────────────────────────────────── */
async function handleBreak(action) {
    const btn = event?.currentTarget;
    const originalHtml = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>'; }
    try {
        const res = await postJson(`index.php?module=attendance&action=${action}`);
        showToast(res.success ? 'success' : 'danger', res.message);
        if (res.success) setTimeout(() => location.reload(), 1200);
    } catch(e) {
        showToast('danger', 'Request failed. Please try again.');
    }
    if (btn) { btn.disabled = false; btn.innerHTML = originalHtml; }
}

/* ── Toast helper ────────────────────────────────────────── */
function showToast(type, msg) {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = 9999;
        document.body.appendChild(container);
    }
    const id  = 'toast_' + Date.now();
    const icon = type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill';
    container.insertAdjacentHTML('beforeend', `
    <div id="${id}" class="toast align-items-center text-bg-${type} border-0 show" role="alert">
      <div class="d-flex">
        <div class="toast-body"><i class="bi bi-${icon} me-2"></i>${escHtml(msg)}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>`);
    setTimeout(() => document.getElementById(id)?.remove(), 4000);
}

/* ── Confirm Delete helper ───────────────────────────────── */
document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
        if (!confirm(el.dataset.confirm || 'Are you sure?')) e.preventDefault();
    });
});

/* ── Table search filter ─────────────────────────────────── */
const tableSearch = document.getElementById('tableSearch');
tableSearch?.addEventListener('input', () => {
    const q = tableSearch.value.toLowerCase();
    document.querySelectorAll('#dataTable tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

/* ── Auto-dismiss alerts ─────────────────────────────────── */
document.querySelectorAll('.alert:not(.alert-permanent)').forEach(el => {
    setTimeout(() => {
        el.style.opacity = '0';
        el.style.transition = 'opacity .5s';
        setTimeout(() => el.remove(), 500);
    }, 4500);
});
