<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AquaSmart Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .dashboard-shell {
        padding-bottom: 20px;
    }

    .dashboard-top-card,
    .dashboard-panel,
    .hero-card,
    .compact-card,
    .water-panel,
    .pump-panel {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.46);
        box-shadow: 0 16px 36px rgba(148, 163, 184, 0.13);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .dashboard-top-card:hover,
    .dashboard-panel:hover,
    .hero-card:hover,
    .compact-card:hover,
    .water-panel:hover,
    .pump-panel:hover,
    .dashboard-alert:hover {
        transform: translateY(-6px);
        box-shadow: 0 22px 46px rgba(148, 163, 184, 0.18);
        border-color: rgba(203, 213, 225, 0.58);
    }

    .dashboard-top-card {
        min-height: 168px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        isolation: isolate;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 255, 255, 0.84));
    }

    .dashboard-top-card::before {
        content: "";
        position: absolute;
        inset: auto -30px -36px auto;
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: radial-gradient(circle, var(--card-soft), rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
    }

    .dashboard-top-card::after {
        content: "";
        position: absolute;
        top: 0;
        left: 24px;
        right: 24px;
        height: 3px;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--card-accent), rgba(255, 255, 255, 0));
        opacity: 0.9;
    }

    .card-green.dashboard-top-card {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.9), rgba(74, 222, 128, 0.86));
        border-color: rgba(110, 231, 183, 0.4);
        box-shadow: 0 18px 38px rgba(34, 197, 94, 0.2);
    }

    .metric-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 16px;
    }

    .metric-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--card-soft), rgba(255, 255, 255, 0.98));
        color: var(--card-accent);
        font-size: 18px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7), 0 12px 28px rgba(148, 163, 184, 0.14);
    }

    .card-green .metric-chip {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .metric-label {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 12px;
    }

    .dashboard-top-card.card-green .metric-label,
    .dashboard-top-card.card-green .metric-note {
        color: rgba(255, 255, 255, 0.82);
    }

    .metric-value {
        font-size: 2.3rem;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -0.04em;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .dashboard-top-card.card-green .metric-value,
    .dashboard-top-card.card-green .metric-sub {
        color: #ffffff;
    }

    .metric-sub {
        color: #475569;
        font-size: 0.98rem;
        font-weight: 600;
        margin-bottom: 0;
    }

    .metric-note {
        color: #64748b;
        margin: 0;
        line-height: 1.5;
    }

    .section-gap {
        margin-top: 0.75rem !important;
    }

    .compact-stack {
        display: grid;
        gap: 14px;
    }

    .compact-card {
        padding: 18px 20px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 255, 255, 0.84));
    }

    .compact-card h6 {
        font-size: 12px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 8px;
    }

    .compact-value {
        display: block;
        font-size: 1.35rem;
        font-weight: 700;
        line-height: 1.1;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .compact-card p {
        margin: 0;
        color: #64748b;
        line-height: 1.45;
    }

    .hero-card {
        padding: 0;
        min-height: 320px;
        background: transparent;
        border-color: rgba(255, 255, 255, 0.42);
    }

    .hero-card img {
        width: 100%;
        height: 100%;
        min-height: 320px;
        object-fit: cover;
        border-radius: 20px;
        display: block;
    }

    .hero-overlay {
        position: absolute;
        left: 24px;
        bottom: 24px;
        max-width: 300px;
        padding: 18px 20px;
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.48), rgba(30, 41, 59, 0.28));
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        color: #ffffff;
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.2);
    }

    .hero-overlay span {
        display: inline-block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(226, 232, 240, 0.8);
        margin-bottom: 8px;
    }

    .hero-overlay h5 {
        margin-bottom: 8px;
        color: #ffffff;
    }

    .hero-overlay p {
        margin: 0;
        color: rgba(226, 232, 240, 0.9);
    }

    .farm-widget {
        position: absolute;
        min-width: 145px;
        padding: 12px 14px;
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.56));
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.45);
        box-shadow: 0 16px 28px rgba(15, 23, 42, 0.12);
    }

    .farm-widget span {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 6px;
    }

    .farm-widget strong {
        display: block;
        font-size: 1.15rem;
        color: #0f172a;
    }

    .widget-temp {
        top: 24px;
        right: 26px;
    }

    .widget-humidity {
        top: 112px;
        right: 120px;
    }

    .widget-ph {
        bottom: 24px;
        right: 28px;
    }

    .dashboard-panel,
    .water-panel,
    .pump-panel {
        padding: 24px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 255, 255, 0.84));
    }

    .section-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 18px;
    }

    .section-head h5 {
        margin-bottom: 8px;
        color: #0f172a;
    }

    .section-head p {
        margin: 0;
        color: #64748b;
        line-height: 1.5;
    }

    .section-kicker {
        display: inline-block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #0f766e;
        margin-bottom: 8px;
    }

    .section-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(239, 246, 255, 0.9);
        border: 1px solid rgba(191, 219, 254, 0.8);
        color: #2563eb;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .chart-stage {
        position: relative;
        height: 340px;
        padding: 12px 6px 0;
    }

    .legend-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 4px;
    }

    .legend-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(248, 250, 252, 0.92);
        border: 1px solid rgba(148, 163, 184, 0.14);
        color: #475569;
        font-size: 13px;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #22c55e;
    }

    .alerts-stack {
        display: grid;
        gap: 14px;
    }

    .dashboard-alert {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 18px;
        border-radius: 18px;
        background: rgba(248, 250, 252, 0.92);
        border: 1px solid rgba(148, 163, 184, 0.12);
        transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease;
    }

    .alert-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #ffffff;
        flex-shrink: 0;
    }

    .alert-icon.critical {
        background: linear-gradient(135deg, #ef4444, #fb7185);
    }

    .alert-icon.warning {
        background: linear-gradient(135deg, #f59e0b, #f97316);
    }

    .dashboard-alert strong {
        display: block;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .dashboard-alert p {
        margin: 0;
        color: #64748b;
        line-height: 1.45;
    }

    .status-badge {
        border: 1px solid rgba(255, 255, 255, 0.68);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65), 0 8px 18px rgba(148, 163, 184, 0.12);
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

    .water-panel .progress {
        height: 14px;
        margin-top: 18px;
        background: #e2e8f0;
    }

    .water-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: 14px;
        color: #64748b;
        font-size: 14px;
    }

    .pump-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 18px;
    }

    .pump-mode-banner {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: #fff1f2;
        color: #be123c;
        border: 1px solid rgba(251, 113, 133, 0.26);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .pump-mode-banner.is-manual {
        background: #ecfeff;
        color: #0f766e;
        border-color: rgba(45, 212, 191, 0.32);
    }

    .pump-device-status {
        width: 100%;
        color: #64748b;
        font-size: 14px;
        font-weight: 600;
    }

    .pump-btn {
        min-width: 132px;
        border: 0;
        border-radius: 999px;
        padding: 10px 18px;
        font-weight: 600;
        letter-spacing: 0.01em;
        box-shadow: 0 14px 28px rgba(148, 163, 184, 0.18), inset 0 1px 0 rgba(255, 255, 255, 0.25);
        transition: transform 0.24s ease, box-shadow 0.24s ease, filter 0.24s ease;
    }

    .pump-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 34px rgba(148, 163, 184, 0.22);
        filter: saturate(1.04);
    }

    .pump-btn.is-off {
        background: linear-gradient(135deg, #64748b, #94a3b8);
        color: #ffffff;
    }

    .pump-btn.is-on {
        background: linear-gradient(135deg, #16a34a, #4ade80);
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12), 0 18px 34px rgba(34, 197, 94, 0.22);
    }

    .pump-btn.is-danger {
        background: linear-gradient(135deg, #ef4444, #fb7185);
        color: #ffffff;
    }

    .pump-btn:disabled {
        transform: none;
        box-shadow: none;
        opacity: 0.6;
        cursor: not-allowed;
        filter: none;
    }

    #time {
        color: #64748b !important;
        font-weight: 500;
    }

    @media (max-width: 991.98px) {
        .hero-card img {
            min-height: 300px;
        }

        .widget-humidity {
            right: 24px;
            top: 102px;
        }
    }

    @media (max-width: 767.98px) {
        .dashboard-top-card,
        .dashboard-panel,
        .hero-card,
        .compact-card,
        .water-panel,
        .pump-panel {
            padding: 20px;
        }

        .hero-card {
            padding: 0;
        }

        .hero-overlay {
            left: 18px;
            right: 18px;
            bottom: 18px;
            max-width: none;
        }

        .farm-widget {
            min-width: 126px;
            padding: 10px 12px;
        }

        .widget-temp {
            top: 18px;
            right: 18px;
        }

        .widget-humidity {
            top: 92px;
            right: 18px;
        }

        .widget-ph {
            bottom: 132px;
            right: 18px;
        }

        .chart-stage {
            height: 300px;
        }
    }

    @media (max-width: 575.98px) {
        .metric-value {
            font-size: 2rem;
        }

        .pump-actions {
            flex-direction: column;
        }

        .pump-btn {
            width: 100%;
        }
    }
</style>
</head>

<body>

<?php include("components/sidebar.php"); ?>
<?php include("components/navbar.php"); ?>

<div class="main-content">
<div class="container-fluid dashboard-shell">

<!-- TOP CARDS -->
<div class="row g-4 align-items-stretch">

    <div class="col-sm-6 col-xl-2">
        <div class="card-soft card-green dashboard-top-card" style="--card-accent:#ffffff; --card-soft:rgba(255,255,255,0.22);">
            <div class="metric-head">
                <div class="metric-chip">PH</div>
                <span class="status-badge">Healthy</span>
            </div>
            <div>
                <div class="metric-label">Plant Health</div>
                <div class="metric-value" id="system_health_score">--</div>
                <p class="metric-sub">Excellent</p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card-soft dashboard-top-card" style="--card-accent:#0ea5e9; --card-soft:rgba(14,165,233,0.14);">
            <div class="metric-head">
                <div class="metric-chip">T</div>
                <span class="section-pill">Live</span>
            </div>
            <div>
                <div class="metric-label">Temperature</div>
                <div class="metric-value" id="temp">24&deg;C</div>
                <p class="metric-note">Stable greenhouse climate across the active grow beds.</p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card-soft dashboard-top-card" style="--card-accent:#2563eb; --card-soft:rgba(37,99,235,0.14);">
            <div class="metric-head">
                <div class="metric-chip">WT</div>
                <span class="status-badge">Safe</span>
            </div>
            <div>
                <div class="metric-label">Water Temperature</div>
                <div class="metric-value" id="water_temp">--</div>
                <p class="metric-note">Tank temperature is steady and suitable for fish and root health.</p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card-soft dashboard-top-card" style="--card-accent:#14b8a6; --card-soft:rgba(20,184,166,0.14);">
            <div class="metric-head">
                <div class="metric-chip">H</div>
                <span class="section-pill">Comfort</span>
            </div>
            <div>
                <div class="metric-label">Humidity</div>
                <div class="metric-value" id="humidity">65%</div>
                <p class="metric-note">Moisture levels remain supportive for healthy plant growth.</p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card-soft dashboard-top-card" style="--card-accent:#8b5cf6; --card-soft:rgba(139,92,246,0.14);">
            <div class="metric-head">
                <div class="metric-chip">pH</div>
                <span class="section-pill">Balanced</span>
            </div>
            <div>
                <div class="metric-label">pH Level</div>
                <div class="metric-value" id="ph">6.9</div>
                <p class="metric-note">Nutrient solution is sitting comfortably in the preferred zone.</p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card-soft dashboard-top-card" style="--card-accent:#f97316; --card-soft:rgba(249,115,22,0.15);">
            <div class="metric-head">
                <div class="metric-chip">NH3</div>
                <span class="status-badge badge-warning">Warning</span>
            </div>
            <div>
                <div class="metric-label">Ammonia Level</div>
                <div class="metric-value" id="ammonia">--</div>
                <p class="metric-note">Ammonia needs closer observation before it reaches the critical zone.</p>
            </div>
        </div>
    </div>

</div>

<div class="row section-gap g-4 align-items-stretch">

    <!-- LEFT SIDE (TASK / INFO CARDS) -->
    <div class="col-md-4">
        <div class="compact-stack">
            <div class="card-soft compact-card">
                <h6>System Status</h6>
                <span class="compact-value" data-field="system_status">--</span>
                <p data-field="timestamp_status">Waiting for live data</p>
            </div>

            <div class="card-soft compact-card">
                <h6>Water Usage</h6>
                <span class="compact-value" id="water_level">--</span>
                <p>Current monitored usage for the active irrigation window.</p>
            </div>

            <div class="card-soft compact-card">
                <h6>Active Pumps</h6>
                <span class="compact-value" data-field="active_alerts_count">--</span>
                <p>One circulation unit is online and maintaining steady flow.</p>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE (BIG VISUAL CARD) -->
    <div class="col-md-8">
        <div class="card-soft hero-card big-visual">

            <img src="assets/images/farm/fam.jpg" alt="farm">

            <!-- MAIN OVERLAY -->
            <div class="hero-overlay">
                <span>Farm Overview</span>
                <h5>Farm Overview</h5>
                <p>Monitoring in real-time with a quick snapshot of the core environmental metrics.</p>
            </div>

            <!-- FLOATING WIDGETS -->
            <div class="farm-widget widget-temp">
                <span>Temperature</span>
                <strong id="farmWidgetTemp">--</strong>
            </div>

            <div class="farm-widget widget-humidity">
                <span>Humidity</span>
                <strong id="farmWidgetHumidity">--</strong>
            </div>

            <div class="farm-widget widget-ph">
                <span>pH Level</span>
                <strong id="farmWidgetPh">--</strong>
            </div>

        </div>
    </div>

</div>

<!-- SECOND ROW -->
<div class="row section-gap g-4 align-items-stretch">

    <!-- CHART -->
    <div class="col-md-8">
        <div class="card-soft dashboard-panel h-100">
            <div class="section-head">
                <div>
                    <span class="section-kicker">System Trends</span>
                    <h5>System Trends</h5>
                    <p>A cleaner view of temperature movement across the last seven recorded intervals.</p>
                </div>
                <div class="legend-row">
                    <span class="legend-pill"><span class="legend-dot"></span>Temperature</span>
                </div>
            </div>
            <div class="chart-stage">
                <canvas id="chart"></canvas>
            </div>
        </div>
    </div>

    <!-- ALERTS -->
    <div class="col-md-4">
        <div class="card-soft dashboard-panel h-100">
            <div class="section-head">
                <div>
                    <span class="section-kicker">Alerts</span>
                    <h5>Alerts</h5>
                    <p>Important issues surfaced with severity labels for faster response.</p>
                </div>
                <span class="section-pill" id="dashboardAlertCount">0 Active</span>
            </div>

            <div class="alerts-stack" id="dashboardAlerts">
                <div class="dashboard-alert">
                    <div class="alert-icon warning">!</div>
                    <div>
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <strong>Waiting for Alerts</strong>
                            <span class="status-badge">Loading</span>
                        </div>
                        <p>Live alerts from the API will appear here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- THIRD ROW -->
<div class="row section-gap g-4 align-items-stretch">

    <div class="col-md-6">
        <div class="card-soft water-panel h-100">
            <div class="section-head">
                <div>
                    <span class="section-kicker">Water Level</span>
                    <h5>Water Level</h5>
                    <p>Reservoir status for the active farming cycle.</p>
                </div>
                <span class="section-pill" data-water-level-badge>--</span>
            </div>
            <div class="progress">
                <div id="waterBar" class="progress-bar bg-success" style="width:0%" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
            </div>
            <div class="water-meta">
                <span>Current capacity</span>
                <strong data-water-level-text>--</strong>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-soft pump-panel h-100">
            <div class="section-head">
                <div>
                    <span class="section-kicker">Pump Control</span>
                    <h5>Pump Control</h5>
                    <p>Quick actions for heater, fan, irrigation, refill, and filtration tasks.</p>
                </div>
                <span class="pump-mode-banner" id="dashboardPumpModeIndicator">AUTO MODE ACTIVE</span>
            </div>

            <div class="pump-device-status" id="dashboardPumpStatusText">Waiting for live pump state</div>
            <div class="pump-actions">
                <button class="btn btn-soft pump-btn is-off" data-pump-toggle="heater">Heater</button>
                <button class="btn btn-soft pump-btn is-off" data-pump-toggle="fan">Fan</button>
                <button class="btn btn-soft pump-btn is-off" data-pump-toggle="irrigation">Irrigation</button>
                <button class="btn btn-soft pump-btn is-off" data-pump-toggle="refill">Refill</button>
                <button class="btn btn-soft pump-btn is-off" data-pump-toggle="filter">Filtration</button>
            </div>

        </div>
    </div>

</div>

</div>
</div>

<script src="assets/js/app.js"></script>
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

        function initChart() {
            var chartCanvas = document.getElementById('chart');
            if (!chartCanvas || typeof window.Chart === 'undefined') {
                return;
            }

            var existingChart = window.Chart.getChart(chartCanvas);
            if (existingChart) {
                existingChart.destroy();
            }

            var context = chartCanvas.getContext('2d');
            var gradient = context.createLinearGradient(0, 0, 0, 340);
            gradient.addColorStop(0, 'rgba(34, 197, 94, 0.22)');
            gradient.addColorStop(1, 'rgba(34, 197, 94, 0.02)');

            new window.Chart(chartCanvas, {
                type: 'line',
                data: {
                    labels: ['06:00', '08:00', '10:00', '12:00', '14:00', '16:00', '18:00'],
                    datasets: [{
                        label: 'Temperature',
                        data: [22.8, 23.4, 24.1, 24.6, 24.9, 24.5, 24.0],
                        borderColor: '#22c55e',
                        backgroundColor: gradient,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        pointHoverBackgroundColor: '#22c55e',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2,
                        cubicInterpolationMode: 'monotone',
                        tension: 0.42
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#ffffff',
                            bodyColor: '#e2e8f0',
                            padding: 12,
                            cornerRadius: 14,
                            borderColor: 'rgba(255, 255, 255, 0.08)',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            border: {
                                display: false
                            },
                            ticks: {
                                color: '#64748b',
                                padding: 10
                            }
                        },
                        y: {
                            min: 20,
                            max: 26,
                            grid: {
                                color: 'rgba(148, 163, 184, 0.08)',
                                drawBorder: false,
                                drawTicks: false
                            },
                            border: {
                                display: false
                            },
                            ticks: {
                                color: '#64748b',
                                maxTicksLimit: 5,
                                padding: 12,
                                callback: function (value) {
                                    return value + ' C';
                                }
                            }
                        }
                    }
                }
            });
        }

        updateClock();
        setInterval(updateClock, 60000);
        initChart();
    })();
</script>

</body>
</html>
