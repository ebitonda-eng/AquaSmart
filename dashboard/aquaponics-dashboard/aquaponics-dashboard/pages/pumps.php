<?php
$scriptFile = isset($_SERVER['SCRIPT_FILENAME']) ? realpath($_SERVER['SCRIPT_FILENAME']) : null;
$isStandalone = $scriptFile && $scriptFile === realpath(__FILE__);

$pumpCards = [
    [
        'name' => 'Refill Pump',
        'status' => 'ON',
        'tone' => 'good',
        'description' => 'Reservoir refill cycle is active and supplying the main tank.',
        'accent' => '#0ea5e9',
        'soft' => 'rgba(14, 165, 233, 0.15)',
        'power' => '74%',
    ],
    [
        'name' => 'Circulation Pump',
        'status' => 'OFF',
        'tone' => 'danger',
        'description' => 'Circulation loop is paused pending the next scheduled cycle.',
        'accent' => '#f97316',
        'soft' => 'rgba(249, 115, 22, 0.16)',
        'power' => '0%',
    ],
];

$flowMetrics = [
    [
        'label' => 'Water Flow Rate',
        'value' => '12 L/min',
        'detail' => 'Measured across the active refill line.',
    ],
    [
        'label' => 'Pump Efficiency',
        'value' => '91%',
        'detail' => 'Efficiency remains above the weekly average.',
    ],
];

$alerts = [
    [
        'title' => 'Low water level',
        'detail' => 'Reservoir level dropped below the preferred buffer for the next irrigation cycle.',
        'tone' => 'warning',
    ],
    [
        'title' => 'Pump overload',
        'detail' => 'Circulation motor reported a high load event during the last runtime check.',
        'tone' => 'danger',
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
        if ($name === 'Refill Pump') {
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
        background: var(--pump-accent);
        box-shadow: 0 0 0 7px var(--pump-soft);
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
    }

    .btn-soft.action-btn {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 110px;
        border: 0;
        padding: 11px 20px;
        border-radius: 999px;
        font-weight: 600;
        letter-spacing: 0.01em;
        box-shadow: 0 14px 28px rgba(148, 163, 184, 0.18), inset 0 1px 0 rgba(255, 255, 255, 0.25);
        transition: transform 0.24s ease, box-shadow 0.24s ease, opacity 0.24s ease, filter 0.24s ease;
    }

    .btn-soft.action-btn::before {
        font-size: 13px;
        line-height: 1;
    }

    .btn-soft.action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 34px rgba(148, 163, 184, 0.22);
        filter: saturate(1.04);
    }

    .btn-start {
        background: linear-gradient(135deg, #22c55e, #4ade80);
        color: #ffffff;
    }

    .btn-start::before {
        content: "▶";
    }

    .btn-stop {
        background: linear-gradient(135deg, #ef4444, #fb7185);
        color: #ffffff;
    }

    .btn-stop::before {
        content: "■";
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

    .metric-card:hover,
    .alert-row:hover {
        border-color: rgba(203, 213, 225, 0.52);
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

    .alert-content p {
        color: #64748b;
        margin-bottom: 0;
        line-height: 1.5;
    }

    .section-copy {
        color: #64748b;
        margin-bottom: 0;
        line-height: 1.55;
    }

    .pump-card:hover .pump-icon {
        transform: translateY(-2px) scale(1.03);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.55), 0 18px 34px rgba(148, 163, 184, 0.18);
    }

    .pump-card:hover .status-badge,
    .control-row:hover .status-badge,
    .alert-row:hover .status-badge {
        transform: translateY(-1px);
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
        <p class="text-secondary mb-0">Track pump state, launch control actions, and monitor critical flow conditions across the water system.</p>
    </div>

    <div class="card-soft pump-overview">
        <span class="overview-label">Active Pump Network</span>
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <div class="overview-value">1 / 2</div>
                <small class="text-secondary">One pump currently running</small>
            </div>
            <span class="status-badge badge-neutral">Monitored</span>
        </div>
    </div>
</div>

<div class="row g-4 align-items-stretch">
    <?php foreach ($pumpCards as $pump): ?>
        <div class="col-lg-6">
            <div class="card-soft pump-card" style="--pump-accent: <?php echo htmlspecialchars($pump['accent']); ?>; --pump-soft: <?php echo htmlspecialchars($pump['soft']); ?>;">
                <div class="pump-topline">
                    <div class="pump-icon">
                        <?php echo pump_icon($pump['name']); ?>
                    </div>
                    <span class="<?php echo htmlspecialchars(pumps_badge_class($pump['tone'])); ?>">
                        <?php echo htmlspecialchars($pump['status']); ?>
                    </span>
                </div>

                <div class="pump-title"><?php echo htmlspecialchars($pump['name']); ?></div>

                <div class="pump-state">
                    <span class="state-dot"></span>
                    <span class="state-label"><?php echo htmlspecialchars($pump['status']); ?></span>
                </div>

                <p class="pump-description"><?php echo htmlspecialchars($pump['description']); ?></p>

                <div class="pump-meta">
                    <span>Motor load</span>
                    <strong><?php echo htmlspecialchars($pump['power']); ?></strong>
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
                    <h5 class="mb-2">Pump Actions</h5>
                    <p class="section-copy">Start or stop each pump from a single control surface with clear action states and modern button styling.</p>
                </div>
                <span class="status-badge">Remote Ready</span>
            </div>

            <div class="control-grid">
                <div class="control-row">
                    <div class="control-copy">
                        <h6>Refill Pump</h6>
                        <p>Use refill actions to top up the reservoir and maintain consistent supply volume.</p>
                    </div>

                    <div class="button-group">
                        <button type="button" class="btn btn-soft action-btn btn-start">Start</button>
                        <button type="button" class="btn btn-soft action-btn btn-stop">Stop</button>
                    </div>
                </div>

                <div class="control-row">
                    <div class="control-copy">
                        <h6>Circulation Pump</h6>
                        <p>Use circulation actions to drive continuous water movement through the grow loop.</p>
                    </div>

                    <div class="button-group">
                        <button type="button" class="btn btn-soft action-btn btn-start">Start</button>
                        <button type="button" class="btn btn-soft action-btn btn-stop">Stop</button>
                    </div>
                </div>
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
                <span class="status-badge">Live</span>
            </div>

            <div class="metrics-grid">
                <?php foreach ($flowMetrics as $metric): ?>
                    <div class="metric-card">
                        <span><?php echo htmlspecialchars($metric['label']); ?></span>
                        <strong><?php echo htmlspecialchars($metric['value']); ?></strong>
                        <p><?php echo htmlspecialchars($metric['detail']); ?></p>
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
                <span class="status-badge badge-warning">2 Alerts</span>
            </div>

            <div class="alert-stack">
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

<?php if ($isStandalone): ?>
    </div>
</div>
</body>
</html>
<?php endif; ?>
