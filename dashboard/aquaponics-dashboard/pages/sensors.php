<?php
$scriptFile = isset($_SERVER['SCRIPT_FILENAME']) ? realpath($_SERVER['SCRIPT_FILENAME']) : null;
$isStandalone = $scriptFile && $scriptFile === realpath(__FILE__);

$sensorCards = [
    [
        'name' => 'Temperature',
        'value' => '--',
        'unit' => 'C',
        'status' => 'Live',
        'tone' => 'good',
        'detail' => 'Live reading from the backend API.',
        'trend' => 'Streaming from the latest sensor sample',
        'updated' => '--',
        'accent' => '#0ea5e9',
        'soft' => 'rgba(14, 165, 233, 0.16)',
        'icon' => 'temperature',
    ],
    [
        'name' => 'Water Temperature',
        'value' => '--',
        'unit' => 'C',
        'status' => 'Live',
        'tone' => 'good',
        'detail' => 'Live reading from the backend API.',
        'trend' => 'Streaming from the latest sensor sample',
        'updated' => '--',
        'accent' => '#2563eb',
        'soft' => 'rgba(37, 99, 235, 0.16)',
        'icon' => 'water_temperature',
    ],
    [
        'name' => 'Humidity',
        'value' => '--',
        'unit' => '%',
        'status' => 'Live',
        'tone' => 'warning',
        'detail' => 'Live reading from the backend API.',
        'trend' => 'Streaming from the latest sensor sample',
        'updated' => '--',
        'accent' => '#14b8a6',
        'soft' => 'rgba(20, 184, 166, 0.16)',
        'icon' => 'humidity',
    ],
    [
        'name' => 'pH',
        'value' => '--',
        'unit' => 'pH',
        'status' => 'Live',
        'tone' => 'good',
        'detail' => 'Live reading from the backend API.',
        'trend' => 'Streaming from the latest sensor sample',
        'updated' => '--',
        'accent' => '#8b5cf6',
        'soft' => 'rgba(139, 92, 246, 0.16)',
        'icon' => 'ph',
    ],
    [
        'name' => 'Ammonia',
        'value' => '--',
        'unit' => 'ppm',
        'status' => 'Live',
        'tone' => 'warning',
        'detail' => 'Live reading from the backend API.',
        'trend' => 'Streaming from the latest sensor sample',
        'updated' => '--',
        'accent' => '#f97316',
        'soft' => 'rgba(249, 115, 22, 0.18)',
        'icon' => 'ammonia',
    ],
    [
        'name' => 'Light',
        'value' => '--',
        'unit' => 'lux',
        'status' => 'Live',
        'tone' => 'danger',
        'detail' => 'Live reading from the backend API.',
        'trend' => 'Streaming from the latest sensor sample',
        'updated' => '--',
        'accent' => '#f59e0b',
        'soft' => 'rgba(245, 158, 11, 0.18)',
        'icon' => 'light',
    ],
];

$sensorStatus = [
    [
        'name' => 'Temperature Node T-01',
        'location' => 'Grow Bed A',
        'state' => 'Online',
        'tone' => 'good',
        'meta' => 'Synced 2 min ago',
    ],
    [
        'name' => 'Humidity Node H-02',
        'location' => 'Greenhouse East',
        'state' => 'Online',
        'tone' => 'good',
        'meta' => 'Synced 1 min ago',
    ],
    [
        'name' => 'pH Probe PH-01',
        'location' => 'Nutrient Tank',
        'state' => 'Online',
        'tone' => 'good',
        'meta' => 'Synced just now',
    ],
    [
        'name' => 'Water Temp Node WT-05',
        'location' => 'Fish Tank Loop',
        'state' => 'Online',
        'tone' => 'good',
        'meta' => 'Synced just now',
    ],
    [
        'name' => 'Ammonia Sensor NH3-01',
        'location' => 'Biofilter Chamber',
        'state' => 'Online',
        'tone' => 'warning',
        'meta' => 'Synced 1 min ago',
    ],
    [
        'name' => 'Light Meter L-04',
        'location' => 'Seedling Zone',
        'state' => 'Online',
        'tone' => 'warning',
        'meta' => 'Signal is stable',
    ],
    [
        'name' => 'Water Level W-03',
        'location' => 'Reservoir Loop',
        'state' => 'Offline',
        'tone' => 'offline',
        'meta' => 'No heartbeat for 14 min',
    ],
    [
        'name' => 'EC Sensor EC-02',
        'location' => 'Mixing Chamber',
        'state' => 'Online',
        'tone' => 'good',
        'meta' => 'Synced 3 min ago',
    ],
];

$sensorTable = [
    [
        'name' => 'Temperature Sensor T-01',
        'zone' => 'Grow Bed A',
        'value' => '--',
        'status' => 'Live',
        'tone' => 'good',
        'updated' => '--',
    ],
    [
        'name' => 'Humidity Sensor H-02',
        'zone' => 'Greenhouse East',
        'value' => '--',
        'status' => 'Live',
        'tone' => 'warning',
        'updated' => '--',
    ],
    [
        'name' => 'pH Probe PH-01',
        'zone' => 'Nutrient Tank',
        'value' => '--',
        'status' => 'Live',
        'tone' => 'good',
        'updated' => '--',
    ],
    [
        'name' => 'Water Temperature Sensor WT-05',
        'zone' => 'Fish Tank Loop',
        'value' => '--',
        'status' => 'Live',
        'tone' => 'good',
        'updated' => '--',
    ],
    [
        'name' => 'Ammonia Sensor NH3-01',
        'zone' => 'Biofilter Chamber',
        'value' => '--',
        'status' => 'Live',
        'tone' => 'warning',
        'updated' => '--',
    ],
    [
        'name' => 'Light Meter L-04',
        'zone' => 'Seedling Zone',
        'value' => '--',
        'status' => 'Live',
        'tone' => 'danger',
        'updated' => '--',
    ],
    [
        'name' => 'Water Level W-03',
        'zone' => 'Reservoir Loop',
        'value' => '--',
        'status' => 'Offline',
        'tone' => 'offline',
        'updated' => '2026-04-18 08:35',
    ],
    [
        'name' => 'EC Sensor EC-02',
        'zone' => 'Mixing Chamber',
        'value' => '1.8 mS/cm',
        'status' => 'Good',
        'tone' => 'good',
        'updated' => '2026-04-18 08:44',
    ],
];

if (!function_exists('sensors_badge_class')) {
    function sensors_badge_class($tone)
    {
        $classes = [
            'good' => 'status-badge',
            'warning' => 'status-badge badge-warning',
            'danger' => 'status-badge badge-danger',
            'offline' => 'status-badge badge-offline',
            'neutral' => 'status-badge badge-neutral',
        ];

        return $classes[$tone] ?? 'status-badge';
    }
}

if (!function_exists('sensors_dot_class')) {
    function sensors_dot_class($tone)
    {
        $classes = [
            'good' => 'sensor-status-dot dot-good',
            'warning' => 'sensor-status-dot dot-warning',
            'danger' => 'sensor-status-dot dot-danger',
            'offline' => 'sensor-status-dot dot-offline',
        ];

        return $classes[$tone] ?? 'sensor-status-dot dot-good';
    }
}

if (!function_exists('sensors_icon')) {
    function sensors_icon($type)
    {
        switch ($type) {
            case 'temperature':
                return <<<SVG
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M14 14.76V5a2 2 0 0 0-4 0v9.76a4 4 0 1 0 4 0Z"></path>
    <path d="M12 9v8"></path>
</svg>
SVG;
            case 'water_temperature':
                return <<<SVG
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M15 14.5V5a3 3 0 0 0-6 0v9.5a4.5 4.5 0 1 0 6 0Z"></path>
    <path d="M12 9v6"></path>
    <path d="M5 18c1-.7 2-.7 3 0s2 .7 3 0 2-.7 3 0 2 .7 3 0"></path>
</svg>
SVG;
            case 'humidity':
                return <<<SVG
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M12 3.5c-3.2 4.1-5.5 6.8-5.5 10a5.5 5.5 0 0 0 11 0c0-3.2-2.3-5.9-5.5-10Z"></path>
    <path d="M9.5 15.5a2.5 2.5 0 0 0 5 0"></path>
</svg>
SVG;
            case 'ammonia':
                return <<<SVG
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M9 3h6"></path>
    <path d="M10 3v4l-4.2 7a4.7 4.7 0 0 0 4 7h4.4a4.7 4.7 0 0 0 4-7L14 7V3"></path>
    <path d="M8.5 14.5h7"></path>
    <path d="M9.5 17.5h5"></path>
</svg>
SVG;
            case 'ph':
                return <<<SVG
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M10 2v5.5L5.8 14a4.6 4.6 0 0 0 3.9 7h4.6a4.6 4.6 0 0 0 3.9-7L14 7.5V2"></path>
    <path d="M8 12h8"></path>
    <path d="M9.2 16.2h5.6"></path>
</svg>
SVG;
            case 'light':
            default:
                return <<<SVG
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <circle cx="12" cy="12" r="4"></circle>
    <path d="M12 2v2.5"></path>
    <path d="M12 19.5V22"></path>
    <path d="M4.9 4.9l1.8 1.8"></path>
    <path d="M17.3 17.3l1.8 1.8"></path>
    <path d="M2 12h2.5"></path>
    <path d="M19.5 12H22"></path>
    <path d="M4.9 19.1l1.8-1.8"></path>
    <path d="M17.3 6.7l1.8-1.8"></path>
</svg>
SVG;
        }
    }
}

if (!function_exists('render_sensors_page_styles')) {
    function render_sensors_page_styles()
    {
        echo <<<'CSS'
<style>
    .sensors-heading {
        margin-bottom: 1.5rem;
    }

    .sensors-kicker {
        display: inline-block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #0f766e;
        margin-bottom: 10px;
    }

    .page-summary {
        min-width: 240px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.88), rgba(236, 253, 245, 0.92));
    }

    .summary-label {
        display: block;
        font-size: 12px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 6px;
    }

    .summary-value {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
        color: #0f172a;
    }

    .sensor-card {
        position: relative;
        overflow: hidden;
        height: 100%;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.94), rgba(255, 255, 255, 0.78));
        border: 1px solid rgba(255, 255, 255, 0.42);
    }

    .sensor-card::before {
        content: "";
        position: absolute;
        top: -50px;
        right: -35px;
        width: 140px;
        height: 140px;
        background: radial-gradient(circle, var(--sensor-soft), rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
    }

    .metric-icon {
        width: 50px;
        height: 50px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--sensor-accent);
        background: linear-gradient(135deg, var(--sensor-soft), rgba(255, 255, 255, 0.96));
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.48);
    }

    .metric-icon svg {
        width: 24px;
        height: 24px;
    }

    .sensor-reading {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        margin-top: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .sensor-value {
        font-size: 2.2rem;
        font-weight: 700;
        line-height: 1;
        color: #0f172a;
    }

    .sensor-unit {
        font-size: 0.95rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        padding-bottom: 6px;
    }

    .sensor-card-copy {
        color: #64748b;
        min-height: 42px;
    }

    .sensor-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        border-top: 1px solid rgba(148, 163, 184, 0.15);
        margin-top: 16px;
        padding-top: 14px;
        font-size: 13px;
        color: #64748b;
    }

    .trend-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.05);
        font-size: 12px;
        font-weight: 600;
        color: #334155;
    }

    .chart-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .legend-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(248, 250, 252, 0.92);
        border: 1px solid rgba(148, 163, 184, 0.14);
        font-size: 13px;
        color: #475569;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .chart-stage {
        position: relative;
        height: 320px;
    }

    .status-metric {
        background: rgba(248, 250, 252, 0.82);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 16px;
        padding: 16px;
        height: 100%;
    }

    .status-metric span {
        display: block;
        font-size: 12px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 8px;
    }

    .status-metric strong {
        display: block;
        font-size: 1.65rem;
        line-height: 1;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .status-metric small {
        color: #64748b;
    }

    .status-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .status-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding-top: 14px;
        border-top: 1px solid rgba(148, 163, 184, 0.12);
    }

    .status-item:first-child {
        border-top: 0;
        padding-top: 0;
    }

    .sensor-node {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .sensor-status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .dot-good {
        background: #16a34a;
        box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.12);
    }

    .dot-warning {
        background: #f59e0b;
        box-shadow: 0 0 0 5px rgba(245, 158, 11, 0.12);
    }

    .dot-danger {
        background: #ef4444;
        box-shadow: 0 0 0 5px rgba(239, 68, 68, 0.12);
    }

    .dot-offline {
        background: #94a3b8;
        box-shadow: 0 0 0 5px rgba(148, 163, 184, 0.12);
    }

    .status-badge.badge-warning {
        background: #fef3c7;
        color: #b45309;
    }

    .status-badge.badge-danger {
        background: #fee2e2;
        color: #dc2626;
    }

    .status-badge.badge-offline {
        background: #e2e8f0;
        color: #475569;
    }

    .status-badge.badge-neutral {
        background: #e0f2fe;
        color: #0369a1;
    }

    .table-panel .table {
        margin-bottom: 0;
    }

    .table-panel thead th {
        border-bottom: 1px solid rgba(148, 163, 184, 0.18);
        color: #64748b;
        font-size: 12px;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        font-weight: 600;
        padding: 0 0.75rem 1rem;
    }

    .table-panel .table > :not(caption) > * > * {
        background: transparent;
        border-bottom-color: rgba(148, 163, 184, 0.12);
        padding: 1rem 0.75rem;
    }

    .sensor-name {
        font-weight: 600;
        color: #0f172a;
    }

    .sensor-subtext {
        color: #64748b;
        font-size: 13px;
    }

    .value-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 12px;
        background: rgba(248, 250, 252, 0.92);
        font-weight: 600;
        color: #0f172a;
    }

    #time {
        color: #64748b !important;
        font-weight: 500;
    }

    @media (max-width: 991.98px) {
        .page-summary {
            width: 100%;
        }

        .chart-stage {
            height: 280px;
        }
    }

    @media (max-width: 575.98px) {
        .sensor-value {
            font-size: 1.9rem;
        }

        .status-item {
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
    <title>Sensors | AquaSmart Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <?php render_sensors_page_styles(); ?>
</head>
<body>
<?php include __DIR__ . '/../components/sidebar.php'; ?>
<?php include __DIR__ . '/../components/navbar.php'; ?>
<div class="main-content">
    <div class="container-fluid">
<?php else: ?>
<?php render_sensors_page_styles(); ?>
<?php endif; ?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 sensors-heading">
    <div>
        <span class="sensors-kicker">Sensor Operations</span>
        <h2 class="mb-2">Environmental Sensors</h2>
        <p class="text-secondary mb-0">A clean view of live readings, device health, and recent updates across the aquaponics system.</p>
    </div>

    <div class="card-soft page-summary">
        <span class="summary-label">Network Health</span>
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div>
                <div class="summary-value" id="sensorNetworkHealth">--</div>
                <small class="text-secondary" id="sensorNetworkHealthDetail">Waiting for live data</small>
            </div>
            <span class="status-badge badge-neutral">Stable</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <?php foreach ($sensorCards as $card): ?>
        <div class="col-sm-6 col-xl-4">
            <div class="card-soft sensor-card" style="--sensor-accent: <?php echo htmlspecialchars($card['accent']); ?>; --sensor-soft: <?php echo htmlspecialchars($card['soft']); ?>;">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                    <div class="metric-icon">
                        <?php echo sensors_icon($card['icon']); ?>
                    </div>

                    <span class="<?php echo htmlspecialchars(sensors_badge_class($card['tone'])); ?>">
                        <?php echo htmlspecialchars($card['status']); ?>
                    </span>
                </div>

                <h6 class="text-uppercase fw-semibold"><?php echo htmlspecialchars($card['name']); ?></h6>

                <div class="sensor-reading">
                    <span class="sensor-value" data-field="<?php echo htmlspecialchars($card['icon']); ?>"><?php echo htmlspecialchars($card['value']); ?></span>
                    <span class="sensor-unit"><?php echo htmlspecialchars($card['unit']); ?></span>
                </div>

                <p class="sensor-card-copy mb-0"><?php echo htmlspecialchars($card['detail']); ?></p>

                <div class="sensor-meta">
                    <span class="trend-pill"><?php echo htmlspecialchars($card['trend']); ?></span>
                    <span data-field="updated_relative"><?php echo htmlspecialchars($card['updated']); ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-8">
        <div class="card-soft h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="sensors-kicker mb-2">Trend View</span>
                    <h5 class="mb-2">Sensor Trends</h5>
                    <p class="text-secondary mb-0">Multi-sensor movement across the last seven reporting windows.</p>
                </div>

                <div class="chart-toolbar">
                    <span class="legend-chip"><span class="legend-dot" style="background:#22c55e;"></span>Temperature</span>
                    <span class="legend-chip"><span class="legend-dot" style="background:#2563eb;"></span>Water Temperature</span>
                    <span class="legend-chip"><span class="legend-dot" style="background:#14b8a6;"></span>Humidity</span>
                    <span class="legend-chip"><span class="legend-dot" style="background:#8b5cf6;"></span>pH</span>
                    <span class="legend-chip"><span class="legend-dot" style="background:#f97316;"></span>Ammonia</span>
                    <span class="legend-chip"><span class="legend-dot" style="background:#f59e0b;"></span>Light</span>
                </div>
            </div>

            <div class="chart-stage">
                <canvas id="sensorTrendsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-soft h-100">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="sensors-kicker mb-2">Connectivity</span>
                    <h5 class="mb-2">Sensor Status</h5>
                    <p class="text-secondary mb-0">Live availability across active devices and critical nodes.</p>
                </div>

                <span class="status-badge" id="sensorOnlineCount">--</span>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="status-metric">
                        <span>Offline</span>
                        <strong id="sensorOfflineCount">--</strong>
                        <small>Water level node needs attention</small>
                    </div>
                </div>

                <div class="col-6">
                    <div class="status-metric">
                        <span>Avg Latency</span>
                        <strong id="sensorLatencyValue">--</strong>
                        <small>Healthy device response time</small>
                    </div>
                </div>
            </div>

            <div class="status-list">
                <?php foreach ($sensorStatus as $device): ?>
                    <div class="status-item">
                        <div class="sensor-node">
                            <span class="<?php echo htmlspecialchars(sensors_dot_class($device['tone'])); ?>"></span>

                            <div>
                                <div class="sensor-name"><?php echo htmlspecialchars($device['name']); ?></div>
                                <div class="sensor-subtext"><?php echo htmlspecialchars($device['location']); ?> . <?php echo htmlspecialchars($device['meta']); ?></div>
                            </div>
                        </div>

                        <span class="<?php echo htmlspecialchars(sensors_badge_class($device['tone'])); ?>">
                            <?php echo htmlspecialchars($device['state']); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card-soft table-panel">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="sensors-kicker mb-2">Inventory</span>
                    <h5 class="mb-2">All Sensors</h5>
                    <p class="text-secondary mb-0">Latest values, operating status, and last report times for the full sensor network.</p>
                </div>

                <span class="status-badge badge-neutral">8 Sensors Tracked</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Sensor Name</th>
                            <th scope="col">Value</th>
                            <th scope="col">Status</th>
                            <th scope="col">Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sensorTable as $sensor): ?>
                            <tr>
                                <td>
                                    <div class="sensor-name"><?php echo htmlspecialchars($sensor['name']); ?></div>
                                    <div class="sensor-subtext"><?php echo htmlspecialchars($sensor['zone']); ?></div>
                                </td>
                                <td>
                                    <span class="value-chip"><?php echo htmlspecialchars($sensor['value']); ?></span>
                                </td>
                                <td>
                                    <span class="<?php echo htmlspecialchars(sensors_badge_class($sensor['tone'])); ?>">
                                        <?php echo htmlspecialchars($sensor['status']); ?>
                                    </span>
                                </td>
                                <td class="text-secondary"><?php echo htmlspecialchars($sensor['updated']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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

            var now = new Date();
            timeElement.textContent = now.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function buildChart() {
            var chartCanvas = document.getElementById('sensorTrendsChart');
            if (!chartCanvas || typeof window.Chart === 'undefined') {
                return;
            }

            var existingChart = window.Chart.getChart(chartCanvas);
            if (existingChart) {
                existingChart.destroy();
            }

            new window.Chart(chartCanvas, {
                type: 'line',
                data: {
                    labels: ['06:00', '09:00', '12:00', '15:00', '18:00', '21:00', '00:00'],
                    datasets: [
                        {
                            label: 'Temperature',
                            data: [23.8, 24.1, 24.4, 24.9, 24.6, 24.2, 24.6],
                            yAxisID: 'tempAxis',
                            borderColor: '#22c55e',
                            backgroundColor: 'rgba(34, 197, 94, 0.12)',
                            borderWidth: 2.5,
                            pointRadius: 0,
                            tension: 0.35
                        },
                        {
                            label: 'Water Temperature',
                            data: [25.6, 25.8, 26.0, 26.2, 26.1, 25.9, 26.0],
                            yAxisID: 'tempAxis',
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.12)',
                            borderWidth: 2.5,
                            pointRadius: 0,
                            tension: 0.35
                        },
                        {
                            label: 'Humidity',
                            data: [64, 63, 61, 59, 58, 57, 58],
                            yAxisID: 'humidityAxis',
                            borderColor: '#14b8a6',
                            backgroundColor: 'rgba(20, 184, 166, 0.12)',
                            borderWidth: 2.5,
                            pointRadius: 0,
                            tension: 0.35
                        },
                        {
                            label: 'pH',
                            data: [6.7, 6.8, 6.9, 6.8, 6.8, 6.7, 6.8],
                            yAxisID: 'phAxis',
                            borderColor: '#8b5cf6',
                            backgroundColor: 'rgba(139, 92, 246, 0.12)',
                            borderWidth: 2.5,
                            pointRadius: 0,
                            tension: 0.35
                        },
                        {
                            label: 'Ammonia',
                            data: [0.28, 0.34, 0.41, 0.56, 0.62, 0.58, 0.62],
                            yAxisID: 'ammoniaAxis',
                            borderColor: '#f97316',
                            backgroundColor: 'rgba(249, 115, 22, 0.12)',
                            borderWidth: 2.5,
                            pointRadius: 0,
                            tension: 0.35
                        },
                        {
                            label: 'Light',
                            data: [310, 420, 560, 610, 520, 460, 420],
                            yAxisID: 'lightAxis',
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.12)',
                            borderWidth: 2.5,
                            pointRadius: 0,
                            tension: 0.35
                        }
                    ]
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
                            borderColor: 'rgba(255, 255, 255, 0.12)',
                            borderWidth: 1,
                            padding: 12
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#64748b'
                            }
                        },
                        tempAxis: {
                            type: 'linear',
                            position: 'left',
                            min: 20,
                            max: 28,
                            ticks: {
                                color: '#64748b',
                                callback: function (value) {
                                    return value + ' C';
                                }
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.12)'
                            }
                        },
                        humidityAxis: {
                            display: false,
                            min: 40,
                            max: 80
                        },
                        phAxis: {
                            display: false,
                            min: 5.5,
                            max: 7.5
                        },
                        ammoniaAxis: {
                            display: false,
                            min: 0,
                            max: 1.2
                        },
                        lightAxis: {
                            type: 'linear',
                            position: 'right',
                            min: 0,
                            max: 700,
                            grid: {
                                drawOnChartArea: false
                            },
                            ticks: {
                                color: '#94a3b8',
                                callback: function (value) {
                                    return value + ' lx';
                                }
                            }
                        }
                    }
                }
            });
        }

        function initChart() {
            if (typeof window.Chart !== 'undefined') {
                buildChart();
                return;
            }

            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = buildChart;
            document.head.appendChild(script);
        }

        updateClock();
        setInterval(updateClock, 60000);
        initChart();
    })();
</script>
<script src="<?php echo $isStandalone ? '../assets/js/app.js' : 'assets/js/app.js'; ?>"></script>

<?php if ($isStandalone): ?>
    </div>
</div>
</body>
</html>
<?php endif; ?>
