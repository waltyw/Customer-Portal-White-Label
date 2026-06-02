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

// Highlight current nav item (already handled server-side, but fix edge cases)
var currentPath = window.location.pathname;
document.querySelectorAll('.nav-item').forEach(function (link) {
    if (link.getAttribute('href') === currentPath) {
        link.classList.add('active');
    }
});

// Mobile side drawer
(function () {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('navOverlay');
    var toggle  = document.getElementById('sidebarToggle');

    if (!sidebar || !overlay || !toggle) return;

    function openDrawer() {
        sidebar.classList.add('is-open');
        overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    toggle.addEventListener('click', openDrawer);
    overlay.addEventListener('click', closeDrawer);

    sidebar.querySelectorAll('.nav-item').forEach(function (link) {
        link.addEventListener('click', closeDrawer);
    });
}());

// Auto-wrap tables for horizontal scroll on mobile
document.querySelectorAll('.table').forEach(function (table) {
    if (!table.parentElement.classList.contains('table-responsive')) {
        var wrap = document.createElement('div');
        wrap.className = 'table-responsive';
        table.parentNode.insertBefore(wrap, table);
        wrap.appendChild(table);
    }
});
