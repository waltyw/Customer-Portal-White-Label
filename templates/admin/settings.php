<?php use App\Core\Security; ?>
<div class="page-header">
    <h1>Portal Settings</h1>
</div>

<form method="POST" action="/admin/settings" enctype="multipart/form-data" id="settingsForm">
    <?= Security::csrfField() ?>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start;">

        <!-- Left: all settings -->
        <div>

            <!-- Branding -->
            <div class="card">
                <div class="card-header"><h2>Branding</h2></div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Portal Name</label>
                            <input type="text" name="app_name" value="<?= Security::e($settings['app_name'] ?? '') ?>" placeholder="My Customer Portal">
                        </div>
                        <div class="form-group">
                            <label>Support Email</label>
                            <input type="email" name="support_email" value="<?= Security::e($settings['support_email'] ?? '') ?>" placeholder="support@yourdomain.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="flex:3;">
                            <label>Logo <span class="hint">(PNG, JPG, SVG or WebP — max 2MB)</span></label>
                            <div style="display:flex;align-items:center;gap:16px;margin-bottom:10px;">
                                <img src="/assets/img/logo.<?= Security::e($settings['logo_ext'] ?? 'png') ?>"
                                     alt="Current logo"
                                     style="height:36px;width:auto;background:#f1f5f9;padding:5px 10px;border-radius:8px;border:1px solid #e2e8f0;">
                                <span style="font-size:12px;color:#94a3b8;">Current logo</span>
                            </div>
                            <input type="file" name="logo" accept=".png,.jpg,.jpeg,.gif,.svg,.webp">
                            <small style="color:#64748b;font-size:12px;">Leave blank to keep existing logo.</small>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Favicon <span class="hint">(PNG, SVG or ICO — max 512KB)</span></label>
                            <?php $favExt = $settings['favicon_ext'] ?? 'png'; ?>
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                                <img src="/assets/img/favicon.<?= Security::e($favExt) ?>?v=<?= time() ?>"
                                     alt="Favicon" style="width:32px;height:32px;object-fit:contain;border:1px solid #e2e8f0;border-radius:4px;padding:2px;"
                                     onerror="this.style.display='none'">
                                <span style="font-size:12px;color:#94a3b8;">Current favicon</span>
                            </div>
                            <input type="file" name="favicon" accept=".png,.ico,.svg">
                        </div>
                    </div>
                    <div class="form-group" style="max-width:200px;">
                        <label>Currency Symbol</label>
                        <input type="text" name="currency_symbol" value="<?= Security::e($settings['currency_symbol'] ?? '£') ?>" maxlength="3" placeholder="£">
                        <small style="color:#64748b;font-size:12px;">Shown on invoices and dashboard (e.g. £ $ €)</small>
                    </div>
                </div>
            </div>

            <!-- Sidebar colours -->
            <div class="card">
                <div class="card-header"><h2>Sidebar Colours</h2></div>
                <div class="card-body">
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                        <?php
                        $sidebarFields = [
                            'sidebar_bg'     => ['Sidebar Background', 'Main sidebar colour'],
                            'sidebar_text'   => ['Nav Item Text',      'Unselected menu items'],
                            'sidebar_active' => ['Active Item',        'Selected menu item'],
                        ];
                        foreach ($sidebarFields as $key => [$label, $hint]):
                            $val = Security::e($settings[$key] ?? '#000000');
                        ?>
                        <div class="form-group">
                            <label><?= $label ?></label>
                            <small style="color:#64748b;font-size:11px;display:block;margin-bottom:6px;"><?= $hint ?></small>
                            <input type="color" id="<?= $key ?>" name="<?= $key ?>" value="<?= $val ?>"
                                   style="width:100%;height:40px;padding:2px 4px;border-radius:6px;cursor:pointer;border:1px solid #e2e8f0;"
                                   oninput="syncHex('<?= $key ?>')">
                            <input type="text" id="<?= $key ?>_hex" value="<?= $val ?>"
                                   maxlength="7" style="margin-top:6px;font-family:monospace;font-size:13px;"
                                   oninput="syncPicker('<?= $key ?>')">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Accent colours -->
            <div class="card">
                <div class="card-header"><h2>Accent Colours</h2></div>
                <div class="card-body">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <?php
                        $accentFields = [
                            'primary_color' => ['Primary Colour',       'Buttons, links, badges'],
                            'primary_dark'  => ['Primary Hover',        'Button hover state'],
                        ];
                        foreach ($accentFields as $key => [$label, $hint]):
                            $val = Security::e($settings[$key] ?? '#2563eb');
                        ?>
                        <div class="form-group">
                            <label><?= $label ?></label>
                            <small style="color:#64748b;font-size:11px;display:block;margin-bottom:6px;"><?= $hint ?></small>
                            <input type="color" id="<?= $key ?>" name="<?= $key ?>" value="<?= $val ?>"
                                   style="width:100%;height:40px;padding:2px 4px;border-radius:6px;cursor:pointer;border:1px solid #e2e8f0;"
                                   oninput="syncHex('<?= $key ?>')">
                            <input type="text" id="<?= $key ?>_hex" value="<?= $val ?>"
                                   maxlength="7" style="margin-top:6px;font-family:monospace;font-size:13px;"
                                   oninput="syncPicker('<?= $key ?>')">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Text & background colours -->
            <div class="card">
                <div class="card-header"><h2>Text &amp; Background Colours</h2></div>
                <div class="card-body">
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:16px;">
                        <?php
                        $textFields = [
                            'text_color' => ['Main Text',         '#1e293b'],
                            'text_muted' => ['Muted Text',        '#64748b'],
                            'body_bg'    => ['Page Background',   '#f8fafc'],
                            'card_bg'    => ['Card Background',   '#ffffff'],
                        ];
                        foreach ($textFields as $key => [$label, $default]):
                            $val = Security::e($settings[$key] ?? $default);
                        ?>
                        <div class="form-group">
                            <label><?= $label ?></label>
                            <input type="color" id="<?= $key ?>" name="<?= $key ?>" value="<?= $val ?>"
                                   style="width:100%;height:40px;padding:2px 4px;border-radius:6px;cursor:pointer;border:1px solid #e2e8f0;"
                                   oninput="syncHex('<?= $key ?>')">
                            <input type="text" id="<?= $key ?>_hex" value="<?= $val ?>"
                                   maxlength="7" style="margin-top:6px;font-family:monospace;font-size:13px;"
                                   oninput="syncPicker('<?= $key ?>')">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Settings</button>
                <button type="button" class="btn btn-outline" onclick="resetDefaults()">Reset Defaults</button>
            </div>
        </div>

        <!-- Right: live preview -->
        <div style="position:sticky;top:32px;">
            <div class="card" style="overflow:hidden;">
                <div class="card-header"><h2>Live Preview</h2></div>

                <!-- Mini sidebar -->
                <div id="prev-sidebar" style="padding:14px;transition:background .2s;">
                    <div style="background:#fff;border-radius:7px;padding:5px 8px;margin-bottom:12px;text-align:center;">
                        <img src="/assets/img/logo.<?= Security::e($settings['logo_ext'] ?? 'png') ?>" style="height:22px;width:auto;">
                    </div>
                    <div id="prev-active" style="display:flex;align-items:center;gap:7px;padding:7px 9px;border-radius:6px;font-size:12px;font-weight:600;color:#fff;margin-bottom:3px;transition:background .2s;">
                        <div style="width:10px;height:10px;background:rgba(255,255,255,.5);border-radius:2px;flex-shrink:0;"></div>
                        Dashboard
                    </div>
                    <div id="prev-inactive" style="display:flex;align-items:center;gap:7px;padding:7px 9px;border-radius:6px;font-size:12px;margin-bottom:3px;transition:color .2s;">
                        <div style="width:10px;height:10px;background:rgba(255,255,255,.2);border-radius:2px;flex-shrink:0;"></div>
                        <span>Tickets</span>
                    </div>
                    <div id="prev-inactive2" style="display:flex;align-items:center;gap:7px;padding:7px 9px;border-radius:6px;font-size:12px;transition:color .2s;">
                        <div style="width:10px;height:10px;background:rgba(255,255,255,.2);border-radius:2px;flex-shrink:0;"></div>
                        <span>Invoices</span>
                    </div>
                </div>

                <!-- Content area -->
                <div id="prev-body" style="padding:14px;transition:background .2s;">
                    <div id="prev-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:12px;margin-bottom:10px;transition:background .2s;">
                        <div id="prev-text" style="font-size:13px;font-weight:600;margin-bottom:4px;transition:color .2s;">Customer Dashboard</div>
                        <div id="prev-muted" style="font-size:11px;transition:color .2s;">Welcome back to your portal</div>
                    </div>
                    <div id="prev-btn" style="display:inline-block;padding:7px 14px;border-radius:6px;font-size:12px;font-weight:600;color:#fff;transition:background .2s;">
                        Pay Invoice
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>

<script>
// Keep color picker and hex text input in sync
function syncHex(key) {
    document.getElementById(key + '_hex').value = document.getElementById(key).value;
    updatePreview();
}

function syncPicker(key) {
    const hex = document.getElementById(key + '_hex').value;
    if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
        document.getElementById(key).value = hex;
        updatePreview();
    }
}

function get(key) {
    return document.getElementById(key).value;
}

function updatePreview() {
    document.getElementById('prev-sidebar').style.background  = get('sidebar_bg');
    document.getElementById('prev-active').style.background   = get('sidebar_active');
    document.getElementById('prev-inactive').style.color      = get('sidebar_text');
    document.getElementById('prev-inactive2').style.color     = get('sidebar_text');
    document.getElementById('prev-body').style.background     = get('body_bg');
    document.getElementById('prev-card').style.background     = get('card_bg');
    document.getElementById('prev-text').style.color          = get('text_color');
    document.getElementById('prev-muted').style.color         = get('text_muted');
    document.getElementById('prev-btn').style.background      = get('primary_color');
}

function resetDefaults() {
    const defaults = {
        sidebar_bg:     '#0f172a',
        sidebar_text:   '#94a3b8',
        sidebar_active: '#2563eb',
        primary_color:  '#2563eb',
        primary_dark:   '#1d4ed8',
        body_bg:        '#f8fafc',
        text_color:     '#1e293b',
        text_muted:     '#64748b',
        card_bg:        '#ffffff',
    };
    Object.entries(defaults).forEach(([key, val]) => {
        const picker = document.getElementById(key);
        const hex    = document.getElementById(key + '_hex');
        if (picker) picker.value = val;
        if (hex)    hex.value    = val;
    });
    updatePreview();
}

// Run once on load to initialise preview
updatePreview();
</script>
