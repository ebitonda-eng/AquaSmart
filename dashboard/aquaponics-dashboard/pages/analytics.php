<?php
$scriptFile = isset($_SERVER['SCRIPT_FILENAME']) ? realpath($_SERVER['SCRIPT_FILENAME']) : null;
$isStandalone = $scriptFile && $scriptFile === realpath(__FILE__);

$summaryCards = [
    [
        'title' => 'Average Temperature',
        'value' => '--',
        'description' => 'Steady across grow beds over the last 24 hours.',
        'accent' => '#0ea5e9',
        'soft' => 'rgba(14, 165, 233, 0.14)',
        'badge' => 'Stable',
        'tone' => 'neutral',
    ],
    [
        'title' => 'Average Humidity',
        'value' => '--',
        'description' => 'Holding near the greenhouse comfort range.',
        'accent' => '#14b8a6',
        'soft' => 'rgba(20, 184, 166, 0.14)',
        'badge' => 'Healthy',
        'tone' => 'good',
    ],
    [
        'title' => 'Average pH',
        'value' => '--',
        'description' => 'Nutrient solution remains within target limits.',
        'accent' => '#8b5cf6',
        'soft' => 'rgba(139, 92, 246, 0.14)',
        'badge' => 'Optimal',
        'tone' => 'good',
    ],
    [
        'title' => 'Water Usage',
        'value' => '--',
        'description' => 'Daily consumption is trending slightly upward.',
        'accent' => '#f59e0b',
        'soft' => 'rgba(245, 158, 11, 0.16)',
        'badge' => '+8%',
        'tone' => 'warning',
    ],
];

$insights = [
    [
        'title' => 'Temperature stable',
        'description' => 'Temperature stayed within a tight 1.2 C range, indicating steady environmental control.',
        'tone' => 'good',
        'label' => 'Low variance',
    ],
    [
        'title' => 'Water usage increased',
        'description' => 'Usage rose during the afternoon cycle, likely tied to stronger lighting and evapotranspiration.',
        'tone' => 'warning',
        'label' => 'Monitor trend',
    ],
    [
        'title' => 'pH within optimal range',
        'description' => 'pH remained centered around 6.8, supporting nutrient absorption and root health.',
        'tone' => 'neutral',
        'label' => 'On target',
    ],
];

if (!function_exists('analytics_badge_class')) {
    function analytics_badge_class($tone)
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

if (!function_exists('render_analytics_styles')) {
    function render_analytics_styles()
    {
        echo <<<'CSS'
<style>
    .analytics-shell {
        padding-bottom: 20px;
    }

    .analytics-header {
        margin-bottom: 2rem;
    }

    .analytics-kicker {
        display: inline-block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #0f766e;
        margin-bottom: 10px;
    }

    .analytics-overview {
        min-width: 250px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(236, 253, 245, 0.94));
        box-shadow: 0 14px 32px rgba(148, 163, 184, 0.12);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .analytics-overview:hover {
        transform: translateY(-5px);
        box-shadow: 0 18px 38px rgba(148, 163, 184, 0.16);
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

    .summary-card {
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        padding: 24px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 255, 255, 0.82));
        border: 1px solid rgba(255, 255, 255, 0.44);
        box-shadow: 0 14px 32px rgba(148, 163, 184, 0.12);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
        isolation: isolate;
    }

    .summary-card::before {
        content: "";
        position: absolute;
        inset: auto -28px -36px auto;
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: radial-gradient(circle, var(--summary-soft), rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
    }

    .summary-card::after {
        content: "";
        position: absolute;
        top: 0;
        left: 24px;
        right: 24px;
        height: 3px;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--summary-accent), rgba(255, 255, 255, 0));
        opacity: 0.85;
    }

    .summary-card:hover,
    .chart-card:hover,
    .insights-panel:hover,
    .insight-card:hover,
    .meta-tile:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 40px rgba(148, 163, 184, 0.16);
    }

    .summary-topline {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
    }

    .summary-spark {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--summary-soft), rgba(255, 255, 255, 0.98));
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--summary-accent);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.55);
    }

    .summary-spark::after {
        content: "";
        width: 22px;
        height: 22px;
        border-radius: 10px;
        border: 2px solid currentColor;
        border-top-color: transparent;
        transform: rotate(-28deg);
    }

    .summary-card h6 {
        font-size: 0.95rem;
        color: #334155;
        margin-bottom: 10px;
    }

    .summary-value {
        font-size: 2.15rem;
        font-weight: 700;
        line-height: 1;
        color: #0f172a;
        margin-bottom: 12px;
        letter-spacing: -0.03em;
    }

    .summary-description {
        color: #64748b;
        margin: 0;
        max-width: 28ch;
        line-height: 1.55;
    }

    .chart-card {
        position: relative;
        overflow: hidden;
        padding: 24px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.94), rgba(255, 255, 255, 0.8));
        box-shadow: 0 14px 34px rgba(148, 163, 184, 0.12);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .chart-card::before,
    .insights-panel::before {
        content: "";
        position: absolute;
        inset: 0 0 auto 0;
        height: 120px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.55), rgba(255, 255, 255, 0));
        pointer-events: none;
    }

    .chart-stage {
        position: relative;
        height: 360px;
        margin-top: 6px;
    }

    .secondary-chart-stage {
        position: relative;
        height: 280px;
        margin-top: 4px;
    }

    .chart-legend {
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
        color: #475569;
        font-size: 13px;
        transition: background-color 0.24s ease, border-color 0.24s ease, transform 0.24s ease;
    }

    .legend-chip:hover {
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 0.96);
        border-color: rgba(148, 163, 184, 0.22);
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .chart-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 14px;
        margin-top: 24px;
    }

    .meta-tile {
        background: rgba(248, 250, 252, 0.85);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 16px;
        padding: 14px 16px;
        transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease;
    }

    .meta-tile span {
        display: block;
        font-size: 12px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 6px;
    }

    .meta-tile strong {
        color: #0f172a;
        font-size: 1.15rem;
    }

    .panel-note {
        color: #64748b;
        margin-bottom: 0;
        line-height: 1.55;
    }

    .doughnut-wrap {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 18px;
    }

    .distribution-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .distribution-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding-top: 12px;
        border-top: 1px solid rgba(148, 163, 184, 0.12);
    }

    .distribution-item:first-child {
        border-top: 0;
        padding-top: 0;
    }

    .distribution-name {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #334155;
        font-weight: 600;
    }

    .distribution-value {
        color: #0f172a;
        font-weight: 700;
    }

    .insights-panel {
        position: relative;
        overflow: hidden;
        padding: 24px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 255, 255, 0.82));
        box-shadow: 0 14px 34px rgba(148, 163, 184, 0.12);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .insight-card {
        height: 100%;
        background: rgba(248, 250, 252, 0.72);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 18px;
        padding: 18px;
        transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease;
    }

    .insight-card h6 {
        color: #0f172a;
        margin-bottom: 10px;
        font-size: 1rem;
    }

    .insight-card p {
        color: #64748b;
        margin-bottom: 0;
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

    #time {
        color: #64748b !important;
        font-weight: 500;
    }

    .analytics-section {
        margin-top: 0.75rem !important;
    }

    .analytics-shell .row.align-items-stretch > [class*="col-"] {
        display: flex;
    }

    .analytics-shell .row.align-items-stretch > [class*="col-"] > .card-soft,
    .analytics-shell .row.align-items-stretch > [class*="col-"] > .insight-card {
        width: 100%;
    }

    @media (max-width: 991.98px) {
        .analytics-overview {
            width: 100%;
        }

        .chart-meta {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575.98px) {
        .summary-value {
            font-size: 1.9rem;
        }

        .summary-card,
        .chart-card,
        .insights-panel {
            padding: 20px;
        }

        .chart-stage {
            height: 300px;
        }

        .secondary-chart-stage {
            height: 240px;
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
    <title>Analytics | AquaSmart Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <?php render_analytics_styles(); ?>
</head>
<body>
<?php include __DIR__ . '/../components/sidebar.php'; ?>
<?php include __DIR__ . '/../components/navbar.php'; ?>
<div class="main-content">
    <div class="container-fluid analytics-shell">
<?php else: ?>
<?php render_analytics_styles(); ?>
<?php endif; ?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 analytics-header">
    <div>
        <span class="analytics-kicker">Analytics Center</span>
        <h2 class="mb-2">System Analytics</h2>
        <p class="text-secondary mb-0">Monitor long-term trends, compare resource usage, and surface operational insights from your sensor network.</p>
    </div>

    <div class="card-soft analytics-overview">
        <span class="overview-label">Today's Efficiency Score</span>
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <div class="overview-value" id="analyticsEfficiency">--</div>
                <small class="text-secondary" id="analyticsEfficiencyDetail">Waiting for live data</small>
            </div>
            <span class="status-badge">Healthy</span>
        </div>
    </div>
</div>

<div class="row g-4 align-items-stretch">
    <?php foreach ($summaryCards as $card): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="card-soft summary-card" style="--summary-accent: <?php echo htmlspecialchars($card['accent']); ?>; --summary-soft: <?php echo htmlspecialchars($card['soft']); ?>;">
                <div class="summary-topline">
                    <div class="summary-spark"></div>
                    <span class="<?php echo htmlspecialchars(analytics_badge_class($card['tone'])); ?>">
                        <?php echo htmlspecialchars($card['badge']); ?>
                    </span>
                </div>

                <h6><?php echo htmlspecialchars($card['title']); ?></h6>
                <div class="summary-value"><?php
                    if ($card['title'] === 'Average Temperature') {
                        echo '<span id="analyticsAvgTemp">--</span>';
                    } elseif ($card['title'] === 'Average Humidity') {
                        echo '<span id="analyticsAvgHumidity">--</span>';
                    } elseif ($card['title'] === 'Average pH') {
                        echo '<span id="analyticsAvgPh">--</span>';
                    } else {
                        echo '<span id="analyticsWaterUsage">--</span>';
                    }
                ?></div>
                <p class="summary-description"><?php echo htmlspecialchars($card['description']); ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4 align-items-stretch analytics-section">
    <div class="col-12">
        <div class="card-soft chart-card">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="analytics-kicker mb-2">Trend Monitoring</span>
                    <h5 class="mb-2">Environmental Trend Analysis</h5>
                    <p class="text-secondary mb-0">A smooth multi-metric view of Temperature, Water Temperature, Humidity, pH, and Ammonia activity over the last seven days.</p>
                </div>

                <div class="chart-legend">
                    <span class="legend-chip"><span class="legend-dot" style="background:#22c55e;"></span>Temperature</span>
                    <span class="legend-chip"><span class="legend-dot" style="background:#2563eb;"></span>Water Temperature</span>
                    <span class="legend-chip"><span class="legend-dot" style="background:#14b8a6;"></span>Humidity</span>
                    <span class="legend-chip"><span class="legend-dot" style="background:#8b5cf6;"></span>pH</span>
                    <span class="legend-chip"><span class="legend-dot" style="background:#f97316;"></span>Ammonia</span>
                </div>
            </div>

            <div class="chart-stage">
                <canvas id="analyticsTrendChart"></canvas>
            </div>

            <div class="chart-meta">
                <div class="meta-tile">
                    <span>Peak Temperature</span>
                    <strong id="analyticsPeakTemperature">--</strong>
                </div>
                <div class="meta-tile">
                    <span>Water Temp Avg</span>
                    <strong id="analyticsWaterTempAvg">--</strong>
                </div>
                <div class="meta-tile">
                    <span>Humidity Range</span>
                    <strong id="analyticsHumidityRange">--</strong>
                </div>
                <div class="meta-tile">
                    <span>Average pH Drift</span>
                    <strong id="analyticsPhDrift">--</strong>
                </div>
                <div class="meta-tile">
                    <span>Ammonia Range</span>
                    <strong id="analyticsAmmoniaRange">--</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 align-items-stretch analytics-section">
    <div class="col-lg-7">
        <div class="card-soft chart-card h-100">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="analytics-kicker mb-2">Resource Tracking</span>
                    <h5 class="mb-2">Water Usage Over Time</h5>
                    <p class="panel-note">Daily water draw across the most recent seven operational cycles.</p>
                </div>
                <span class="status-badge badge-warning">Usage +8%</span>
            </div>

            <div class="secondary-chart-stage">
                <canvas id="waterUsageChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-soft chart-card h-100">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="analytics-kicker mb-2">Distribution</span>
                    <h5 class="mb-2">Sensor Distribution</h5>
                    <p class="panel-note">Relative reporting emphasis across the key environmental sensors.</p>
                </div>
                <span class="status-badge badge-neutral">Balanced</span>
            </div>

            <div class="doughnut-wrap">
                <div class="secondary-chart-stage">
                    <canvas id="distributionChart"></canvas>
                </div>

                <div class="distribution-list">
                    <div class="distribution-item">
                        <div class="distribution-name">
                            <span class="legend-dot" style="background:#22c55e;"></span>
                            Temperature
                        </div>
                        <div class="distribution-value">24%</div>
                    </div>
                    <div class="distribution-item">
                        <div class="distribution-name">
                            <span class="legend-dot" style="background:#14b8a6;"></span>
                            Humidity
                        </div>
                        <div class="distribution-value">20%</div>
                    </div>
                    <div class="distribution-item">
                        <div class="distribution-name">
                            <span class="legend-dot" style="background:#8b5cf6;"></span>
                            pH
                        </div>
                        <div class="distribution-value">18%</div>
                    </div>
                    <div class="distribution-item">
                        <div class="distribution-name">
                            <span class="legend-dot" style="background:#2563eb;"></span>
                            Water Temp
                        </div>
                        <div class="distribution-value">22%</div>
                    </div>
                    <div class="distribution-item">
                        <div class="distribution-name">
                            <span class="legend-dot" style="background:#f97316;"></span>
                            Ammonia
                        </div>
                        <div class="distribution-value">16%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 align-items-stretch analytics-section">
    <div class="col-12">
        <div class="card-soft insights-panel">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="analytics-kicker mb-2">Insights</span>
                    <h5 class="mb-2">Operational Insights</h5>
                    <p class="text-secondary mb-0">Quick takeaways from the latest analytics pass across environment and resource behavior.</p>
                </div>

                <span class="status-badge">3 Active Insights</span>
            </div>

            <div class="row g-3">
                <?php foreach ($insights as $insight): ?>
                    <div class="col-lg-4">
                        <div class="insight-card">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <h6 class="mb-0"><?php echo htmlspecialchars($insight['title']); ?></h6>
                                <span class="<?php echo htmlspecialchars(analytics_badge_class($insight['tone'])); ?>">
                                    <?php echo htmlspecialchars($insight['label']); ?>
                                </span>
                            </div>
                            <p><?php echo htmlspecialchars($insight['description']); ?></p>
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

        function createCharts() {
            if (typeof window.Chart === 'undefined') {
                return;
            }

            var sharedGridColor = 'rgba(148, 163, 184, 0.08)';
            var sharedTickColor = '#64748b';
            var sharedTooltip = {
                backgroundColor: '#0f172a',
                titleColor: '#ffffff',
                bodyColor: '#e2e8f0',
                padding: 12,
                cornerRadius: 14,
                borderColor: 'rgba(255, 255, 255, 0.08)',
                borderWidth: 1,
                displayColors: true
            };

            var trendCanvas = document.getElementById('analyticsTrendChart');
            var waterCanvas = document.getElementById('waterUsageChart');
            var distributionCanvas = document.getElementById('distributionChart');

            if (trendCanvas) {
                var trendContext = trendCanvas.getContext('2d');
                var trendGradient = trendContext.createLinearGradient(0, 0, 0, trendCanvas.height || 360);
                trendGradient.addColorStop(0, 'rgba(34, 197, 94, 0.18)');
                trendGradient.addColorStop(1, 'rgba(34, 197, 94, 0.01)');

                var existingTrend = window.Chart.getChart(trendCanvas);
                if (existingTrend) {
                    existingTrend.destroy();
                }

                new window.Chart(trendCanvas, {
                    type: 'line',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [
                            {
                                label: 'Temperature',
                                data: [23.8, 24.1, 24.0, 24.6, 24.9, 24.5, 24.3],
                                yAxisID: 'tempAxis',
                                borderColor: '#22c55e',
                                backgroundColor: trendGradient,
                                borderWidth: 3,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                                pointHoverBackgroundColor: '#22c55e',
                                pointHoverBorderColor: '#ffffff',
                                pointHoverBorderWidth: 2,
                                fill: true,
                                cubicInterpolationMode: 'monotone',
                                tension: 0.42
                            },
                            {
                                label: 'Water Temperature',
                                data: [25.4, 25.6, 25.8, 26.0, 26.2, 26.1, 26.0],
                                yAxisID: 'tempAxis',
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37, 99, 235, 0.08)',
                                borderWidth: 2.8,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                                pointHoverBackgroundColor: '#2563eb',
                                pointHoverBorderColor: '#ffffff',
                                pointHoverBorderWidth: 2,
                                fill: false,
                                cubicInterpolationMode: 'monotone',
                                tension: 0.42
                            },
                            {
                                label: 'Humidity',
                                data: [63, 61, 64, 62, 60, 59, 61],
                                yAxisID: 'humidityAxis',
                                borderColor: '#14b8a6',
                                backgroundColor: 'rgba(20, 184, 166, 0.08)',
                                borderWidth: 2.6,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                                pointHoverBackgroundColor: '#14b8a6',
                                pointHoverBorderColor: '#ffffff',
                                pointHoverBorderWidth: 2,
                                fill: false,
                                cubicInterpolationMode: 'monotone',
                                tension: 0.42
                            },
                            {
                                label: 'pH',
                                data: [6.7, 6.8, 6.8, 6.9, 6.8, 6.7, 6.8],
                                yAxisID: 'phAxis',
                                borderColor: '#8b5cf6',
                                backgroundColor: 'rgba(139, 92, 246, 0.08)',
                                borderWidth: 2.6,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                                pointHoverBackgroundColor: '#8b5cf6',
                                pointHoverBorderColor: '#ffffff',
                                pointHoverBorderWidth: 2,
                                fill: false,
                                cubicInterpolationMode: 'monotone',
                                tension: 0.42
                            },
                            {
                                label: 'Ammonia',
                                data: [0.18, 0.24, 0.28, 0.41, 0.56, 0.62, 0.58],
                                yAxisID: 'ammoniaAxis',
                                borderColor: '#f97316',
                                backgroundColor: 'rgba(249, 115, 22, 0.08)',
                                borderWidth: 2.6,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                                pointHoverBackgroundColor: '#f97316',
                                pointHoverBorderColor: '#ffffff',
                                pointHoverBorderWidth: 2,
                                fill: false,
                                cubicInterpolationMode: 'monotone',
                                tension: 0.42
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                top: 6,
                                right: 8,
                                bottom: 0,
                                left: 4
                            }
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: sharedTooltip
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
                                    color: sharedTickColor,
                                    padding: 10
                                }
                            },
                            tempAxis: {
                                type: 'linear',
                                position: 'left',
                                min: 20,
                                max: 28,
                                grid: {
                                    color: sharedGridColor,
                                    drawTicks: false,
                                    drawBorder: false
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    color: sharedTickColor,
                                    maxTicksLimit: 5,
                                    padding: 12,
                                    callback: function (value) {
                                        return value + ' C';
                                    }
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
                                type: 'linear',
                                position: 'right',
                                min: 0,
                                max: 1.2,
                                grid: {
                                    drawOnChartArea: false
                                },
                                ticks: {
                                    color: '#f97316',
                                    maxTicksLimit: 4,
                                    padding: 12,
                                    callback: function (value) {
                                        return value + ' ppm';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            if (waterCanvas) {
                var waterContext = waterCanvas.getContext('2d');
                var waterGradient = waterContext.createLinearGradient(0, 0, 0, waterCanvas.height || 280);
                waterGradient.addColorStop(0, 'rgba(20, 184, 166, 0.9)');
                waterGradient.addColorStop(1, 'rgba(125, 211, 177, 0.72)');

                var existingWater = window.Chart.getChart(waterCanvas);
                if (existingWater) {
                    existingWater.destroy();
                }

                new window.Chart(waterCanvas, {
                    type: 'bar',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [
                            {
                                label: 'Water Usage',
                                data: [142, 156, 149, 163, 172, 181, 186],
                                backgroundColor: waterGradient,
                                hoverBackgroundColor: '#14b8a6',
                                borderRadius: 14,
                                borderSkipped: false,
                                maxBarThickness: 34
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                top: 8,
                                right: 4,
                                bottom: 0,
                                left: 4
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: sharedTooltip
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
                                    color: sharedTickColor,
                                    padding: 10
                                }
                            },
                            y: {
                                grid: {
                                    color: sharedGridColor,
                                    drawTicks: false,
                                    drawBorder: false
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    color: sharedTickColor,
                                    maxTicksLimit: 4,
                                    padding: 12,
                                    callback: function (value) {
                                        return value + ' L';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            if (distributionCanvas) {
                var existingDistribution = window.Chart.getChart(distributionCanvas);
                if (existingDistribution) {
                    existingDistribution.destroy();
                }

                new window.Chart(distributionCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Temperature', 'Humidity', 'pH', 'Water Temperature', 'Ammonia'],
                        datasets: [
                            {
                                data: [24, 20, 18, 22, 16],
                                backgroundColor: ['#22c55e', '#14b8a6', '#8b5cf6', '#2563eb', '#f97316'],
                                borderColor: '#ffffff',
                                borderWidth: 5,
                                hoverOffset: 6
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: sharedTooltip
                        }
                    }
                });
            }
        }

        function initCharts() {
            if (typeof window.Chart !== 'undefined') {
                createCharts();
                return;
            }

            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = createCharts;
            document.head.appendChild(script);
        }

        updateClock();
        setInterval(updateClock, 60000);
        initCharts();
    })();
</script>
<script src="<?php echo $isStandalone ? '../assets/js/app.js' : 'assets/js/app.js'; ?>"></script>

<?php if ($isStandalone): ?>
    </div>
</div>
</body>
</html>
<?php endif; ?>
