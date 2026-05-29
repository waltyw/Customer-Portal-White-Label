<?php use App\Core\Security; ?>
<div class="page-header">
    <h1>My Account</h1>
</div>

<div style="max-width:640px;">
    <div class="card">
        <div class="card-header"><h2>Your Details</h2></div>
        <div class="card-body">
            <form method="POST" action="/account">
                <?= Security::csrfField() ?>

                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name <span class="required">*</span></label>
                        <input type="text" name="name" required value="<?= Security::e($user['name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Company</label>
                        <input type="text" name="company" value="<?= Security::e($user['company'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" value="<?= Security::e($user['phone'] ?? '') ?>" placeholder="07700 000000">
                    </div>
                    <div class="form-group">
                        <label>Website URL</label>
                        <input type="text" name="website_url" value="<?= Security::e($user['website_url'] ?? '') ?>" placeholder="https://yourdomain.co.uk">
                    </div>
                </div>

                <?php if (!empty($user['website_url'])): ?>
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#166534;">
                    <strong>Your mail server:</strong> <?= Security::e(\App\Models\User::mailServer($user['website_url'])) ?>
                    — this is used to pre-fill your <a href="/help" style="color:#166534;">email setup guides</a>.
                </div>
                <?php endif; ?>

                <div class="form-group" style="margin-bottom:0;">
                    <label>Email Address</label>
                    <input type="email" value="<?= Security::e($user['email'] ?? '') ?>" disabled style="background:#f8fafc;color:#94a3b8;cursor:not-allowed;">
                    <small style="color:#64748b;font-size:12px;">To change your email address please <a href="/tickets/create">raise a support ticket</a>.</small>
                </div>

                <div class="form-actions" style="margin-top:20px;">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2>Change Password</h2></div>
        <div class="card-body">
            <p style="color:#64748b;margin-bottom:16px;">Use the password reset flow to set a new password — a reset link will be emailed to you.</p>
            <a href="/forgot-password" class="btn btn-outline">Send Password Reset Email</a>
        </div>
    </div>
</div>
