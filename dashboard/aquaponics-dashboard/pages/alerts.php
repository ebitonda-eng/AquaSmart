<?php
$scriptFile = isset($_SERVER['SCRIPT_FILENAME']) ? realpath($_SERVER['SCRIPT_FILENAME']) : null;
$isStandalone = $scriptFile && $scriptFile === realpath(__FILE__);

$activeAlerts = [
    [
        'icon' => 'fire',
        'message' => 'High Temperature',
        'type' => 'Thermal',
        'severity' => 'Critical',
        'timestamp' => '2 min ago',
        'detail' => 'Grow Bed A reached 29.4 C and exceeded the safe daytime threshold.',
        'accent' => '#ef4444',
        'soft' => 'rgba(239, 68, 68, 0.16)',
    ],
    [
        'icon' => 'water',
        'message' => 'Low Water Level',
        'type' => 'Water',
        'severity' => 'Warning',
        'timestamp' => '8 min ago',
        'detail' => 'Reservoir volume dropped below the preferred refill trigger point.',
        'accent' => '#0ea5e9',
        'soft' => 'rgba(14, 165, 233, 0.16)',
    ],
    [
        'icon' => 'thermometer',
        'message' => 'Water Temperature Low',
        'type' => 'Water Temperature',
        'severity' => 'Warning',
        'timestamp' => '10 min ago',
        'detail' => 'Nursery loop water temperature slipped to 19.3 C and crossed the warning threshold.',
        'accent' => '#2563eb',
        'soft' => 'rgba(37, 99, 235, 0.16)',
    ],
    [
        'icon' => 'warning',
        'message' => 'Pump Overload',
        'type' => 'Pump',
        'severity' => 'Critical',
        'timestamp' => '12 min ago',
        'detail' => 'Circulation motor load spiked during the last runtime cycle.',
        'accent' => '#f97316',
        'soft' => 'rgba(249, 115, 22, 0.18)',
    ],
    [
        'icon' => 'warning',
        'message' => 'Sensor Signal Weak',
        'type' => 'Sensor',
        'severity' => 'Low',
        'timestamp' => '19 min ago',
        'detail' => 'Humidity node H-02 reported intermittent packet loss from Greenhouse East.',
        'accent' => '#8b5cf6',
        'soft' => 'rgba(139, 92, 246, 0.16)',
    ],
    [
        'icon' => 'ammonia',
        'message' => 'Ammonia Level Rising',
        'type' => 'Ammonia',
        'severity' => 'Warning',
        'timestamp' => '21 min ago',
        'detail' => 'NH3 concentration reached 0.62 ppm and moved above the warning threshold.',
        'accent' => '#f97316',
        'soft' => 'rgba(249, 115, 22, 0.18)',
    ],
];

$alertHistory = [
    [
        'alert' => 'High Temperature',
        'type' => 'Thermal',
        'time' => 'Apr 18, 09:12',
        'status' => 'Active',
        'tone' => 'critical',
        'detail' => 'Grow Bed A',
    ],
    [
        'alert' => 'Low Water Level',
        'type' => 'Water',
        'time' => 'Apr 18, 09:06',
        'status' => 'Active',
        'tone' => 'warning',
        'detail' => 'Reservoir Tank',
    ],
    [
        'alert' => 'Water Temperature Low',
        'type' => 'Water Temperature',
        'time' => 'Apr 18, 09:04',
        'status' => 'Active',
        'tone' => 'warning',
        'detail' => 'Nursery Loop at 19.3 C',
    ],
    [
        'alert' => 'Pump Overload',
        'type' => 'Pump',
        'time' => 'Apr 18, 09:02',
        'status' => 'Active',
        'tone' => 'critical',
        'detail' => 'Circulation Line',
    ],
    [
        'alert' => 'Ammonia Level Rising',
        'type' => 'Ammonia',
        'time' => 'Apr 18, 08:58',
        'status' => 'Active',
        'tone' => 'warning',
        'detail' => 'NH3 reached 0.62 ppm',
    ],
    [
        'alert' => 'Sensor Signal Weak',
        'type' => 'Sensor',
        'time' => 'Apr 18, 08:55',
        'status' => 'Active',
        'tone' => 'low',
        'detail' => 'Humidity Node H-02',
    ],
    [
        'alert' => 'Ammonia Spike',
        'type' => 'Ammonia',
        'time' => 'Apr 18, 08:33',
        'status' => 'Resolved',
        'tone' => 'critical',
        'detail' => 'NH3 peaked at 1.08 ppm',
    ],
    [
        'alert' => 'Water Temperature High',
        'type' => 'Water Temperature',
        'time' => 'Apr 18, 08:27',
        'status' => 'Resolved',
        'tone' => 'critical',
        'detail' => 'Fish tank reached 30.6 C',
    ],
    [
        'alert' => 'pH Deviation',
        'type' => 'Nutrient',
        'time' => 'Apr 18, 08:21',
        'status' => 'Resolved',
        'tone' => 'resolved',
        'detail' => 'Nutrient Tank',
    ],
    [
        'alert' => 'Water Flow Dip',
        'type' => 'Flow',
        'time' => 'Apr 18, 07:48',
        'status' => 'Resolved',
        'tone' => 'resolved',
        'detail' => 'Refill Loop',
    ],
    [
        'alert' => 'Power Fluctuation',
        'type' => 'Electrical',
        'time' => 'Apr 18, 07:12',
        'status' => 'Resolved',
        'tone' => 'resolved',
        'detail' => 'Pump Relay Rack',
    ],
    [
        'alert' => 'Humidity Drift',
        'type' => 'Climate',
        'time' => 'Apr 18, 06:44',
        'status' => 'Resolved',
        'tone' => 'resolved',
        'detail' => 'Greenhouse East',
    ],
];

$totalAlertsToday = count($alertHistory);
$activeAlertCount = count($activeAlerts);

$healthLevel = 'Good';
foreach ($activeAlerts as $alert) {
    if ($alert['severity'] === 'Critical') {
        $healthLevel = 'Critical';
        break;
    }

    if ($alert['severity'] === 'Warning') {
        $healthLevel = 'Warning';
    }
}

$severityCounts = [
    'Critical' => 0,
    'Warning' => 0,
    'Low' => 0,
];

foreach ($activeAlerts as $alert) {
    if (isset($severityCounts[$alert['severity']])) {
        $severityCounts[$alert['severity']]++;
    }
}

if (!function_exists('alerts_severity_badge_class')) {
    function alerts_severity_badge_class($tone)
    {
        $classes = [
            'Low' => 'status-badge badge-low',
            'Warning' => 'status-badge badge-warning',
            'Critical' => 'status-badge badge-critical',
            'Active' => 'status-badge badge-warning',
            'Resolved' => 'status-badge badge-resolved',
            'critical' => 'status-badge badge-critical',
            'warning' => 'status-badge badge-warning',
            'low' => 'status-badge badge-low',
            'resolved' => 'status-badge badge-resolved',
            'health-good' => 'status-badge badge-resolved',
            'health-warning' => 'status-badge badge-warning',
            'health-critical' => 'status-badge badge-critical',
        ];

        return $classes[$tone] ?? 'status-badge';
    }
}

if (!function_exists('alerts_tone_class')) {
    function alerts_tone_class($tone)
    {
        $classes = [
            'critical' => 'tone-critical',
            'warning' => 'tone-warning',
            'low' => 'tone-low',
            'resolved' => 'tone-resolved',
        ];

        return $classes[$tone] ?? 'tone-low';
    }
}

if (!function_exists('alerts_icon_markup')) {
    function alerts_icon_markup($icon)
    {
        $icons = [
            'fire' => '&#128293;',
            'water' => '&#128167;',
            'thermometer' => '&#127777;&#65039;',
            'ammonia' => '&#129514;',
            'warning' => '&#9888;&#65039;',
        ];

        return $icons[$icon] ?? '&#9888;&#65039;';
    }
}

if (!function_exists('alerts_type_icon_key')) {
    function alerts_type_icon_key($type)
    {
        $map = [
            'Thermal' => 'fire',
            'Water' => 'water',
            'Water Temperature' => 'thermometer',
            'Flow' => 'water',
            'Ammonia' => 'ammonia',
            'Pump' => 'warning',
            'Sensor' => 'warning',
            'Nutrient' => 'warning',
            'Electrical' => 'warning',
            'Climate' => 'warning',
        ];

        return $map[$type] ?? 'warning';
    }
}

if (!function_exists('render_alerts_styles')) {
    function render_alerts_styles()
    {
        echo <<<'CSS'
<style>
    .alerts-shell {
        padding-bottom: 20px;
    }

    .alerts-header {
        margin-bottom: 2rem;
    }

    .alerts-kicker {
        display: inline-block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #0f766e;
        margin-bottom: 10px;
    }

    .alerts-overview {
        min-width: 260px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.92), rgba(239, 246, 255, 0.94));
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 18px 40px rgba(148, 163, 184, 0.14);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .alerts-overview:hover {
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

    .alerts-section {
        margin-top: 0.75rem !important;
    }

    .alerts-shell .row.align-items-stretch > [class*="col-"] {
        display: flex;
    }

    .alerts-shell .row.align-items-stretch > [class*="col-"] > .card-soft {
        width: 100%;
    }

    .alerts-panel,
    .status-panel,
    .history-panel {
        position: relative;
        overflow: hidden;
        padding: 24px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 255, 255, 0.82));
        border: 1px solid rgba(255, 255, 255, 0.46);
        box-shadow: 0 16px 36px rgba(148, 163, 184, 0.13);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .alerts-panel:hover,
    .status-panel:hover,
    .history-panel:hover,
    .alert-tile:hover,
    .status-metric:hover,
    .history-table tbody tr:hover {
        transform: translateY(-6px);
        box-shadow: 0 22px 46px rgba(148, 163, 184, 0.18);
        border-color: rgba(203, 213, 225, 0.58);
    }

    .panel-copy {
        color: #64748b;
        margin-bottom: 0;
        line-height: 1.55;
    }

    .alerts-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        margin-top: 4px;
    }

    .alert-tile {
        position: relative;
        overflow: hidden;
        min-height: 100%;
        padding: 22px;
        display: flex;
        flex-direction: column;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.92));
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 20px;
        box-shadow: 0 14px 28px rgba(148, 163, 184, 0.12);
        transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease, background-color 0.24s ease;
    }

    .alert-tile.tone-critical {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(255, 241, 242, 0.92));
    }

    .alert-tile.tone-warning {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(255, 247, 237, 0.92));
    }

    .alert-tile.tone-low {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(245, 243, 255, 0.9));
    }

    .alert-tile::before {
        content: "";
        position: absolute;
        top: -46px;
        right: -30px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: radial-gradient(circle, var(--alert-soft), rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
    }

    .alert-tile::after {
        content: "";
        position: absolute;
        top: 0;
        left: 18px;
        right: 18px;
        height: 4px;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--alert-accent), rgba(255, 255, 255, 0));
        opacity: 0.9;
    }

    .alert-tile:hover {
        box-shadow: 0 20px 40px rgba(148, 163, 184, 0.16);
    }

    .alert-topline {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 20px;
    }

    .alert-icon {
        position: relative;
        width: 60px;
        height: 60px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        background: linear-gradient(135deg, var(--alert-soft), rgba(255, 255, 255, 0.98));
        border: 1px solid rgba(255, 255, 255, 0.72);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.55), 0 14px 28px rgba(148, 163, 184, 0.15);
        transition: transform 0.24s ease, box-shadow 0.24s ease;
    }

    .alert-icon::after {
        content: "";
        position: absolute;
        inset: -6px;
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.42);
        opacity: 0.85;
    }

    .alert-message {
        font-size: 1.08rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
        margin-bottom: 12px;
    }

    .alert-detail {
        color: #64748b;
        line-height: 1.55;
        margin-bottom: 20px;
    }

    .alert-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        border-top: 1px solid rgba(148, 163, 184, 0.12);
        padding-top: 16px;
        color: #64748b;
        font-size: 13px;
        margin-top: auto;
    }

    .alert-meta strong {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.7);
        color: #0f172a;
        font-weight: 600;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
    }

    .status-stack {
        display: grid;
        gap: 14px;
    }

    .status-metric {
        padding: 18px;
        background: rgba(248, 250, 252, 0.86);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 18px;
        transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease;
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
        font-size: 1.85rem;
        line-height: 1;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .status-metric p {
        margin: 0;
        color: #64748b;
        line-height: 1.5;
    }

    .severity-list {
        display: grid;
        gap: 12px;
        margin-top: 18px;
    }

    .severity-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding-top: 12px;
        border-top: 1px solid rgba(148, 163, 184, 0.12);
    }

    .severity-row:first-child {
        border-top: 0;
        padding-top: 0;
    }

    .severity-name {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #334155;
        font-weight: 600;
    }

    .severity-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .dot-critical {
        background: #ef4444;
    }

    .dot-warning {
        background: #f59e0b;
    }

    .dot-low {
        background: #3b82f6;
    }

    .history-table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .history-table thead th {
        border-bottom: 0;
        color: #64748b;
        font-size: 12px;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        font-weight: 600;
        padding: 0 0.75rem 1rem;
    }

    .history-table > :not(caption) > * > * {
        background: transparent;
        border-bottom-color: transparent;
        padding: 1rem 0.75rem;
    }

    .history-table tbody tr {
        transition: transform 0.22s ease;
    }

    .history-table tbody td {
        background: rgba(248, 250, 252, 0.9) !important;
        border-top: 1px solid rgba(226, 232, 240, 0.85);
        border-bottom: 1px solid rgba(226, 232, 240, 0.85);
        box-shadow: 0 10px 24px rgba(148, 163, 184, 0.08);
        transition: background-color 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
    }

    .history-table tbody td:first-child {
        border-left: 1px solid rgba(226, 232, 240, 0.85);
        border-radius: 16px 0 0 16px;
    }

    .history-table tbody td:last-child {
        border-right: 1px solid rgba(226, 232, 240, 0.85);
        border-radius: 0 16px 16px 0;
    }

    .history-table tbody tr:hover td {
        background: rgba(255, 255, 255, 0.98) !important;
        border-color: rgba(203, 213, 225, 0.9);
        box-shadow: 0 16px 30px rgba(148, 163, 184, 0.12);
    }

    .history-entry {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .history-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        background: linear-gradient(135deg, #e0f2fe, #ffffff);
        border: 1px solid rgba(191, 219, 254, 0.85);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
    }

    .history-icon.tone-critical {
        background: linear-gradient(135deg, #fee2e2, #ffffff);
        border-color: rgba(252, 165, 165, 0.8);
    }

    .history-icon.tone-warning {
        background: linear-gradient(135deg, #ffedd5, #ffffff);
        border-color: rgba(253, 186, 116, 0.8);
    }

    .history-icon.tone-low {
        background: linear-gradient(135deg, #ede9fe, #ffffff);
        border-color: rgba(196, 181, 253, 0.8);
    }

    .history-icon.tone-resolved {
        background: linear-gradient(135deg, #dcfce7, #ffffff);
        border-color: rgba(134, 239, 172, 0.8);
    }

    .history-alert {
        font-weight: 600;
        color: #0f172a;
    }

    .history-subtext {
        color: #64748b;
        font-size: 13px;
    }

    .type-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(226, 232, 240, 0.9);
        font-weight: 600;
        color: #0f172a;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
    }

    .status-badge {
        border: 1px solid rgba(255, 255, 255, 0.68);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65), 0 8px 18px rgba(148, 163, 184, 0.12);
        transition: transform 0.24s ease, box-shadow 0.24s ease;
    }

    .status-badge.badge-low {
        background: #eff6ff;
        color: #2563eb;
        border-color: rgba(191, 219, 254, 0.9);
    }

    .status-badge.badge-warning {
        background: #fff7ed;
        color: #c2410c;
        border-color: rgba(253, 186, 116, 0.9);
    }

    .status-badge.badge-critical {
        background: #fff1f2;
        color: #be123c;
        border-color: rgba(253, 164, 175, 0.9);
    }

    .status-badge.badge-resolved {
        background: #ecfdf3;
        color: #15803d;
        border-color: rgba(134, 239, 172, 0.9);
    }

    .alert-tile:hover .alert-icon {
        transform: translateY(-2px) scale(1.04);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.55), 0 18px 32px rgba(148, 163, 184, 0.18);
    }

    #time {
        color: #64748b !important;
        font-weight: 500;
    }

    @media (max-width: 991.98px) {
        .alerts-overview {
            width: 100%;
        }

        .alerts-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575.98px) {
        .alerts-panel,
        .status-panel,
        .history-panel {
            padding: 20px;
        }

        .alert-icon {
            width: 50px;
            height: 50px;
            font-size: 22px;
        }

        .history-entry {
            gap: 12px;
        }

        .history-icon {
            width: 38px;
            height: 38px;
            font-size: 16px;
        }

        .overview-value,
        .status-metric strong {
            font-size: 1.7rem;
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
    <title>Alerts | AquaSmart Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <?php render_alerts_styles(); ?>
</head>
<body>
<?php include __DIR__ . '/../components/sidebar.php'; ?>
<?php include __DIR__ . '/../components/navbar.php'; ?>
<div class="main-content">
    <div class="container-fluid alerts-shell">
<?php else: ?>
<?php render_alerts_styles(); ?>
<?php endif; ?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 alerts-header">
    <div>
        <span class="alerts-kicker">Alert Center</span>
        <h2 class="mb-2">Alert Monitoring</h2>
        <p class="text-secondary mb-0">Track live incidents, review history, and monitor overall system health across the aquaponics network.</p>
    </div>

    <div class="card-soft alerts-overview">
        <span class="overview-label">Monitoring Window</span>
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <div class="overview-value" id="alertsOverviewCount">0</div>
                <small class="text-secondary">alerts currently need attention</small>
            </div>
            <span class="<?php echo htmlspecialchars(alerts_severity_badge_class('health-' . strtolower($healthLevel))); ?>" id="alertsOverviewHealth">
                <?php echo htmlspecialchars($healthLevel); ?>
            </span>
        </div>
    </div>
</div>

<div class="row g-4 align-items-stretch">
    <div class="col-lg-8">
        <div class="card-soft alerts-panel h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="alerts-kicker mb-2">Active Alerts</span>
                    <h5 class="mb-2">Current Incidents</h5>
                    <p class="panel-copy">Live issues are grouped below with severity, incident type, and the latest reporting time.</p>
                </div>

                <span class="<?php echo htmlspecialchars(alerts_severity_badge_class('health-' . strtolower($healthLevel))); ?>" id="alertsSystemBadge">
                    <?php echo htmlspecialchars($healthLevel . ' System'); ?>
                </span>
            </div>

            <div class="alerts-grid" id="activeAlertsGrid">
                <?php foreach ($activeAlerts as $alert): ?>
                    <div class="alert-tile <?php echo htmlspecialchars(alerts_tone_class(strtolower($alert['severity']))); ?>" style="--alert-accent: <?php echo htmlspecialchars($alert['accent']); ?>; --alert-soft: <?php echo htmlspecialchars($alert['soft']); ?>;">
                        <div class="alert-topline">
                            <div class="alert-icon"><?php echo alerts_icon_markup($alert['icon']); ?></div>
                            <span class="<?php echo htmlspecialchars(alerts_severity_badge_class($alert['severity'])); ?>">
                                <?php echo htmlspecialchars($alert['severity']); ?>
                            </span>
                        </div>

                        <div class="alert-message"><?php echo htmlspecialchars($alert['message']); ?></div>
                        <p class="alert-detail"><?php echo htmlspecialchars($alert['detail']); ?></p>

                        <div class="alert-meta">
                            <span><strong><?php echo htmlspecialchars($alert['type']); ?></strong> alert</span>
                            <span><?php echo htmlspecialchars($alert['timestamp']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-soft status-panel h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="alerts-kicker mb-2">System Status</span>
                    <h5 class="mb-2">Health Overview</h5>
                    <p class="panel-copy">Quick operating metrics for alert load and the current system risk level.</p>
                </div>

                <span class="<?php echo htmlspecialchars(alerts_severity_badge_class('health-' . strtolower($healthLevel))); ?>">
                    <?php echo htmlspecialchars($healthLevel); ?>
                </span>
            </div>

            <div class="status-stack">
                <div class="status-metric">
                    <span>Total Alerts Today</span>
                    <strong id="alertsTotalToday"><?php echo htmlspecialchars((string) $totalAlertsToday); ?></strong>
                    <p>Combined active and resolved incidents logged during the current day.</p>
                </div>

                <div class="status-metric">
                    <span>Active Alerts</span>
                    <strong id="alertsActiveCount"><?php echo htmlspecialchars((string) $activeAlertCount); ?></strong>
                    <p>Incidents still open and waiting for intervention or automatic recovery.</p>
                </div>

                <div class="status-metric">
                    <span>System Health</span>
                    <strong id="alertsSystemHealth"><?php echo htmlspecialchars($healthLevel); ?></strong>
                    <p>Health reflects the highest severity currently active in the alert stream.</p>
                </div>
            </div>

            <div class="severity-list">
                <div class="severity-row">
                    <div class="severity-name">
                        <span class="severity-dot dot-critical"></span>
                        Critical
                    </div>
                    <span class="status-badge badge-critical" id="alertsCriticalCount"><?php echo htmlspecialchars((string) $severityCounts['Critical']); ?></span>
                </div>

                <div class="severity-row">
                    <div class="severity-name">
                        <span class="severity-dot dot-warning"></span>
                        Warning
                    </div>
                    <span class="status-badge badge-warning" id="alertsWarningCount"><?php echo htmlspecialchars((string) $severityCounts['Warning']); ?></span>
                </div>

                <div class="severity-row">
                    <div class="severity-name">
                        <span class="severity-dot dot-low"></span>
                        Low
                    </div>
                    <span class="status-badge badge-low" id="alertsLowCount"><?php echo htmlspecialchars((string) $severityCounts['Low']); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 align-items-stretch alerts-section">
    <div class="col-12">
        <div class="card-soft history-panel">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="alerts-kicker mb-2">Alert History</span>
                    <h5 class="mb-2">Recent Alert Log</h5>
                    <p class="panel-copy">A chronological view of alert events with incident type, time, and current resolution state.</p>
                </div>

                <span class="status-badge" id="alertsHistoryCount"><?php echo htmlspecialchars((string) $totalAlertsToday); ?> logged today</span>
            </div>

            <div class="table-responsive">
                <table class="table history-table align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Alert</th>
                            <th scope="col">Type</th>
                            <th scope="col">Time</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody id="alertHistoryBody">
                        <?php foreach ($alertHistory as $row): ?>
                            <tr>
                                <td>
                                    <div class="history-entry">
                                        <span class="history-icon <?php echo htmlspecialchars(alerts_tone_class($row['tone'])); ?>">
                                            <?php echo alerts_icon_markup(alerts_type_icon_key($row['type'])); ?>
                                        </span>
                                        <div>
                                            <div class="history-alert"><?php echo htmlspecialchars($row['alert']); ?></div>
                                            <div class="history-subtext"><?php echo htmlspecialchars($row['detail']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="type-chip"><?php echo htmlspecialchars($row['type']); ?></span>
                                </td>
                                <td class="text-secondary"><?php echo htmlspecialchars($row['time']); ?></td>
                                <td>
                                    <span class="<?php echo htmlspecialchars(alerts_severity_badge_class($row['tone'])); ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
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

            timeElement.textContent = new Date().toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        updateClock();
        setInterval(updateClock, 60000);
    })();
</script>
<script src="<?php echo $isStandalone ? '../assets/js/app.js' : 'assets/js/app.js'; ?>"></script>

<?php if ($isStandalone): ?>
    </div>
</div>
</body>
</html>
<?php endif; ?>
