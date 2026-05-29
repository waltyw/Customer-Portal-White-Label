<?php use App\Core\Security; ?>
<div class="page-header">
    <h1>Portal Settings</h1>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;">

    <!-- Settings form -->
    <form method="POST" action="/admin/settings" enctype="multipart/form-data">
        <?= Security::csrfField() ?>

        <!-- Branding -->
        <div class="card">
            <div class="card-header"><h2>Branding</h2></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Portal Name</label>
                    <input type="text" name="app_name" value="<?= Security::e($settings['app_name'] ?? '') ?>" placeholder="My Customer Portal">
                    <small style="color:#64748b;font-size:12px;">Shown in browser tab and emails.</small>
                </div>
                <div class="form-group">
                    <label>Support Email</label>
                    <input type="email" name="support_email" value="<?= Security::e($settings['support_email'] ?? '') ?>" placeholder="support@yourdomain.com">
                </div>
                <div class="form-group">
                    <label>Logo <span class="hint">(PNG, JPG, SVG or WebP — max 2MB)</span></label>
                    <div style="display:flex;align-items:center;gap:16px;margin-bottom:10px;">
                        <img src="/assets/img/logo.<?= Security::e($settings['logo_ext'] ?? 'png') ?>"
                             alt="Current logo"
                             style="height:40px;width:auto;background:#f1f5f9;padding:6px 10px;border-radius:8px;border:1px solid #e2e8f0;">
                        <span style="font-size:12px;color:#94a3b8;">Current logo</span>
                    </div>
                    <input type="file" name="logo" accept=".png,.jpg,.jpeg,.gif,.svg,.webp">
                    <small style="color:#64748b;font-size:12px;">Leave blank to keep existing logo.</small>
                </div>
            </div>
        </div>

        <!-- Colours -->
        <div class="card">
            <div class="card-header"><h2>Colours</h2></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                    <div class="form-group">
                        <label>Primary Colour</label>
                        <small style="color:#64748b;font-size:12px;display:block;margin-bottom:6px;">Buttons, links, active nav</small>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <input type="color" name="primary_color" value="<?= Security::e($settings['primary_color'] ?? '#2563eb') ?>"
                                   style="width:48px;height:36px;padding:2px;border-radius:6px;cursor:pointer;border:1px solid #e2e8f0;"
                                   oninput="document.getElementById('primary_color_hex').value=this.value;updatePreview()">
                            <input type="text" id="primary_color_hex" value="<?= Security::e($settings['primary_color'] ?? '#2563eb') ?>"
                                   style="width:90px;font-family:monospace;"
                                   oninput="document.querySelector('[name=primary_color]').value=this.value;updatePreview()">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Primary Hover Colour</label>
                        <small style="color:#64748b;font-size:12px;display:block;margin-bottom:6px;">Button hover state</small>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <input type="color" name="primary_dark" value="<?= Security::e($settings['primary_dark'] ?? '#1d4ed8') ?>"
                                   style="width:48px;height:36px;padding:2px;border-radius:6px;cursor:pointer;border:1px solid #e2e8f0;"
                                   oninput="document.getElementById('primary_dark_hex').value=this.value">
                            <input type="text" id="primary_dark_hex" value="<?= Security::e($settings['primary_dark'] ?? '#1d4ed8') ?>"
                                   style="width:90px;font-family:monospace;"
                                   oninput="document.querySelector('[name=primary_dark]').value=this.value">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Sidebar Background</label>
                        <small style="color:#64748b;font-size:12px;display:block;margin-bottom:6px;">Main sidebar colour</small>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <input type="color" name="sidebar_bg" value="<?= Security::e($settings['sidebar_bg'] ?? '#0f172a') ?>"
                                   style="width:48px;height:36px;padding:2px;border-radius:6px;cursor:pointer;border:1px solid #e2e8f0;"
                                   oninput="document.getElementById('sidebar_bg_hex').value=this.value;updatePreview()">
                            <input type="text" id="sidebar_bg_hex" value="<?= Security::e($settings['sidebar_bg'] ?? '#0f172a') ?>"
                                   style="width:90px;font-family:monospace;"
                                   oninput="document.querySelector('[name=sidebar_bg]').value=this.value;updatePreview()">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Sidebar Text Colour</label>
                        <small style="color:#64748b;font-size:12px;display:block;margin-bottom:6px;">Nav item text</small>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <input type="color" name="sidebar_text" value="<?= Security::e($settings['sidebar_text'] ?? '#94a3b8') ?>"
                                   style="width:48px;height:36px;padding:2px;border-radius:6px;cursor:pointer;border:1px solid #e2e8f0;"
                                   oninput="document.getElementById('sidebar_text_hex').value=this.value;updatePreview()">
                            <input type="text" id="sidebar_text_hex" value="<?= Security::e($settings['sidebar_text'] ?? '#94a3b8') ?>"
                                   style="width:90px;font-family:monospace;"
                                   oninput="document.querySelector('[name=sidebar_text]').value=this.value;updatePreview()">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Active Nav Colour</label>
                        <small style="color:#64748b;font-size:12px;display:block;margin-bottom:6px;">Selected menu item</small>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <input type="color" name="sidebar_active" value="<?= Security::e($settings['sidebar_active'] ?? '#2563eb') ?>"
                                   style="width:48px;height:36px;padding:2px;border-radius:6px;cursor:pointer;border:1px solid #e2e8f0;"
                                   oninput="document.getElementById('sidebar_active_hex').value=this.value;updatePreview()">
                            <input type="text" id="sidebar_active_hex" value="<?= Security::e($settings['sidebar_active'] ?? '#2563eb') ?>"
                                   style="width:90px;font-family:monospace;"
                                   oninput="document.querySelector('[name=sidebar_active]').value=this.value;updatePreview()">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Settings</button>
            <a href="/admin" class="btn btn-outline">Cancel</a>
        </div>
    </form>

    <!-- Live preview -->
    <div style="position:sticky;top:32px;">
        <div class="card">
            <div class="card-header"><h2>Live Preview</h2></div>
            <div class="card-body" style="padding:0;overflow:hidden;border-radius:0 0 10px 10px;">
                <div id="preview-sidebar" style="background:#0f172a;padding:16px;">
                    <div style="background:#fff;border-radius:8px;padding:6px 10px;margin-bottom:16px;text-align:center;">
                        <img src="/assets/img/logo.<?= Security::e($settings['logo_ext'] ?? 'png') ?>" alt="Logo" style="height:28px;width:auto;">
                    </div>
                    <div id="preview-nav-item" style="display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:6px;background:#2563eb;color:#fff;font-size:13px;margin-bottom:4px;">
                        <div style="width:14px;height:14px;background:rgba(255,255,255,.4);border-radius:3px;"></div>
                        Dashboard
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:6px;color:#94a3b8;font-size:13px;margin-bottom:4px;" id="preview-nav-text">
                        <div style="width:14px;height:14px;background:rgba(255,255,255,.2);border-radius:3px;"></div>
                        Support Tickets
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:6px;color:#94a3b8;font-size:13px;" id="preview-nav-text2">
                        <div style="width:14px;height:14px;background:rgba(255,255,255,.2);border-radius:3px;"></div>
                        Invoices
                    </div>
                </div>
                <div style="padding:16px;background:#f8fafc;">
                    <div id="preview-btn" style="background:#2563eb;color:#fff;padding:8px 16px;border-radius:6px;font-size:13px;font-weight:600;display:inline-block;">
                        Pay Invoice
                    </div>
                    <div style="margin-top:12px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:12px;font-size:12px;color:#64748b;">
                        Invoice #BBZ-0001 — £99.00
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function updatePreview() {
    const bg     = document.querySelector('[name="sidebar_bg"]').value;
    const text   = document.querySelector('[name="sidebar_text"]').value;
    const active = document.querySelector('[name="sidebar_active"]').value;
    const primary= document.querySelector('[name="primary_color"]').value;

    document.getElementById('preview-sidebar').style.background = bg;
    document.getElementById('preview-nav-item').style.background = active;
    document.getElementById('preview-nav-text').style.color  = text;
    document.getElementById('preview-nav-text2').style.color = text;
    document.getElementById('preview-btn').style.background  = primary;
}
</script>
