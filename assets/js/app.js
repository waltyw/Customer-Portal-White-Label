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

// Mobile sidebar toggle
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
const toggle  = document.getElementById('sidebarToggle');

if (sidebar && overlay && toggle) {
    const openSidebar  = () => { sidebar.classList.add('is-open'); overlay.classList.add('is-open'); };
    const closeSidebar = () => { sidebar.classList.remove('is-open'); overlay.classList.remove('is-open'); };

    toggle.addEventListener('click', openSidebar);
    overlay.addEventListener('click', closeSidebar);

    sidebar.querySelectorAll('.nav-item').forEach(link => {
        link.addEventListener('click', closeSidebar);
    });
}

// Auto-wrap tables for horizontal scroll on mobile
document.querySelectorAll('.table').forEach(table => {
    if (!table.parentElement.classList.contains('table-responsive')) {
        const wrap = document.createElement('div');
        wrap.className = 'table-responsive';
        table.parentNode.insertBefore(wrap, table);
        wrap.appendChild(table);
    }
});
