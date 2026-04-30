<?php
$scriptFile = isset($_SERVER['SCRIPT_FILENAME']) ? realpath($_SERVER['SCRIPT_FILENAME']) : null;
$isStandalone = $scriptFile && $scriptFile === realpath(__FILE__);

$summaryCards = [
    [
        'title' => 'Avg Temperature',
        'value' => '24.2 C',
        'description' => 'Average grow-bed temperature across the selected reporting range.',
        'accent' => '#0ea5e9',
        'soft' => 'rgba(14, 165, 233, 0.14)',
        'badge' => 'Stable',
        'tone' => 'neutral',
    ],
    [
        'title' => 'Avg Humidity',
        'value' => '62%',
        'description' => 'Humidity stayed close to the greenhouse comfort target.',
        'accent' => '#14b8a6',
        'soft' => 'rgba(20, 184, 166, 0.14)',
        'badge' => 'Healthy',
        'tone' => 'good',
    ],
    [
        'title' => 'Avg pH',
        'value' => '6.8',
        'description' => 'The nutrient solution remained within the preferred balance zone.',
        'accent' => '#8b5cf6',
        'soft' => 'rgba(139, 92, 246, 0.14)',
        'badge' => 'Optimal',
        'tone' => 'good',
    ],
    [
        'title' => 'Total Water Usage',
        'value' => '1,286 L',
        'description' => 'Combined refill and circulation usage for the current reporting window.',
        'accent' => '#f59e0b',
        'soft' => 'rgba(245, 158, 11, 0.16)',
        'badge' => 'Monthly',
        'tone' => 'warning',
    ],
];

$reportRows = [
    ['date' => '2026-04-12', 'temperature' => '23.9 C', 'humidity' => '61%', 'ph' => '6.7', 'water' => '168 L'],
    ['date' => '2026-04-13', 'temperature' => '24.1 C', 'humidity' => '63%', 'ph' => '6.8', 'water' => '174 L'],
    ['date' => '2026-04-14', 'temperature' => '24.3 C', 'humidity' => '62%', 'ph' => '6.8', 'water' => '181 L'],
    ['date' => '2026-04-15', 'temperature' => '24.4 C', 'humidity' => '64%', 'ph' => '6.9', 'water' => '186 L'],
    ['date' => '2026-04-16', 'temperature' => '24.0 C', 'humidity' => '60%', 'ph' => '6.8', 'water' => '173 L'],
    ['date' => '2026-04-17', 'temperature' => '24.2 C', 'humidity' => '61%', 'ph' => '6.8', 'water' => '196 L'],
];

if (!function_exists('reports_badge_class')) {
    function reports_badge_class($tone)
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

if (!function_exists('render_reports_styles')) {
    function render_reports_styles()
    {
        echo <<<'CSS'
<style>
    .reports-shell {
        padding-bottom: 20px;
    }

    .reports-header {
        margin-bottom: 2rem;
    }

    .reports-kicker {
        display: inline-block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #0f766e;
        margin-bottom: 10px;
    }

    .reports-overview {
        min-width: 260px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.92), rgba(239, 246, 255, 0.94));
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 18px 40px rgba(148, 163, 184, 0.14);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .reports-overview:hover {
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

    .reports-section {
        margin-top: 0.75rem !important;
    }

    .reports-shell .row.align-items-stretch > [class*="col-"] {
        display: flex;
    }

    .reports-shell .row.align-items-stretch > [class*="col-"] > .card-soft {
        width: 100%;
    }

    .summary-card,
    .filter-card,
    .table-card,
    .export-card {
        position: relative;
        overflow: hidden;
        padding: 24px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 255, 255, 0.82));
        border: 1px solid rgba(255, 255, 255, 0.46);
        box-shadow: 0 16px 36px rgba(148, 163, 184, 0.13);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .summary-card:hover,
    .filter-card:hover,
    .table-card:hover,
    .export-card:hover,
    .filter-group:hover,
    .history-row:hover {
        transform: translateY(-6px);
        box-shadow: 0 22px 46px rgba(148, 163, 184, 0.18);
        border-color: rgba(203, 213, 225, 0.58);
    }

    .summary-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
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

    .summary-title {
        font-size: 0.95rem;
        color: #334155;
        margin-bottom: 10px;
        font-weight: 600;
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
        line-height: 1.55;
        max-width: 28ch;
    }

    .panel-copy {
        color: #64748b;
        margin-bottom: 0;
        line-height: 1.55;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-top: 18px;
    }

    .filter-group {
        padding: 16px;
        background: rgba(248, 250, 252, 0.86);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 18px;
        transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease;
    }

    .filter-label {
        display: block;
        font-size: 12px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 10px;
    }

    .filter-card .form-control,
    .filter-card .form-select {
        min-height: 46px;
        border-radius: 14px;
        border: 1px solid rgba(203, 213, 225, 0.9);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: none;
        color: #0f172a;
    }

    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.12);
    }

    .generate-btn,
    .export-btn {
        width: 100%;
        min-height: 46px;
        border: 0;
        border-radius: 999px;
        font-weight: 600;
        letter-spacing: 0.01em;
        box-shadow: 0 14px 28px rgba(148, 163, 184, 0.18), inset 0 1px 0 rgba(255, 255, 255, 0.25);
        transition: transform 0.24s ease, box-shadow 0.24s ease, filter 0.24s ease;
    }

    .generate-btn:hover,
    .export-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 34px rgba(148, 163, 184, 0.22);
        filter: saturate(1.04);
    }

    .generate-btn {
        background: linear-gradient(135deg, #14b8a6, #2dd4bf);
        color: #ffffff;
    }

    .export-btn.pdf-btn {
        background: linear-gradient(135deg, #ef4444, #fb7185);
        color: #ffffff;
    }

    .export-btn.csv-btn {
        background: linear-gradient(135deg, #2563eb, #60a5fa);
        color: #ffffff;
    }

    .reports-table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .reports-table thead th {
        border-bottom: 0;
        color: #64748b;
        font-size: 12px;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        font-weight: 600;
        padding: 0 0.75rem 1rem;
    }

    .reports-table > :not(caption) > * > * {
        background: transparent;
        border-bottom-color: transparent;
        padding: 1rem 0.75rem;
    }

    .reports-table tbody td {
        background: rgba(248, 250, 252, 0.9) !important;
        border-top: 1px solid rgba(226, 232, 240, 0.85);
        border-bottom: 1px solid rgba(226, 232, 240, 0.85);
        box-shadow: 0 10px 24px rgba(148, 163, 184, 0.08);
        transition: background-color 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
    }

    .reports-table tbody td:first-child {
        border-left: 1px solid rgba(226, 232, 240, 0.85);
        border-radius: 16px 0 0 16px;
    }

    .reports-table tbody td:last-child {
        border-right: 1px solid rgba(226, 232, 240, 0.85);
        border-radius: 0 16px 16px 0;
    }

    .reports-table tbody tr:hover td {
        background: rgba(255, 255, 255, 0.98) !important;
        border-color: rgba(203, 213, 225, 0.9);
        box-shadow: 0 16px 30px rgba(148, 163, 184, 0.12);
    }

    .report-date {
        font-weight: 600;
        color: #0f172a;
    }

    .report-subtext {
        color: #64748b;
        font-size: 13px;
    }

    .value-chip {
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

    .export-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-top: 18px;
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

    @media (max-width: 1199.98px) {
        .filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .reports-overview {
            width: 100%;
        }
    }

    @media (max-width: 767.98px) {
        .export-grid,
        .filter-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575.98px) {
        .summary-card,
        .filter-card,
        .table-card,
        .export-card {
            padding: 20px;
        }

        .summary-value,
        .overview-value {
            font-size: 1.85rem;
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
    <title>Reports | AquaSmart Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <?php render_reports_styles(); ?>
</head>
<body>
<?php include __DIR__ . '/../components/sidebar.php'; ?>
<?php include __DIR__ . '/../components/navbar.php'; ?>
<div class="main-content">
    <div class="container-fluid reports-shell">
<?php else: ?>
<?php render_reports_styles(); ?>
<?php endif; ?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 reports-header">
    <div>
        <span class="reports-kicker">Reporting Center</span>
        <h2 class="mb-2">System Reports</h2>
        <p class="text-secondary mb-0">Review historical performance, filter report windows, and export clean summaries for your aquaponics operation.</p>
    </div>

    <div class="card-soft reports-overview">
        <span class="overview-label">Current Window</span>
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <div class="overview-value">30 Days</div>
                <small class="text-secondary">Prepared for monthly reporting</small>
            </div>
            <span class="status-badge badge-neutral">Ready</span>
        </div>
    </div>
</div>

<div class="row g-4 align-items-stretch">
    <?php foreach ($summaryCards as $card): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="card-soft summary-card" style="--summary-accent: <?php echo htmlspecialchars($card['accent']); ?>; --summary-soft: <?php echo htmlspecialchars($card['soft']); ?>;">
                <div class="summary-topline">
                    <div class="summary-spark"></div>
                    <span class="<?php echo htmlspecialchars(reports_badge_class($card['tone'])); ?>">
                        <?php echo htmlspecialchars($card['badge']); ?>
                    </span>
                </div>

                <div class="summary-title"><?php echo htmlspecialchars($card['title']); ?></div>
                <div class="summary-value"><?php echo htmlspecialchars($card['value']); ?></div>
                <p class="summary-description"><?php echo htmlspecialchars($card['description']); ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4 align-items-stretch reports-section">
    <div class="col-12">
        <div class="card-soft filter-card">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <span class="reports-kicker mb-2">Filters</span>
                    <h5 class="mb-2">Report Filters</h5>
                    <p class="panel-copy">Choose a reporting window, adjust the date range, and generate an updated table for export.</p>
                </div>

                <span class="status-badge">Custom Range</span>
            </div>

            <div class="filter-grid">
                <div class="filter-group">
                    <label class="filter-label" for="reportFrom">From</label>
                    <input id="reportFrom" type="date" class="form-control" value="2026-04-01">
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="reportTo">To</label>
                    <input id="reportTo" type="date" class="form-control" value="2026-04-18">
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="reportRange">Range</label>
                    <select id="reportRange" class="form-select">
                        <option>Today</option>
                        <option>Week</option>
                        <option selected>Month</option>
                    </select>
                </div>

                <div class="filter-group d-flex flex-column justify-content-end">
                    <label class="filter-label" for="generateReport">Action</label>
                    <button id="generateReport" type="button" class="btn btn-soft generate-btn">Generate Report</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 align-items-stretch reports-section">
    <div class="col-lg-8">
        <div class="card-soft table-card h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="reports-kicker mb-2">Report Table</span>
                    <h5 class="mb-2">Daily Performance Summary</h5>
                    <p class="panel-copy">A structured view of temperature, humidity, pH, and water usage across the selected dates.</p>
                </div>

                <span class="status-badge badge-neutral"><?php echo htmlspecialchars((string) count($reportRows)); ?> rows</span>
            </div>

            <div class="table-responsive">
                <table class="table reports-table align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Temperature</th>
                            <th scope="col">Humidity</th>
                            <th scope="col">pH</th>
                            <th scope="col">Water Usage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportRows as $row): ?>
                            <tr class="history-row">
                                <td>
                                    <div class="report-date"><?php echo htmlspecialchars($row['date']); ?></div>
                                    <div class="report-subtext">Sensor archive</div>
                                </td>
                                <td><span class="value-chip"><?php echo htmlspecialchars($row['temperature']); ?></span></td>
                                <td><span class="value-chip"><?php echo htmlspecialchars($row['humidity']); ?></span></td>
                                <td><span class="value-chip"><?php echo htmlspecialchars($row['ph']); ?></span></td>
                                <td><span class="value-chip"><?php echo htmlspecialchars($row['water']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-soft export-card h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="reports-kicker mb-2">Export</span>
                    <h5 class="mb-2">Download Options</h5>
                    <p class="panel-copy">Export the current report selection for sharing, archiving, or external analysis.</p>
                </div>

                <span class="status-badge">2 Formats</span>
            </div>

            <div class="export-grid">
                <button type="button" class="btn btn-soft export-btn pdf-btn">Download PDF</button>
                <button type="button" class="btn btn-soft export-btn csv-btn">Export CSV</button>
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
