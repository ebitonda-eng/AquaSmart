<?php
$scriptFile = isset($_SERVER['SCRIPT_FILENAME']) ? realpath($_SERVER['SCRIPT_FILENAME']) : null;
$isStandalone = $scriptFile && $scriptFile === realpath(__FILE__);
$assetPrefix = $isStandalone ? '../' : '';

$backgroundPreview = $assetPrefix . 'assets/images/ui/background.jpg';
$overviewPreview = $assetPrefix . 'assets/images/farm/fam.jpg';

if (!function_exists('settings_badge_class')) {
    function settings_badge_class($tone)
    {
        $classes = [
            'good' => 'status-badge',
            'warning' => 'status-badge badge-warning',
            'danger' => 'status-badge badge-danger',
            'neutral' => 'status-badge badge-neutral',
        ];

        return $classes[$tone] ?? 'status-badge';
    }
}

if (!function_exists('render_settings_styles')) {
    function render_settings_styles()
    {
        echo <<<'CSS'
<style>
    .settings-shell {
        padding-bottom: 20px;
    }

    .settings-header {
        margin-bottom: 2rem;
    }

    .settings-kicker {
        display: inline-block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #0f766e;
        margin-bottom: 10px;
    }

    .settings-overview {
        min-width: 260px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.92), rgba(239, 246, 255, 0.94));
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 18px 40px rgba(148, 163, 184, 0.14);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .settings-overview:hover {
        transform: translateY(-5px);
        box-shadow: 0 22px 46px rgba(148, 163, 184, 0.18);
        border-color: rgba(203, 213, 225, 0.58);
    }

    .overview-label {
        display: block;
        font-size: 12px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 6px;
    }

    .overview-value {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
        color: #0f172a;
    }

    .settings-section {
        margin-top: 0.75rem !important;
    }

    .settings-shell .row.align-items-stretch > [class*="col-"] {
        display: flex;
    }

    .settings-shell .row.align-items-stretch > [class*="col-"] > .card-soft {
        width: 100%;
    }

    .settings-card,
    .save-card {
        position: relative;
        overflow: hidden;
        padding: 24px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 255, 255, 0.82));
        border: 1px solid rgba(255, 255, 255, 0.46);
        box-shadow: 0 16px 36px rgba(148, 163, 184, 0.13);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .settings-card:hover,
    .save-card:hover,
    .toggle-row:hover,
    .preview-frame:hover {
        transform: translateY(-6px);
        box-shadow: 0 22px 46px rgba(148, 163, 184, 0.18);
        border-color: rgba(203, 213, 225, 0.58);
    }

    .panel-copy {
        color: #64748b;
        margin-bottom: 0;
        line-height: 1.55;
    }

    .toggle-stack {
        display: grid;
        gap: 14px;
        margin-top: 18px;
    }

    .toggle-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 18px;
        background: rgba(248, 250, 252, 0.86);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 18px;
        transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease;
    }

    .toggle-copy h6 {
        color: #0f172a;
        font-size: 1rem;
        margin-bottom: 8px;
    }

    .toggle-copy p {
        color: #64748b;
        margin-bottom: 0;
        line-height: 1.5;
    }

    .mode-preview {
        margin-top: 18px;
        padding: 18px;
        background: linear-gradient(135deg, rgba(239, 246, 255, 0.84), rgba(255, 255, 255, 0.9));
        border: 1px solid rgba(191, 219, 254, 0.85);
        border-radius: 18px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .mode-preview span {
        display: block;
        font-size: 12px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 8px;
    }

    .mode-preview strong {
        display: block;
        color: #0f172a;
        font-size: 1.1rem;
        margin-bottom: 8px;
    }

    .mode-preview p {
        margin: 0;
        color: #64748b;
        line-height: 1.5;
    }

    .settings-card .form-control,
    .settings-card .form-select {
        min-height: 46px;
        border-radius: 14px;
        border: 1px solid rgba(203, 213, 225, 0.9);
        background: rgba(255, 255, 255, 0.92);
        box-shadow: none;
        color: #0f172a;
    }

    .settings-card .form-control:focus,
    .settings-card .form-select:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.12);
    }

    .form-label {
        color: #334155;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .form-text {
        color: #64748b;
    }

    .form-check.form-switch {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
        min-height: auto;
        padding-left: 0;
    }

    .form-check.form-switch .form-check-input {
        width: 3rem;
        height: 1.65rem;
        margin-left: 0;
        margin-top: 0;
        border: 0;
        background-color: #cbd5e1;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.12);
        cursor: pointer;
    }

    .form-check.form-switch .form-check-input:checked {
        background-color: #14b8a6;
    }

    .form-check.form-switch .form-check-input:focus {
        box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.14);
    }

    .upload-grid,
    .profile-grid {
        display: grid;
        gap: 18px;
        margin-top: 18px;
    }

    .preview-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
        margin-top: 18px;
    }

    .preview-frame {
        position: relative;
        overflow: hidden;
        min-height: 220px;
        background: rgba(248, 250, 252, 0.86);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 20px;
        transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease;
    }

    .preview-frame img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        display: block;
    }

    .preview-overlay {
        padding: 14px 16px 16px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.86), rgba(255, 255, 255, 0.96));
    }

    .preview-overlay strong {
        display: block;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .preview-overlay p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.45;
    }

    .save-btn {
        min-width: 180px;
        min-height: 48px;
        border: 0;
        border-radius: 999px;
        padding: 10px 22px;
        font-weight: 600;
        letter-spacing: 0.01em;
        background: linear-gradient(135deg, #14b8a6, #2dd4bf);
        color: #ffffff;
        box-shadow: 0 14px 28px rgba(148, 163, 184, 0.18), inset 0 1px 0 rgba(255, 255, 255, 0.25);
        transition: transform 0.24s ease, box-shadow 0.24s ease, filter 0.24s ease;
    }

    .save-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 34px rgba(148, 163, 184, 0.22);
        filter: saturate(1.04);
        color: #ffffff;
    }

    .status-badge {
        border: 1px solid rgba(255, 255, 255, 0.68);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65), 0 8px 18px rgba(148, 163, 184, 0.12);
        transition: transform 0.24s ease, box-shadow 0.24s ease;
    }

    .status-badge.badge-warning {
        background: #fff7ed;
        color: #c2410c;
        border-color: rgba(253, 186, 116, 0.9);
    }

    .status-badge.badge-danger {
        background: #fff1f2;
        color: #be123c;
        border-color: rgba(253, 164, 175, 0.9);
    }

    .status-badge.badge-neutral {
        background: #eff6ff;
        color: #2563eb;
        border-color: rgba(191, 219, 254, 0.9);
    }

    #time {
        color: #64748b !important;
        font-weight: 500;
    }

    @media (max-width: 991.98px) {
        .settings-overview {
            width: 100%;
        }
    }

    @media (max-width: 767.98px) {
        .preview-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575.98px) {
        .settings-card,
        .save-card {
            padding: 20px;
        }

        .overview-value {
            font-size: 1.8rem;
        }

        .toggle-row {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>
CSS;
    }
}
?>
<?php if ($isStandalone): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | AquaSmart Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetPrefix); ?>assets/css/style.css">
    <?php render_settings_styles(); ?>
</head>
<body>
<?php include __DIR__ . '/../components/sidebar.php'; ?>
<?php include __DIR__ . '/../components/navbar.php'; ?>
<div class="main-content">
    <div class="container-fluid settings-shell">
<?php else: ?>
<?php render_settings_styles(); ?>
<?php endif; ?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 settings-header">
    <div>
        <span class="settings-kicker">System Preferences</span>
        <h2 class="mb-2">Dashboard Settings</h2>
        <p class="text-secondary mb-0">Configure appearance, automation behavior, media previews, and profile details for the smart farming dashboard.</p>
    </div>

    <div class="card-soft settings-overview">
        <span class="overview-label">Settings Status</span>
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <div class="overview-value">Synced</div>
                <small class="text-secondary">Preferences are ready to update</small>
            </div>
            <span class="status-badge">Active</span>
        </div>
    </div>
</div>

<div class="row g-4 align-items-stretch">
    <div class="col-lg-6">
        <div class="card-soft settings-card h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <span class="settings-kicker mb-2">Appearance Settings</span>
                    <h5 class="mb-2">Theme Preferences</h5>
                    <p class="panel-copy">Choose how the dashboard interface should appear when the control center is opened.</p>
                </div>

                <span id="modePreviewBadge" class="status-badge">Light</span>
            </div>

            <div class="toggle-stack">
                <div class="toggle-row">
                    <div class="toggle-copy">
                        <h6>Dark Mode</h6>
                        <p>Enable a lower-light interface for late-night monitoring sessions.</p>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="darkModeToggle">
                    </div>
                </div>

                <div class="toggle-row">
                    <div class="toggle-copy">
                        <h6>Light Mode</h6>
                        <p>Keep the dashboard bright and consistent with the current SaaS interface.</p>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="lightModeToggle" checked>
                    </div>
                </div>
            </div>

            <div class="mode-preview">
                <span>Current Mode</span>
                <strong id="modePreviewTitle">Light Mode Active</strong>
                <p id="modePreviewText">The dashboard is currently previewing Light Mode with a crisp, high-clarity layout.</p>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card-soft settings-card h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <span class="settings-kicker mb-2">System Settings</span>
                    <h5 class="mb-2">Automation Controls</h5>
                    <p class="panel-copy">Adjust how alerts and pump operations behave across the smart farming environment.</p>
                </div>

                <span class="status-badge badge-neutral" id="settingsPumpModeBadge">Syncing</span>
            </div>

            <div class="toggle-stack">
                <div class="toggle-row">
                    <div class="toggle-copy">
                        <h6>Enable Alerts</h6>
                        <p>Allow the system to surface warnings and critical events in real time.</p>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="alertsToggle" checked>
                    </div>
                </div>

                <div class="toggle-row">
                    <div class="toggle-copy">
                        <h6>Auto Pump Mode</h6>
                        <p>Automatically manage pump behavior based on sensor logic and scheduled cycles.</p>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="pumpModeToggle" checked>
                    </div>
                </div>
            </div>

            <div class="mode-preview">
                <span>Pump Control Mode</span>
                <strong id="pumpModeTitle">Auto Mode Active</strong>
                <p id="pumpModeText">Manual pump controls are disabled while the ESP32 follows automatic sensor logic.</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 align-items-stretch settings-section">
    <div class="col-lg-6">
        <div class="card-soft settings-card h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <span class="settings-kicker mb-2">Dashboard Customization</span>
                    <h5 class="mb-2">Image Uploads</h5>
                    <p class="panel-copy">Upload visuals for the dashboard background and the farm overview area, then preview them before saving.</p>
                </div>

                <span class="status-badge badge-neutral">Live Preview</span>
            </div>

            <div class="upload-grid">
                <div>
                    <label class="form-label" for="backgroundImageInput">Background Image</label>
                    <input class="form-control" type="file" id="backgroundImageInput" accept="image/*">
                    <div class="form-text">Recommended for wide dashboard backgrounds and branding scenes.</div>
                </div>

                <div>
                    <label class="form-label" for="overviewImageInput">Farm Overview Image</label>
                    <input class="form-control" type="file" id="overviewImageInput" accept="image/*">
                    <div class="form-text">Use a landscape farm or greenhouse image for the overview preview.</div>
                </div>
            </div>

            <div class="preview-grid">
                <div class="preview-frame">
                    <img id="backgroundPreview" src="<?php echo htmlspecialchars($backgroundPreview); ?>" alt="Background preview">
                    <div class="preview-overlay">
                        <strong>Background Preview</strong>
                        <p id="backgroundPreviewLabel">Using the current dashboard background image.</p>
                    </div>
                </div>

                <div class="preview-frame">
                    <img id="overviewPreview" src="<?php echo htmlspecialchars($overviewPreview); ?>" alt="Farm overview preview">
                    <div class="preview-overlay">
                        <strong>Farm Overview Preview</strong>
                        <p id="overviewPreviewLabel">Using the current farm overview image.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card-soft settings-card h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <span class="settings-kicker mb-2">Profile Settings</span>
                    <h5 class="mb-2">System Profile</h5>
                    <p class="panel-copy">Update the name, farm location, and admin details used throughout the dashboard.</p>
                </div>

                <span class="status-badge">Profile Ready</span>
            </div>

            <div class="profile-grid">
                <div>
                    <label class="form-label" for="systemName">System Name</label>
                    <input id="systemName" type="text" class="form-control" value="AquaSmart Control Center">
                </div>

                <div>
                    <label class="form-label" for="farmLocation">Location</label>
                    <input id="farmLocation" type="text" class="form-control" value="Johannesburg Greenhouse Block A">
                </div>

                <div>
                    <label class="form-label" for="adminName">Admin Name</label>
                    <input id="adminName" type="text" class="form-control" value="Farm Operations Admin">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 align-items-stretch settings-section">
    <div class="col-12">
        <div class="card-soft save-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="settings-kicker mb-2">Save Settings</span>
                    <h5 class="mb-2">Apply Dashboard Preferences</h5>
                    <p class="panel-copy">Save the current theme, automation toggles, image choices, and profile details for the system.</p>
                </div>

                <button type="button" class="btn btn-soft save-btn" id="saveSettingsButton">Save Settings</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        function updateClock() {
            var timeElement = document.getElementById('time');
            if (!timeElement) {
                return;
            }

            timeElement.textContent = new Date().toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function syncMode(mode) {
            var darkToggle = document.getElementById('darkModeToggle');
            var lightToggle = document.getElementById('lightModeToggle');
            var previewTitle = document.getElementById('modePreviewTitle');
            var previewText = document.getElementById('modePreviewText');
            var previewBadge = document.getElementById('modePreviewBadge');

            if (!darkToggle || !lightToggle || !previewTitle || !previewText || !previewBadge) {
                return;
            }

            if (mode === 'dark') {
                darkToggle.checked = true;
                lightToggle.checked = false;
                previewTitle.textContent = 'Dark Mode Active';
                previewText.textContent = 'The dashboard is previewing Dark Mode for lower-glare monitoring and calmer night operations.';
                previewBadge.textContent = 'Dark';
                previewBadge.className = 'status-badge badge-neutral';
                return;
            }

            darkToggle.checked = false;
            lightToggle.checked = true;
            previewTitle.textContent = 'Light Mode Active';
            previewText.textContent = 'The dashboard is currently previewing Light Mode with a crisp, high-clarity layout.';
            previewBadge.textContent = 'Light';
            previewBadge.className = 'status-badge';
        }

        function bindModeSwitches() {
            var darkToggle = document.getElementById('darkModeToggle');
            var lightToggle = document.getElementById('lightModeToggle');

            if (!darkToggle || !lightToggle) {
                return;
            }

            darkToggle.addEventListener('change', function () {
                syncMode(darkToggle.checked ? 'dark' : 'light');
            });

            lightToggle.addEventListener('change', function () {
                syncMode(lightToggle.checked ? 'light' : 'dark');
            });

            syncMode(lightToggle.checked ? 'light' : 'dark');
        }

        function setPendingPumpModeState(isPending) {
            var pumpToggle = document.getElementById('pumpModeToggle');
            var badge = document.getElementById('settingsPumpModeBadge');
            var title = document.getElementById('pumpModeTitle');
            var text = document.getElementById('pumpModeText');
            var saveButton = document.getElementById('saveSettingsButton');

            if (!pumpToggle) {
                return;
            }

            if (isPending) {
                pumpToggle.setAttribute('data-pending-mode', 'true');

                if (badge) {
                    badge.textContent = 'Save Required';
                    badge.className = 'status-badge badge-warning';
                }

                if (title) {
                    title.textContent = pumpToggle.checked ? 'Auto Mode Selected' : 'Manual Mode Selected';
                }

                if (text) {
                    text.textContent = pumpToggle.checked
                        ? 'Press Save Settings to store auto mode in the database and lock manual pump control.'
                        : 'Press Save Settings to store manual mode in the database and unlock dashboard pump control.';
                }

                if (saveButton) {
                    saveButton.textContent = 'Save Settings';
                }

                return;
            }

            pumpToggle.removeAttribute('data-pending-mode');
        }

        function bindPumpSettingsSave() {
            var pumpToggle = document.getElementById('pumpModeToggle');
            var saveButton = document.getElementById('saveSettingsButton');

            if (!pumpToggle || !saveButton) {
                return;
            }

            pumpToggle.addEventListener('change', function () {
                setPendingPumpModeState(true);
            });

            saveButton.addEventListener('click', function () {
                if (!window.AquaSmart || typeof window.AquaSmart.setPumpMode !== 'function') {
                    return;
                }

                var targetMode = pumpToggle.checked ? 'auto' : 'manual';
                saveButton.disabled = true;
                saveButton.textContent = 'Saving...';

                window.AquaSmart.setPumpMode(targetMode)
                    .then(function () {
                        setPendingPumpModeState(false);
                        if (window.AquaSmart && typeof window.AquaSmart.fetchPumpData === 'function') {
                            return window.AquaSmart.fetchPumpData();
                        }
                        return null;
                    })
                    .then(function () {
                        saveButton.textContent = 'Saved';
                        window.setTimeout(function () {
                            saveButton.textContent = 'Save Settings';
                        }, 1200);
                    })
                    .catch(function (error) {
                        window.console.error('Failed to save pump mode setting:', error);
                        saveButton.textContent = 'Save Failed';
                        window.setTimeout(function () {
                            saveButton.textContent = 'Save Settings';
                        }, 1600);
                    })
                    .finally(function () {
                        saveButton.disabled = false;
                    });
            });
        }

        function bindImagePreview(inputId, imageId, labelId, fallbackText) {
            var input = document.getElementById(inputId);
            var preview = document.getElementById(imageId);
            var label = document.getElementById(labelId);

            if (!input || !preview || !label) {
                return;
            }

            input.addEventListener('change', function () {
                var file = input.files && input.files[0];

                if (!file) {
                    label.textContent = fallbackText;
                    return;
                }

                var reader = new FileReader();
                reader.onload = function (event) {
                    preview.src = event.target.result;
                    label.textContent = 'Selected file: ' + file.name;
                };
                reader.readAsDataURL(file);
            });
        }

        updateClock();
        setInterval(updateClock, 60000);
        bindModeSwitches();
        bindPumpSettingsSave();
        bindImagePreview('backgroundImageInput', 'backgroundPreview', 'backgroundPreviewLabel', 'Using the current dashboard background image.');
        bindImagePreview('overviewImageInput', 'overviewPreview', 'overviewPreviewLabel', 'Using the current farm overview image.');
    })();
</script>
<script src="<?php echo $isStandalone ? '../assets/js/app.js' : 'assets/js/app.js'; ?>"></script>

<?php if ($isStandalone): ?>
    </div>
</div>
</body>
</html>
<?php endif; ?>
