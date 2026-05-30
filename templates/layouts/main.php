<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\Security::e($title ?? 'Portal') ?> — <?= \App\Core\Security::e(\App\Models\Setting::get('app_name')) ?></title>
    <?php $favExt = \App\Models\Setting::get('favicon_ext') ?: 'png'; ?>
    <link rel="icon" href="/assets/img/favicon.<?= $favExt ?>" type="<?= $favExt === 'svg' ? 'image/svg+xml' : ($favExt === 'ico' ? 'image/x-icon' : 'image/png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <?php
    $fontUrl = \App\Models\Setting::googleFontUrl();
    $activeFont = \App\Models\Setting::get('font_family') ?: 'Inter';
    ?>
    <link href="https://fonts.googleapis.com/css2?family=<?= urlencode($activeFont) ?>:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
    <?= \App\Models\Setting::cssVars() ?>
</head>
<body>
<?php
$currentPath = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$user = \App\Auth\Auth::user();
?>
<div class="layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <?php
            $logoExt  = \App\Models\Setting::get('logo_ext') ?: 'png';
            $logoFile = $_SERVER['DOCUMENT_ROOT'] . '/assets/img/logo.' . $logoExt;
            $appName  = \App\Models\Setting::get('app_name') ?: 'Portal';
            ?>
            <?php if (file_exists($logoFile)): ?>
            <a href="/dashboard"><img src="/assets/img/logo.<?= $logoExt ?>?v=<?= filemtime($logoFile) ?>" alt="<?= \App\Core\Security::e($appName) ?>" class="sidebar-logo-img"></a>
            <?php else: ?>
            <a href="/dashboard" style="color:#fff;font-size:16px;font-weight:700;text-decoration:none;padding:8px 0;"><?= \App\Core\Security::e($appName) ?></a>
            <?php endif; ?>
        </div>
        <nav class="sidebar-nav">
            <a href="/dashboard" class="nav-item <?= $currentPath === '/dashboard' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="/tickets" class="nav-item <?= str_starts_with($currentPath, '/tickets') ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Support Tickets
            </a>
            <a href="/invoices" class="nav-item <?= str_starts_with($currentPath, '/invoices') || str_starts_with($currentPath, '/payment') ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Invoices &amp; Payments
            </a>
            <a href="/help" class="nav-item <?= str_starts_with($currentPath, '/help') ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Help &amp; Email Setup
            </a>
            <a href="/account" class="nav-item <?= str_starts_with($currentPath, '/account') ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                My Account
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></div>
                <div>
                    <div class="user-name"><?= \App\Core\Security::e($user['name'] ?? '') ?></div>
                    <div class="user-company"><?= \App\Core\Security::e($user['company'] ?? '') ?></div>
                </div>
            </div>
            <a href="/logout" class="logout-btn" title="Sign out">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </a>
        </div>
    </aside>

    <!-- Main content -->
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
