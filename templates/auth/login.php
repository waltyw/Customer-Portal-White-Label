<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= \App\Core\Security::e($_ENV['APP_NAME'] ?? 'Customer Portal') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="auth-body">
<?php $flash = \App\Core\Security::getFlash(); ?>
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <?php
            $logoExt  = \App\Models\Setting::get('logo_ext') ?: 'png';
            $logoFile = $_SERVER['DOCUMENT_ROOT'] . '/assets/img/logo.' . $logoExt;
            $appName  = \App\Models\Setting::get('app_name') ?: 'Customer Portal';
            ?>
            <?php if (file_exists($logoFile)): ?>
            <img src="/assets/img/logo.<?= $logoExt ?>?v=<?= filemtime($logoFile) ?>" alt="<?= \App\Core\Security::e($appName) ?>" class="auth-logo-img">
            <?php else: ?>
            <div class="auth-logo"><?= \App\Core\Security::e(mb_substr($appName, 0, 1)) ?></div>
            <h1><?= \App\Core\Security::e($appName) ?></h1>
            <?php endif; ?>
            <p>Sign in to your account</p>
        </div>

        <?php if ($flash): ?>
        <div class="alert alert-<?= \App\Core\Security::e($flash['type']) ?>" style="margin-bottom:20px;">
            <?= \App\Core\Security::e($flash['message']) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/login" class="auth-form">
            <?= \App\Core\Security::csrfField() ?>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required autofocus
                    value="<?= \App\Core\Security::e($_POST['email'] ?? '') ?>"
                    placeholder="you@example.com">
            </div>
            <div class="form-group">
                <label for="password">
                    Password
                    <a href="/forgot-password" class="label-link">Forgot password?</a>
                </label>
                <div style="position:relative;">
                    <input type="password" id="password" name="password" required placeholder="••••••••••" style="padding-right:44px;">
                    <button type="button" onclick="togglePassword()" id="toggle-pw"
                        style="position:absolute;right:0;top:0;bottom:0;width:40px;background:none;border:none;cursor:pointer;color:#94a3b8;display:flex;align-items:center;justify-content:center;"
                        title="Show password" aria-label="Show password">
                        <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg id="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        </form>
    </div>
    <p class="auth-footer">
        Need help? <a href="mailto:<?= \App\Core\Security::e($_ENV['SMTP_FROM_EMAIL'] ?? '') ?>">Contact support</a>
    </p>
</div>
<script>
function togglePassword() {
    var input = document.getElementById('password');
    var eyeOn  = document.getElementById('icon-eye');
    var eyeOff = document.getElementById('icon-eye-off');
    var btn    = document.getElementById('toggle-pw');
    if (input.type === 'password') {
        input.type = 'text';
        eyeOn.style.display  = 'none';
        eyeOff.style.display = '';
        btn.title = btn.setAttribute('aria-label', 'Hide password') || 'Hide password';
    } else {
        input.type = 'password';
        eyeOn.style.display  = '';
        eyeOff.style.display = 'none';
        btn.title = btn.setAttribute('aria-label', 'Show password') || 'Show password';
    }
}
</script>
</body>
</html>

