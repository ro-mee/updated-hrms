/**
 * HRMS Pro - Main JavaScript
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
