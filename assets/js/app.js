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

// ── Mobile side drawer ───────────────────────────────────────────────────────
// Only runs when viewport is narrow — on desktop this block never executes,
// so there is no hamburger button or overlay in the DOM at all.
if (window.innerWidth <= 1024) {
    var sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        // Clone the sidebar logo so the mobile bar shows the same branding
        var sidebarLogoEl = sidebar.querySelector('.sidebar-logo-img');
        var sidebarLogoText = sidebar.querySelector('.sidebar-brand a');
        var logoHtml = sidebarLogoEl
            ? '<img src="' + sidebarLogoEl.src + '" alt="' + sidebarLogoEl.alt + '" class="mobile-logo-img">'
            : '<span class="mobile-logo-text">' + (sidebarLogoText ? sidebarLogoText.textContent.trim() : '') + '</span>';

        // Build and inject mobile top bar before everything else in <body>
        var topBar = document.createElement('header');
        topBar.className = 'mobile-header';
        topBar.innerHTML =
            '<button class="nav-toggle" aria-label="Open menu" id="navToggle">' +
            '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">' +
            '<line x1="3" y1="6" x2="21" y2="6"/>' +
            '<line x1="3" y1="12" x2="21" y2="12"/>' +
            '<line x1="3" y1="18" x2="21" y2="18"/>' +
            '</svg></button>' +
            '<div class="mobile-brand">' + logoHtml + '</div>';
        document.body.insertBefore(topBar, document.body.firstChild);

        // Build and append overlay backdrop
        var overlay = document.createElement('div');
        overlay.className = 'nav-overlay';
        document.body.appendChild(overlay);

        // Make sidebar an off-canvas drawer
        sidebar.classList.add('is-drawer');

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

        document.getElementById('navToggle').addEventListener('click', openDrawer);
        overlay.addEventListener('click', closeDrawer);

        sidebar.querySelectorAll('.nav-item').forEach(function (link) {
            link.addEventListener('click', closeDrawer);
        });
    }
}

// Auto-wrap tables for horizontal scroll on mobile
document.querySelectorAll('.table').forEach(function (table) {
    if (!table.parentElement.classList.contains('table-responsive')) {
        var wrap = document.createElement('div');
        wrap.className = 'table-responsive';
        table.parentNode.insertBefore(wrap, table);
        wrap.appendChild(table);
    }
});
