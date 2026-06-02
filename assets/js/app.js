'use strict';

// Auto-dismiss flash alerts after 6 seconds
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => {
        el.style.transition = 'opacity .4s';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 400);
    }, 6000);
});

// Confirm before destructive form submissions
document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('submit', e => {
        if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
});

// Highlight current nav item (already handled server-side, but fix edge cases)
const path = window.location.pathname;
document.querySelectorAll('.nav-item').forEach(link => {
    if (link.getAttribute('href') === path) {
        link.classList.add('active');
    }
});

// Mobile sidebar toggle — IIFE avoids variable name conflicts with page scripts
(function () {
    var navSidebar = document.getElementById('sidebar');
    var navOverlay = document.getElementById('sidebarOverlay');
    var navToggle  = document.getElementById('sidebarToggle');

    if (!navSidebar || !navOverlay || !navToggle) return;

    function openNav() {
        navSidebar.classList.add('is-open');
        navOverlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeNav() {
        navSidebar.classList.remove('is-open');
        navOverlay.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    navToggle.addEventListener('click', openNav);
    navOverlay.addEventListener('click', closeNav);

    navSidebar.querySelectorAll('.nav-item').forEach(function (link) {
        link.addEventListener('click', closeNav);
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
