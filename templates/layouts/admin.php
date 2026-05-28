<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\Security::e($title ?? 'Admin') ?> — Admin — <?= \App\Core\Security::e($_ENV['APP_NAME'] ?? 'Portal') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<?php
$currentPath = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$user = \App\Auth\Auth::user();
?>
<div class="layout">
    <aside class="sidebar sidebar-admin">
        <div class="sidebar-brand">
            <a href="/admin"><img src="/assets/img/logo.png" alt="Beebizzi" class="sidebar-logo-img"></a>
        </div>
        <nav class="sidebar-nav">
            <a href="/admin" class="nav-item <?= $currentPath === '/admin' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="/admin/customers" class="nav-item <?= str_starts_with($currentPath, '/admin/customers') ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Customers
            </a>
            <a href="/admin/tickets" class="nav-item <?= str_starts_with($currentPath, '/admin/tickets') ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Support Tickets
            </a>
            <a href="/admin/invoices" class="nav-item <?= str_starts_with($currentPath, '/admin/invoices') ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Invoices
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar" style="background:#7c3aed;"><?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?></div>
                <div>
                    <div class="user-name"><?= \App\Core\Security::e($user['name'] ?? '') ?></div>
                    <div class="user-company" style="color:#f59e0b;">Administrator</div>
                </div>
            </div>
            <a href="/logout" class="logout-btn" title="Sign out">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <div class="content-inner">
            <?php if (isset($flash)): ?>
            <div class="alert alert-<?= \App\Core\Security::e($flash['type']) ?>">
                <?= \App\Core\Security::e($flash['message']) ?>
                <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
            <?php endif; ?>
            <?= $content ?>
        </div>
    </main>
</div>

<script src="/assets/js/app.js"></script>
</body>
</html>
