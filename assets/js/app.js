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

// Highlight current nav item
var currentPath = window.location.pathname;
document.querySelectorAll('.nav-item').forEach(function (link) {
    if (link.getAttribute('href') === currentPath) link.classList.add('active');
});

// ── Drawer ───────────────────────────────────────────────────────────────────
(function () {
    var drawer  = document.getElementById('drawer');
    var overlay = document.getElementById('drawerOverlay');
    var openBtn = document.getElementById('menuBtn');
    var closeBtn = document.getElementById('drawerClose');

    if (!drawer || !overlay || !openBtn) return;

    function open() {
        drawer.classList.add('is-open');
        overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        drawer.classList.remove('is-open');
        overlay.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    openBtn.addEventListener('click', open);
    overlay.addEventListener('click', close);
    if (closeBtn) closeBtn.addEventListener('click', close);

    // Close when a nav link is tapped (page will navigate away)
    drawer.querySelectorAll('.nav-item').forEach(function (link) {
        link.addEventListener('click', close);
    });
}());

// ── Tables: wrap for scroll + add landscape hint on small phones ─────────────
document.querySelectorAll('.table').forEach(function (table) {
    if (table.parentElement.classList.contains('table-responsive')) return;

    // Landscape hint (shown via CSS only on very small screens)
    var hint = document.createElement('p');
    hint.className = 'landscape-hint';
    hint.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="5" width="22" height="14" rx="2"/></svg>' +
                     '&nbsp;Rotate device for a better view';

    var wrap = document.createElement('div');
    wrap.className = 'table-responsive';

    table.parentNode.insertBefore(hint, table);
    table.parentNode.insertBefore(wrap, table);
    wrap.appendChild(table);
});
