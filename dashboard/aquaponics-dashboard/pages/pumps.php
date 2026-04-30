<?php
$scriptFile = isset($_SERVER['SCRIPT_FILENAME']) ? realpath($_SERVER['SCRIPT_FILENAME']) : null;
$isStandalone = $scriptFile && $scriptFile === realpath(__FILE__);

$pumpCards = [
    [
        'key' => 'heater',
        'name' => 'Heater',
        'status' => '--',
        'tone' => 'neutral',
        'description' => 'Controls water warming support for stable fish and root temperatures.',
        'accent' => '#ef4444',
        'soft' => 'rgba(239, 68, 68, 0.15)',
        'power' => 'Thermal line',
    ],
    [
        'key' => 'fan',
        'name' => 'Fan',
        'status' => '--',
        'tone' => 'neutral',
        'description' => 'Manages airflow and heat extraction across the grow environment.',
        'accent' => '#0ea5e9',
        'soft' => 'rgba(14, 165, 233, 0.15)',
        'power' => 'Ventilation line',
    ],
    [
        'key' => 'irrigation',
        'name' => 'Irrigation',
        'status' => '--',
        'tone' => 'neutral',
        'description' => 'Delivers irrigation flow to the active plant beds on demand.',
        'accent' => '#22c55e',
        'soft' => 'rgba(34, 197, 94, 0.16)',
        'power' => 'Grow-bed loop',
    ],
    [
        'key' => 'refill',
        'name' => 'Refill',
        'status' => '--',
        'tone' => 'neutral',
        'description' => 'Tops up the reservoir when supply volume needs reinforcement.',
        'accent' => '#2563eb',
        'soft' => 'rgba(37, 99, 235, 0.15)',
        'power' => 'Reservoir line',
    ],
    [
        'key' => 'filter',
        'name' => 'Filtration',
        'status' => '--',
        'tone' => 'neutral',
        'description' => 'Runs the filtration circuit for cleaner recirculating water.',
        'accent' => '#8b5cf6',
        'soft' => 'rgba(139, 92, 246, 0.16)',
        'power' => 'Filter loop',
    ],
];

$flowMetrics = [
    [
        'label' => 'Water Flow Rate',
        'value' => '--',
        'detail' => 'Measured across the active refill line.',
    ],
    [
        'label' => 'Pump Efficiency',
        'value' => '--',
        'detail' => 'Efficiency remains above the weekly average.',
    ],
];

$alerts = [
    [
        'title' => 'Waiting for live alerts',
        'detail' => 'Pump notices will appear here after the API returns current safety conditions.',
        'tone' => 'warning',
    ],
];

if (!function_exists('pumps_badge_class')) {
    function pumps_badge_class($tone)
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

if (!function_exists('pump_icon')) {
    function pump_icon($name)
    {
        if ($name === 'Heater') {
            return <<<SVG
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M12 3v7"></path>
    <path d="M9 6.5h6"></path>
    <path d="M8 14a4 4 0 1 0 8 0c0-2.4-1.4-3.8-4-6-2.6 2.2-4 3.6-4 6Z"></path>
</svg>
SVG;
        }

        if ($name === 'Fan') {
            return <<<SVG
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <circle cx="12" cy="12" r="2"></circle>
    <path d="M12 4c2.2 0 3.5 2.5 2.3 4.4L12 12"></path>
    <path d="M19 12c0 2.2-2.5 3.5-4.4 2.3L12 12"></path>
    <path d="M5 12c0-2.2 2.5-3.5 4.4-2.3L12 12"></path>
</svg>
SVG;
        }

        if ($name === 'Irrigation') {
            return <<<SVG
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M4 12h6"></path>
    <path d="M10 8l4 4-4 4"></path>
    <path d="M15 7c2.8 0 5 2.2 5 5"></path>
    <path d="M15 17c2.8 0 5-2.2 5-5"></path>
</svg>
SVG;
        }

        if ($name === 'Refill') {
            return <<<SVG
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M12 3.5c-3.2 4.1-5.5 6.8-5.5 10a5.5 5.5 0 0 0 11 0c0-3.2-2.3-5.9-5.5-10Z"></path>
    <path d="M9.5 15.5a2.5 2.5 0 0 0 5 0"></path>
</svg>
SVG;
        }

        return <<<SVG
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M4 12h5"></path>
    <path d="M15 12h5"></path>
    <path d="M9 9.5V6a2 2 0 0 1 2-2h2"></path>
    <path d="M9 14.5V18a2 2 0 0 0 2 2h2"></path>
    <circle cx="12" cy="12" r="3"></circle>
</svg>
SVG;
    }
}

if (!function_exists('render_pumps_styles')) {
    function render_pumps_styles()
    {
        echo <<<'CSS'
<style>
    .pump-shell {
        padding-bottom: 20px;
    }

    .pump-header {
        margin-bottom: 2rem;
    }

    .pump-kicker {
        display: inline-block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #0f766e;
        margin-bottom: 10px;
    }

    .pump-overview {
        min-width: 250px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.92), rgba(236, 253, 245, 0.94));
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 18px 40px rgba(148, 163, 184, 0.14);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .pump-overview:hover {
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

    .pump-section {
        margin-top: 0.75rem !important;
    }

    .pump-shell .row.align-items-stretch > [class*="col-"] {
        display: flex;
    }

    .pump-shell .row.align-items-stretch > [class*="col-"] > .card-soft {
        width: 100%;
    }

    .pump-card,
    .control-card,
    .monitor-card,
    .alerts-card {
        position: relative;
        overflow: hidden;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 255, 255, 0.82));
        border: 1px solid rgba(255, 255, 255, 0.46);
        box-shadow: 0 16px 36px rgba(148, 163, 184, 0.13);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .pump-card:hover,
    .control-card:hover,
    .monitor-card:hover,
    .alerts-card:hover,
    .metric-card:hover,
    .alert-row:hover {
        transform: translateY(-6px);
        box-shadow: 0 22px 46px rgba(148, 163, 184, 0.18);
        border-color: rgba(203, 213, 225, 0.58);
    }

    .pump-card {
        padding: 26px;
        isolation: isolate;
    }

    .pump-card::before {
        content: "";
        position: absolute;
        top: -52px;
        right: -32px;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: radial-gradient(circle, var(--pump-soft), rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
    }

    .pump-card::after {
        content: "";
        position: absolute;
        top: 0;
        left: 22px;
        right: 22px;
        height: 4px;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--pump-accent), rgba(255, 255, 255, 0));
        opacity: 0.9;
    }

    .pump-card.is-on {
        box-shadow: 0 20px 42px rgba(34, 197, 94, 0.22);
        border-color: rgba(74, 222, 128, 0.55);
    }

    .pump-card.is-danger {
        box-shadow: 0 20px 42px rgba(239, 68, 68, 0.18);
        border-color: rgba(248, 113, 113, 0.5);
    }

    .pump-topline {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 22px;
    }

    .pump-icon {
        position: relative;
        width: 62px;
        height: 62px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--pump-accent);
        background: linear-gradient(135deg, var(--pump-soft), rgba(255, 255, 255, 0.98));
        border: 1px solid rgba(255, 255, 255, 0.7);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.55), 0 14px 28px rgba(148, 163, 184, 0.15);
        transition: transform 0.24s ease, box-shadow 0.24s ease;
    }

    .pump-icon::after {
        content: "";
        position: absolute;
        inset: -6px;
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.42);
        opacity: 0.8;
    }

    .pump-icon svg {
        width: 28px;
        height: 28px;
    }

    .pump-title {
        font-size: 1.08rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 12px;
    }

    .pump-state {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }

    .state-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #94a3b8;
        box-shadow: 0 0 0 7px rgba(148, 163, 184, 0.16);
        transition: background-color 0.24s ease, box-shadow 0.24s ease;
    }

    .state-dot.is-on {
        background: #22c55e;
        box-shadow: 0 0 0 7px rgba(34, 197, 94, 0.18);
    }

    .state-dot.is-danger {
        background: #ef4444;
        box-shadow: 0 0 0 7px rgba(239, 68, 68, 0.16);
    }

    .state-label {
        font-size: 1.95rem;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -0.03em;
        color: #0f172a;
    }

    .pump-description {
        color: #64748b;
        line-height: 1.55;
        margin-bottom: 20px;
    }

    .pump-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        border-top: 1px solid rgba(148, 163, 184, 0.12);
        padding-top: 16px;
        color: #64748b;
        font-size: 13px;
    }

    .pump-meta strong {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(248, 250, 252, 0.92);
        color: #0f172a;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
    }

    .control-card,
    .monitor-card,
    .alerts-card {
        padding: 26px;
    }

    .control-grid {
        display: grid;
        gap: 18px;
    }

    .control-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 16px;
        align-items: center;
        padding: 18px;
        background: rgba(248, 250, 252, 0.82);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 18px;
        transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease;
    }

    .control-row:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 30px rgba(148, 163, 184, 0.14);
        border-color: rgba(203, 213, 225, 0.52);
    }

    .control-copy h6 {
        color: #0f172a;
        font-size: 1rem;
        margin-bottom: 8px;
    }

    .control-copy p {
        color: #64748b;
        margin-bottom: 0;
        line-height: 1.5;
    }

    .button-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: flex-end;
        align-items: center;
    }

    .btn-soft.action-btn {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 128px;
        border: 0;
        padding: 11px 20px;
        border-radius: 999px;
        font-weight: 600;
        letter-spacing: 0.01em;
        box-shadow: 0 14px 28px rgba(148, 163, 184, 0.18), inset 0 1px 0 rgba(255, 255, 255, 0.25);
        transition: transform 0.24s ease, box-shadow 0.24s ease, opacity 0.24s ease, filter 0.24s ease, background 0.24s ease, color 0.24s ease;
    }

    .btn-soft.action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 34px rgba(148, 163, 184, 0.22);
        filter: saturate(1.04);
    }

    .btn-soft.action-btn:disabled {
        transform: none;
        box-shadow: none;
        opacity: 0.6;
        cursor: not-allowed;
        filter: none;
    }

    .btn-soft.action-btn.is-off {
        background: linear-gradient(135deg, #64748b, #94a3b8);
        color: #ffffff;
    }

    .btn-soft.action-btn.is-on {
        background: linear-gradient(135deg, #16a34a, #4ade80);
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12), 0 18px 34px rgba(34, 197, 94, 0.22);
    }

    .btn-soft.action-btn.is-danger {
        background: linear-gradient(135deg, #ef4444, #fb7185);
        color: #ffffff;
    }

    .auto-mode-banner {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        background: #fff1f2;
        color: #be123c;
        border: 1px solid rgba(251, 113, 133, 0.28);
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        font-size: 12px;
    }

    .auto-mode-banner.is-manual {
        background: #ecfeff;
        color: #0f766e;
        border-color: rgba(45, 212, 191, 0.32);
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .metric-card {
        padding: 18px;
        background: rgba(248, 250, 252, 0.82);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 18px;
        transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease;
    }

    .metric-card span {
        display: block;
        font-size: 12px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 8px;
    }

    .metric-card strong {
        display: block;
        font-size: 1.8rem;
        line-height: 1;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .metric-card p {
        margin: 0;
        color: #64748b;
        line-height: 1.5;
    }

    .alert-stack {
        display: grid;
        gap: 14px;
    }

    .alert-row {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 18px;
        background: rgba(248, 250, 252, 0.84);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 18px;
        transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease;
    }

    .alert-icon {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 700;
        color: #ffffff;
        background: linear-gradient(135deg, #f59e0b, #f97316);
    }

    .alert-icon.danger {
        background: linear-gradient(135deg, #ef4444, #fb7185);
    }

    .alert-content h6 {
        font-size: 1rem;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .alert-content p,
    .section-copy {
        color: #64748b;
        margin-bottom: 0;
        line-height: 1.55;
    }

    .pump-card:hover .pump-icon {
        transform: translateY(-2px) scale(1.03);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.55), 0 18px 34px rgba(148, 163, 184, 0.18);
    }

    .status-badge.badge-warning {
        background: #fef3c7;
        color: #b45309;
    }

    .status-badge.badge-danger {
        background: #fee2e2;
        color: #dc2626;
    }

    .status-badge.badge-neutral {
        background: #e0f2fe;
        color: #0369a1;
    }

    .status-badge {
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65), 0 8px 18px rgba(148, 163, 184, 0.12);
        transition: transform 0.24s ease, box-shadow 0.24s ease;
    }

    #time {
        color: #64748b !important;
        font-weight: 500;
    }

    @media (max-width: 991.98px) {
        .pump-overview {
            width: 100%;
        }

        .control-row {
            grid-template-columns: 1fr;
        }

        .button-group {
            justify-content: flex-start;
        }
    }

    @media (max-width: 575.98px) {
        .pump-card,
        .control-card,
        .monitor-card,
        .alerts-card {
            padding: 20px;
        }

        .pump-icon {
            width: 56px;
            height: 56px;
        }

        .metrics-grid {
            grid-template-columns: 1fr;
        }

        .state-label {
            font-size: 1.5rem;
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
    <title>Pumps | AquaSmart Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <?php render_pumps_styles(); ?>
</head>
<body>
<?php include __DIR__ . '/../components/sidebar.php'; ?>
<?php include __DIR__ . '/../components/navbar.php'; ?>
<div class="main-content">
    <div class="container-fluid pump-shell">
<?php else: ?>
<?php render_pumps_styles(); ?>
<?php endif; ?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 pump-header">
    <div>
        <span class="pump-kicker">Pump Operations</span>
        <h2 class="mb-2">Pump Control Dashboard</h2>
        <p class="text-secondary mb-0">Track live relay state, switch between auto and manual control, and monitor critical flow conditions across the water system.</p>
    </div>

    <div class="card-soft pump-overview">
        <span class="overview-label">Active Pump Network</span>
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <div class="overview-value" id="pumpNetworkValue">--</div>
                <small class="text-secondary" id="pumpNetworkDetail">Waiting for live data</small>
            </div>
            <span class="status-badge badge-neutral" id="pumpModeBadge">Mode</span>
        </div>
    </div>
</div>

<div class="row g-4 align-items-stretch">
    <?php foreach ($pumpCards as $pump): ?>
        <div class="col-sm-6 col-xl-4">
            <div class="card-soft pump-card" data-pump-card="<?php echo htmlspecialchars($pump['key']); ?>" style="--pump-accent: <?php echo htmlspecialchars($pump['accent']); ?>; --pump-soft: <?php echo htmlspecialchars($pump['soft']); ?>;">
                <div class="pump-topline">
                    <div class="pump-icon">
                        <?php echo pump_icon($pump['name']); ?>
                    </div>
                    <span class="<?php echo htmlspecialchars(pumps_badge_class($pump['tone'])); ?>" data-pump-badge="<?php echo htmlspecialchars($pump['key']); ?>">
                        <?php echo htmlspecialchars($pump['status']); ?>
                    </span>
                </div>

                <div class="pump-title"><?php echo htmlspecialchars($pump['name']); ?></div>

                <div class="pump-state">
                    <span class="state-dot" data-pump-dot="<?php echo htmlspecialchars($pump['key']); ?>"></span>
                    <span class="state-label" data-pump-label="<?php echo htmlspecialchars($pump['key']); ?>"><?php echo htmlspecialchars($pump['status']); ?></span>
                </div>

                <p class="pump-description"><?php echo htmlspecialchars($pump['description']); ?></p>

                <div class="pump-meta">
                    <span>Control path</span>
                    <strong data-pump-power="<?php echo htmlspecialchars($pump['key']); ?>"><?php echo htmlspecialchars($pump['power']); ?></strong>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4 align-items-stretch pump-section">
    <div class="col-lg-8">
        <div class="card-soft control-card h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="pump-kicker mb-2">Control Panel</span>
                    <h5 class="mb-2">Device Actions</h5>
                    <p class="section-copy">Each control reflects the real database state. Live ON/OFF status stays visible in both auto and manual modes, while manual toggles remain locked during automatic mode.</p>
                </div>
                <span class="auto-mode-banner" id="pumpAutoIndicator">AUTO MODE ACTIVE</span>
            </div>

            <div class="control-grid">
                <?php foreach ($pumpCards as $pump): ?>
                    <div class="control-row">
                        <div class="control-copy">
                            <h6><?php echo htmlspecialchars($pump['name']); ?></h6>
                            <p><?php echo htmlspecialchars($pump['description']); ?></p>
                        </div>

                        <div class="button-group">
                            <span class="status-badge badge-neutral" data-pump-row-status="<?php echo htmlspecialchars($pump['key']); ?>">Waiting</span>
                            <button type="button" class="btn btn-soft action-btn is-off" data-pump-toggle="<?php echo htmlspecialchars($pump['key']); ?>">
                                Toggle
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-soft monitor-card h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="pump-kicker mb-2">Flow Monitor</span>
                    <h5 class="mb-2">Live Performance</h5>
                    <p class="section-copy">Key flow and efficiency indicators for the active pump network.</p>
                </div>
                <span class="status-badge" id="pumpControlLockBadge">Syncing</span>
            </div>

            <div class="metrics-grid">
                <?php foreach ($flowMetrics as $metric): ?>
                    <div class="metric-card">
                        <span><?php echo htmlspecialchars($metric['label']); ?></span>
                        <strong><?php echo $metric['label'] === 'Water Flow Rate' ? '<span id="pumpFlowRate">--</span>' : '<span id="pumpEfficiency">--</span>'; ?></strong>
                        <p><?php echo $metric['label'] === 'Water Flow Rate' ? '<span id="pumpFlowDetail">Waiting for live data</span>' : '<span id="pumpEfficiencyDetail">Waiting for live data</span>'; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 align-items-stretch pump-section">
    <div class="col-12">
        <div class="card-soft alerts-card">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="pump-kicker mb-2">Safety Alerts</span>
                    <h5 class="mb-2">Protection Notices</h5>
                    <p class="section-copy">Warnings that need attention before the next control cycle or prolonged pump runtime.</p>
                </div>
                <span class="status-badge badge-warning" id="pumpAlertsCount">0 Alerts</span>
            </div>

            <div class="alert-stack" id="pumpAlerts">
                <?php foreach ($alerts as $alert): ?>
                    <div class="alert-row">
                        <div class="alert-icon<?php echo $alert['tone'] === 'danger' ? ' danger' : ''; ?>">!</div>
                        <div class="alert-content">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-2">
                                <h6 class="mb-0"><?php echo htmlspecialchars($alert['title']); ?></h6>
                                <span class="<?php echo htmlspecialchars(pumps_badge_class($alert['tone'])); ?>">
                                    <?php echo htmlspecialchars(ucfirst($alert['tone'])); ?>
                                </span>
                            </div>
                            <p><?php echo htmlspecialchars($alert['detail']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var API_URL = '/aquaponics-dashboard/api/data.php';
        var PUMP_KEYS = ['heater', 'fan', 'irrigation', 'refill', 'filter'];
        var POLL_INTERVAL = 2500;

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

        function fetchJson(url, options) {
            return window.fetch(url, options || {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            }).then(function (response) {
                return response.json().catch(function () {
                    return {};
                }).then(function (payload) {
                    if (!response.ok) {
                        var error = new Error(payload.error || ('Request failed with status ' + response.status));
                        error.payload = payload;
                        throw error;
                    }

                    return payload;
                });
            });
        }

        function buildFormBody(values) {
            var body = new window.URLSearchParams();

            Object.keys(values).forEach(function (key) {
                if (typeof values[key] !== 'undefined' && values[key] !== null) {
                    body.append(key, values[key]);
                }
            });

            return body.toString();
        }

        function getSeverityBadgeClass(severity) {
            if (severity === 'critical') {
                return 'status-badge badge-danger';
            }

            if (severity === 'warning') {
                return 'status-badge badge-warning';
            }

            return 'status-badge';
        }

        function updatePumpUiFromState(pumps) {
            if (!pumps) {
                return;
            }

            var mode = pumps.mode === 'manual' ? 'manual' : 'auto';
            var activeCount = 0;

            PUMP_KEYS.forEach(function (pumpKey) {
                var value = Number(pumps[pumpKey]) === 1 ? 1 : 0;
                var label = document.querySelector('[data-pump-label="' + pumpKey + '"]');
                var dot = document.querySelector('[data-pump-dot="' + pumpKey + '"]');
                var card = document.querySelector('[data-pump-card="' + pumpKey + '"]');
                var badge = document.querySelector('[data-pump-badge="' + pumpKey + '"]');
                var rowStatus = document.querySelector('[data-pump-row-status="' + pumpKey + '"]');
                var button = document.querySelector('[data-pump-toggle="' + pumpKey + '"]');

                if (value === 1) {
                    activeCount += 1;
                }

                if (label) {
                    label.textContent = value === 1 ? 'ON' : 'OFF';
                }

                if (dot) {
                    dot.classList.toggle('is-on', value === 1);
                    dot.classList.toggle('is-danger', mode === 'auto');
                }

                if (card) {
                    card.classList.toggle('is-on', value === 1);
                    card.classList.toggle('is-danger', mode === 'auto');
                }

                if (badge) {
                    badge.textContent = value === 1 ? 'Active' : 'Idle';
                    badge.className = value === 1 ? 'status-badge' : 'status-badge badge-neutral';
                }

                if (rowStatus) {
                    rowStatus.textContent = value === 1 ? 'Running' : 'Stopped';
                    rowStatus.className = value === 1 ? 'status-badge' : 'status-badge badge-neutral';
                }

                if (button) {
                    button.disabled = mode === 'auto';
                    button.classList.remove('is-on', 'is-off', 'is-danger');
                    button.classList.add(mode === 'auto' ? 'is-danger' : (value === 1 ? 'is-on' : 'is-off'));
                    button.textContent = pumpKey.charAt(0).toUpperCase() + pumpKey.slice(1) + ' ' + (value === 1 ? 'ON' : 'OFF');
                }
            });

            var modeBadge = document.getElementById('pumpModeBadge');
            var autoIndicator = document.getElementById('pumpAutoIndicator');
            var lockBadge = document.getElementById('pumpControlLockBadge');
            var networkValue = document.getElementById('pumpNetworkValue');
            var networkDetail = document.getElementById('pumpNetworkDetail');

            if (modeBadge) {
                modeBadge.textContent = mode === 'auto' ? 'Auto' : 'Manual';
                modeBadge.className = mode === 'auto' ? 'status-badge badge-danger' : 'status-badge badge-neutral';
            }

            if (autoIndicator) {
                autoIndicator.textContent = mode === 'auto' ? 'AUTO MODE ACTIVE' : 'MANUAL MODE ACTIVE';
                autoIndicator.classList.toggle('is-manual', mode === 'manual');
            }

            if (lockBadge) {
                lockBadge.textContent = mode === 'auto' ? 'Manual Locked' : 'Manual Ready';
                lockBadge.className = mode === 'auto' ? 'status-badge badge-danger' : 'status-badge badge-neutral';
            }

            if (networkValue) {
                networkValue.textContent = activeCount + ' / ' + PUMP_KEYS.length;
            }

            if (networkDetail) {
                networkDetail.textContent = mode === 'auto'
                    ? 'ESP32 automatic logic is controlling outputs'
                    : 'Manual dashboard control is live from the database';
            }
        }

        function renderAlerts(alerts) {
            var alertsContainer = document.getElementById('pumpAlerts');
            var alertsCount = document.getElementById('pumpAlertsCount');
            var normalizedAlerts = Array.isArray(alerts) ? alerts : [];

            if (alertsCount) {
                alertsCount.textContent = normalizedAlerts.length + ' Alerts';
            }

            if (!alertsContainer) {
                return;
            }

            if (!normalizedAlerts.length) {
                alertsContainer.innerHTML = '<div class="alert-row"><div class="alert-icon">!</div><div class="alert-content"><div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-2"><h6 class="mb-0">No Active Alerts</h6><span class="status-badge">Normal</span></div><p>No live warnings were returned by the API.</p></div></div>';
                return;
            }

            alertsContainer.innerHTML = normalizedAlerts.map(function (alert) {
                var severity = String(alert.severity || 'warning').toLowerCase();
                var severityLabel = severity.charAt(0).toUpperCase() + severity.slice(1);

                return '<div class="alert-row">' +
                    '<div class="alert-icon' + (severity === 'critical' ? ' danger' : '') + '">!</div>' +
                    '<div class="alert-content">' +
                    '<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-2">' +
                    '<h6 class="mb-0">' + String(alert.message || 'Alert') + '</h6>' +
                    '<span class="' + getSeverityBadgeClass(severity) + '">' + severityLabel + '</span>' +
                    '</div>' +
                    '<p>Live alert received from the backend monitoring API.</p>' +
                    '</div>' +
                    '</div>';
            }).join('');
        }

        function updateMonitor(sensors, pumps) {
            var level = sensors ? Number(sensors.water_level || 0) : 0;
            var light = sensors ? Number(sensors.light || 0) : 0;
            var mode = pumps && pumps.mode === 'manual' ? 'manual' : 'auto';

            var flowRate = document.getElementById('pumpFlowRate');
            var flowDetail = document.getElementById('pumpFlowDetail');
            var efficiency = document.getElementById('pumpEfficiency');
            var efficiencyDetail = document.getElementById('pumpEfficiencyDetail');

            if (flowRate) {
                flowRate.textContent = level.toFixed(0) + '%';
            }

            if (flowDetail) {
                flowDetail.textContent = 'Current water level returned by the live API.';
            }

            if (efficiency) {
                efficiency.textContent = light.toFixed(0) + ' lux';
            }

            if (efficiencyDetail) {
                efficiencyDetail.textContent = mode === 'auto'
                    ? 'Automatic mode is following the current sensor-driven control path.'
                    : 'Manual mode is active while live sensor readings continue to update.';
            }
        }

        function refreshPumpDashboard() {
            fetchJson(API_URL, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            }).then(function (data) {
                updatePumpUiFromState(data.pumps || {});
                updateMonitor(data.sensors || {}, data.pumps || {});
                renderAlerts(data.alerts || []);
            }).catch(function (error) {
                console.error('Failed to refresh pump dashboard:', error);
            });
        }

        function bindPumpButtons() {
            PUMP_KEYS.forEach(function (pumpKey) {
                var button = document.querySelector('[data-pump-toggle="' + pumpKey + '"]');
                if (!button) {
                    return;
                }

                button.addEventListener('click', function () {
                    if (button.disabled) {
                        return;
                    }

                    var nextState = button.classList.contains('is-on') ? 0 : 1;
                    button.disabled = true;

                    fetchJson(API_URL + '?action=set_pump', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                        },
                        body: buildFormBody({
                            pump: pumpKey,
                            state: nextState
                        })
                    }).then(function () {
                        refreshPumpDashboard();
                    }).catch(function (error) {
                        console.error('Failed to update pump state for ' + pumpKey + ':', error);
                    }).finally(function () {
                        window.setTimeout(function () {
                            refreshPumpDashboard();
                        }, 150);
                    });
                });
            });
        }

        updateClock();
        setInterval(updateClock, 60000);
        bindPumpButtons();
        refreshPumpDashboard();
        setInterval(refreshPumpDashboard, POLL_INTERVAL);
    })();
</script>

<?php if ($isStandalone): ?>
    </div>
</div>
</body>
</html>
<?php endif; ?>
