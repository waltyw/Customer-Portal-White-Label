'use strict';

// Auto-dismiss flash alerts after 6 seconds
document.querySelectorAll('.alert').forEach(function (el) {
    setTimeout(function () {
        el.style.transition = 'opacity .4s';
        el.style.opacity = '0';
        setTimeout(function () { el.remove(); }, 400);
    }, 6000);
});

// Confirm before destructive form submissions
document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('submit', function (e) {
        if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
});

// Highlight current nav item (server-side handles it, this fixes edge cases)
var currentPath = window.location.pathname;
document.querySelectorAll('.nav-item').forEach(function (link) {
    if (link.getAttribute('href') === currentPath) link.classList.add('active');
});

// ── Mobile drawer (kwt-saas pattern) ────────────────────────────────────────
(function () {
    var sidebar  = document.querySelector('.sidebar');
    var backdrop = document.getElementById('drawerBackdrop');
    var openBtn  = document.getElementById('menuOpen');
    var closeBtn = document.getElementById('menuClose');

    if (!sidebar || !backdrop || !openBtn) return;

    function open() {
        sidebar.classList.add('is-open');
        backdrop.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        sidebar.classList.remove('is-open');
        backdrop.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    openBtn.addEventListener('click', open);
    backdrop.addEventListener('click', close);
    if (closeBtn) closeBtn.addEventListener('click', close);

    // Close on nav tap (page navigates away anyway, but keeps it clean)
    sidebar.querySelectorAll('.nav-item').forEach(function (link) {
        link.addEventListener('click', close);
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') close();
    });
}());

// ── Wrap tables for horizontal scroll on mobile ──────────────────────────────
document.querySelectorAll('.table').forEach(function (table) {
    if (!table.parentElement.classList.contains('table-responsive')) {
        var wrap = document.createElement('div');
        wrap.className = 'table-responsive';
        table.parentNode.insertBefore(wrap, table);
        wrap.appendChild(table);
    }
});
