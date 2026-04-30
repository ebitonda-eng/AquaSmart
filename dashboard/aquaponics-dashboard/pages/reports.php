<?php
$scriptFile = isset($_SERVER['SCRIPT_FILENAME']) ? realpath($_SERVER['SCRIPT_FILENAME']) : null;
$isStandalone = $scriptFile && $scriptFile === realpath(__FILE__);

$summaryCards = [
    [
        'title' => 'Avg Temperature',
        'value' => '--',
        'description' => 'Daily mean air temperature across the selected reporting range.',
        'accent' => '#0ea5e9',
        'soft' => 'rgba(14, 165, 233, 0.14)',
        'badge' => 'Daily',
        'tone' => 'neutral',
        'id' => 'reportAvgTemperature',
    ],
    [
        'title' => 'Avg Humidity',
        'value' => '--',
        'description' => 'Daily mean humidity prepared for environment analysis.',
        'accent' => '#14b8a6',
        'soft' => 'rgba(20, 184, 166, 0.14)',
        'badge' => 'Daily',
        'tone' => 'good',
        'id' => 'reportAvgHumidity',
    ],
    [
        'title' => 'Avg pH',
        'value' => '--',
        'description' => 'Daily mean pH across the selected database records.',
        'accent' => '#f97316',
        'soft' => 'rgba(249, 115, 22, 0.16)',
        'badge' => 'Daily',
        'tone' => 'warning',
        'id' => 'reportAvgPh',
    ],
    [
        'title' => 'Avg Ammonia',
        'value' => '--',
        'description' => 'Daily mean ammonia for water quality review and export.',
        'accent' => '#ef4444',
        'soft' => 'rgba(239, 68, 68, 0.14)',
        'badge' => 'Daily',
        'tone' => 'danger',
        'id' => 'reportAvgAmmonia',
    ],
];

$defaultTo = date('Y-m-d');
$defaultFrom = date('Y-m-d', strtotime('-29 days'));

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
        grid-template-columns: repeat(5, minmax(0, 1fr));
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
        transition: transform 0.24s ease, box-shadow 0.24s ease, filter 0.24s ease, opacity 0.24s ease;
    }

    .generate-btn:hover,
    .export-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 34px rgba(148, 163, 184, 0.22);
        filter: saturate(1.04);
    }

    .generate-btn:disabled,
    .export-btn:disabled {
        transform: none;
        box-shadow: none;
        opacity: 0.55;
        cursor: not-allowed;
        filter: none;
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
        white-space: nowrap;
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

    .report-alerts {
        display: grid;
        gap: 12px;
        margin-top: 18px;
    }

    .report-alert-item {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 16px;
        background: rgba(248, 250, 252, 0.9);
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 16px;
        box-shadow: 0 10px 24px rgba(148, 163, 184, 0.08);
    }

    .report-empty,
    .report-loading {
        padding: 16px;
        background: rgba(248, 250, 252, 0.9);
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 16px;
        color: #64748b;
    }

    .report-loading {
        color: #0f766e;
        font-weight: 600;
    }

    .table-stack {
        display: grid;
        gap: 24px;
    }

    .dataset-title {
        margin-bottom: 6px;
    }

    .dataset-copy {
        color: #64748b;
        margin-bottom: 16px;
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

    @media (max-width: 1399.98px) {
        .filter-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
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

<div id="reportSection">
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 reports-header">
    <div>
        <span class="reports-kicker">Reporting Center</span>
        <h2 class="mb-2">System Reports</h2>
        <p class="text-secondary mb-0">Generate structured daily datasets, isolate sensor groups, and export research-ready records directly from the database.</p>
    </div>

    <div class="card-soft reports-overview">
        <span class="overview-label">Current Window</span>
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <div class="overview-value" id="reportWindowLabel">--</div>
                <small class="text-secondary" id="reportWindowDetail">Waiting for report generation</small>
            </div>
            <span class="status-badge badge-neutral" id="reportStatusBadge">Ready</span>
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
                <div class="summary-value" id="<?php echo htmlspecialchars($card['id']); ?>"><?php echo htmlspecialchars($card['value']); ?></div>
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
                    <p class="panel-copy">Choose a date range, select the export dataset, and generate grouped daily records from the database.</p>
                </div>

                <span class="status-badge" id="reportDataStatus">Dataset Pending</span>
            </div>

            <div class="filter-grid">
                <div class="filter-group">
                    <label class="filter-label" for="reportFrom">From</label>
                    <input id="reportFrom" type="date" class="form-control" value="<?php echo htmlspecialchars($defaultFrom); ?>">
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="reportTo">To</label>
                    <input id="reportTo" type="date" class="form-control" value="<?php echo htmlspecialchars($defaultTo); ?>">
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="reportRange">Range</label>
                    <select id="reportRange" class="form-select">
                        <option value="today">Today</option>
                        <option value="week">Week</option>
                        <option value="month" selected>Month</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="reportDataset">Export Dataset</label>
                    <select id="reportDataset" class="form-select">
                        <option value="all" selected>All Data</option>
                        <option value="temperature">Only Temperature</option>
                        <option value="water_quality">Only Water Quality</option>
                        <option value="environment">Only Environment</option>
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
                    <span class="reports-kicker mb-2">Structured Dataset</span>
                    <h5 class="mb-2">Research Tables</h5>
                    <p class="panel-copy">Each row represents one day, grouped by database date and ready for analysis or filtered export.</p>
                </div>

                <span class="status-badge badge-neutral" id="reportRowCount">0 rows</span>
            </div>

            <div class="table-stack">
                <div>
                    <h6 class="dataset-title">Daily Full Dataset</h6>
                    <p class="dataset-copy">Daily grouped records with all primary sensor measures.</p>
                    <div class="table-responsive">
                        <table class="table reports-table align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Temp</th>
                                    <th scope="col">Humidity</th>
                                    <th scope="col">Water Temp</th>
                                    <th scope="col">pH</th>
                                    <th scope="col">Ammonia</th>
                                    <th scope="col">Light</th>
                                    <th scope="col">Water Level</th>
                                </tr>
                            </thead>
                            <tbody id="dailyTableBody">
                                <tr class="history-row">
                                    <td colspan="8"><div class="report-empty">Generate a report to load grouped daily data.</div></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <h6 class="dataset-title">Temperature Data Table</h6>
                    <p class="dataset-copy">Daily air and water temperature values for temperature-focused studies.</p>
                    <div class="table-responsive">
                        <table class="table reports-table align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Temperature</th>
                                    <th scope="col">Water Temperature</th>
                                </tr>
                            </thead>
                            <tbody id="temperatureTableBody">
                                <tr class="history-row">
                                    <td colspan="3"><div class="report-empty">No data available</div></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <h6 class="dataset-title">Water Quality Table</h6>
                    <p class="dataset-copy">Daily pH and ammonia readings isolated for water quality analysis.</p>
                    <div class="table-responsive">
                        <table class="table reports-table align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">pH</th>
                                    <th scope="col">Ammonia</th>
                                </tr>
                            </thead>
                            <tbody id="waterQualityTableBody">
                                <tr class="history-row">
                                    <td colspan="3"><div class="report-empty">No data available</div></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <h6 class="dataset-title">Environment Table</h6>
                    <p class="dataset-copy">Daily humidity and light records for environmental trend analysis.</p>
                    <div class="table-responsive">
                        <table class="table reports-table align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Humidity</th>
                                    <th scope="col">Light</th>
                                </tr>
                            </thead>
                            <tbody id="environmentTableBody">
                                <tr class="history-row">
                                    <td colspan="3"><div class="report-empty">No data available</div></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-soft export-card h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <span class="reports-kicker mb-2">Export</span>
                    <h5 class="mb-2">Download Options</h5>
                    <p class="panel-copy">Export the selected dataset as grouped CSV or as a structured PDF table.</p>
                </div>

                <span class="status-badge" id="reportExportStatus">Locked</span>
            </div>

            <div class="export-grid">
                <button type="button" class="btn btn-soft export-btn pdf-btn" id="downloadPdf" disabled>Download PDF</button>
                <button type="button" class="btn btn-soft export-btn csv-btn" id="exportCsv" disabled>Export CSV</button>
            </div>

            <div class="report-alerts" id="reportAlerts">
                <div class="report-empty">Report alerts will appear here after generation.</div>
            </div>
        </div>
    </div>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
    (function () {
        var reportData = {
            summary: null,
            daily: [],
            temperature: [],
            water_quality: [],
            environment: [],
            alerts: [],
            meta: null,
            loading: false,
            loaded: false
        };

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

        function formatNumber(value, decimals) {
            var numericValue = Number(value);
            if (!Number.isFinite(numericValue)) {
                numericValue = 0;
            }

            return numericValue.toFixed(decimals);
        }

        function formatDateForFilename(date) {
            var year = date.getFullYear();
            var month = String(date.getMonth() + 1).padStart(2, '0');
            var day = String(date.getDate()).padStart(2, '0');
            return year + '-' + month + '-' + day;
        }

        function updateRangeSelection() {
            var range = document.getElementById('reportRange');
            var fromInput = document.getElementById('reportFrom');
            var toInput = document.getElementById('reportTo');
            var now = new Date();
            var toDate = formatDateForFilename(now);
            var fromDate = toDate;

            if (!range || !fromInput || !toInput) {
                return;
            }

            if (range.value === 'week') {
                var weekDate = new Date(now);
                weekDate.setDate(now.getDate() - 6);
                fromDate = formatDateForFilename(weekDate);
            } else if (range.value === 'month') {
                var monthDate = new Date(now);
                monthDate.setDate(now.getDate() - 29);
                fromDate = formatDateForFilename(monthDate);
            }

            fromInput.value = fromDate;
            toInput.value = toDate;
        }

        function setText(id, value) {
            var element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        }

        function setButtonState(disabled) {
            var csvButton = document.getElementById('exportCsv');
            var pdfButton = document.getElementById('downloadPdf');

            if (csvButton) {
                csvButton.disabled = disabled;
            }

            if (pdfButton) {
                pdfButton.disabled = disabled;
            }
        }

        function renderSummary(summary) {
            setText('reportAvgTemperature', formatNumber(summary.avg_temperature, 2) + ' C');
            setText('reportAvgHumidity', formatNumber(summary.avg_humidity, 2) + '%');
            setText('reportAvgPh', formatNumber(summary.avg_ph, 2));
            setText('reportAvgAmmonia', formatNumber(summary.avg_ammonia, 2) + ' ppm');
        }

        function renderEmptyTable(tableId, colSpan, message, isLoading) {
            var tbody = document.getElementById(tableId);
            if (!tbody) {
                return;
            }

            tbody.innerHTML = '<tr class="history-row"><td colspan="' + colSpan + '"><div class="' + (isLoading ? 'report-loading' : 'report-empty') + '">' + message + '</div></td></tr>';
        }

        function renderTableRows(tableId, rows, columns, formatters) {
            var tbody = document.getElementById(tableId);
            if (!tbody) {
                return;
            }

            if (!rows || !rows.length) {
                renderEmptyTable(tableId, columns.length, 'No data available', false);
                return;
            }

            tbody.innerHTML = rows.map(function (row) {
                var cells = columns.map(function (column, index) {
                    var rawValue = row[column];
                    var value = formatters && formatters[index] ? formatters[index](rawValue, row) : rawValue;

                    if (index === 0) {
                        return '<td><div class="report-date">' + String(value) + '</div><div class="report-subtext">Grouped by day</div></td>';
                    }

                    return '<td><span class="value-chip">' + String(value) + '</span></td>';
                }).join('');

                return '<tr class="history-row">' + cells + '</tr>';
            }).join('');
        }

        function renderAlerts(alerts) {
            var container = document.getElementById('reportAlerts');
            if (!container) {
                return;
            }

            if (reportData.loading) {
                container.innerHTML = '<div class="report-loading">Generating report from database records...</div>';
                return;
            }

            if (!alerts || !alerts.length) {
                container.innerHTML = '<div class="report-empty">No report alerts for the selected range.</div>';
                return;
            }

            container.innerHTML = alerts.map(function (alert) {
                var badgeClass = alert.severity === 'critical' ? 'status-badge badge-danger' : 'status-badge badge-warning';
                var severityLabel = alert.severity ? alert.severity.charAt(0).toUpperCase() + alert.severity.slice(1) : 'Warning';

                return '<div class="report-alert-item">' +
                    '<div>' + String(alert.message || 'Alert') + '</div>' +
                    '<span class="' + badgeClass + '">' + severityLabel + '</span>' +
                '</div>';
            }).join('');
        }

        function updateMeta(meta, hasData) {
            var fromText = meta && meta.from ? meta.from : '--';
            var toText = meta && meta.to ? meta.to : '--';
            var rowCount = meta && typeof meta.row_count !== 'undefined' ? meta.row_count : 0;

            setText('reportWindowLabel', fromText + ' to ' + toText);
            setText('reportWindowDetail', reportData.loading ? 'Loading grouped daily data from database' : (hasData ? 'Prepared from grouped database records' : 'No data available for this range'));
            setText('reportStatusBadge', reportData.loading ? 'Loading' : (hasData ? 'Ready' : 'No Data'));
            setText('reportRowCount', rowCount + ' rows');
            setText('reportExportStatus', reportData.loading ? 'Preparing' : (hasData ? '2 Formats' : 'Locked'));
            setText('reportDataStatus', reportData.loading ? 'Generating' : (hasData ? 'Grouped Daily Data' : 'Dataset Pending'));
        }

        function renderReport(payload) {
            reportData.summary = payload.summary || null;
            reportData.daily = Array.isArray(payload.daily) ? payload.daily : [];
            reportData.temperature = Array.isArray(payload.temperature) ? payload.temperature : [];
            reportData.water_quality = Array.isArray(payload.water_quality) ? payload.water_quality : [];
            reportData.environment = Array.isArray(payload.environment) ? payload.environment : [];
            reportData.alerts = Array.isArray(payload.alerts) ? payload.alerts : [];
            reportData.meta = payload.meta || null;
            reportData.loading = false;
            reportData.loaded = true;

            if (reportData.summary) {
                renderSummary(reportData.summary);
            }

            renderTableRows(
                'dailyTableBody',
                reportData.daily,
                ['date', 'temperature', 'humidity', 'water_temperature', 'ph', 'ammonia', 'light', 'water_level'],
                [
                    null,
                    function (value) { return formatNumber(value, 2) + ' C'; },
                    function (value) { return formatNumber(value, 2) + '%'; },
                    function (value) { return formatNumber(value, 2) + ' C'; },
                    function (value) { return formatNumber(value, 2); },
                    function (value) { return formatNumber(value, 2) + ' ppm'; },
                    function (value) { return formatNumber(value, 2); },
                    function (value) { return formatNumber(value, 2) + '%'; }
                ]
            );

            renderTableRows(
                'temperatureTableBody',
                reportData.temperature,
                ['date', 'temperature', 'water_temperature'],
                [
                    null,
                    function (value) { return formatNumber(value, 2) + ' C'; },
                    function (value) { return formatNumber(value, 2) + ' C'; }
                ]
            );

            renderTableRows(
                'waterQualityTableBody',
                reportData.water_quality,
                ['date', 'ph', 'ammonia'],
                [
                    null,
                    function (value) { return formatNumber(value, 2); },
                    function (value) { return formatNumber(value, 2) + ' ppm'; }
                ]
            );

            renderTableRows(
                'environmentTableBody',
                reportData.environment,
                ['date', 'humidity', 'light'],
                [
                    null,
                    function (value) { return formatNumber(value, 2) + '%'; },
                    function (value) { return formatNumber(value, 2); }
                ]
            );

            renderAlerts(reportData.alerts);
            updateMeta(reportData.meta, reportData.daily.length > 0);
            setButtonState(reportData.daily.length === 0);
        }

        function renderLoadingState(from, to) {
            reportData.loading = true;
            reportData.loaded = false;

            renderEmptyTable('dailyTableBody', 8, 'Generating grouped daily data...', true);
            renderEmptyTable('temperatureTableBody', 3, 'Loading temperature dataset...', true);
            renderEmptyTable('waterQualityTableBody', 3, 'Loading water quality dataset...', true);
            renderEmptyTable('environmentTableBody', 3, 'Loading environment dataset...', true);
            renderAlerts([]);
            updateMeta({ from: from, to: to, row_count: 0 }, false);
            setButtonState(true);
        }

        function resetSummary() {
            setText('reportAvgTemperature', '--');
            setText('reportAvgHumidity', '--');
            setText('reportAvgPh', '--');
            setText('reportAvgAmmonia', '--');
        }

        function generateReport() {
            var fromInput = document.getElementById('reportFrom');
            var toInput = document.getElementById('reportTo');

            if (!fromInput || !toInput) {
                return;
            }

            var from = fromInput.value;
            var to = toInput.value;

            if (!from || !to) {
                resetSummary();
                renderEmptyTable('dailyTableBody', 8, 'No data available', false);
                renderEmptyTable('temperatureTableBody', 3, 'No data available', false);
                renderEmptyTable('waterQualityTableBody', 3, 'No data available', false);
                renderEmptyTable('environmentTableBody', 3, 'No data available', false);
                renderAlerts([{ message: 'No data available', severity: 'warning' }]);
                updateMeta({ from: from || '--', to: to || '--', row_count: 0 }, false);
                setButtonState(true);
                return;
            }

            renderLoadingState(from, to);

            fetch('/aquaponics-dashboard/api/reports.php?action=report&from=' + encodeURIComponent(from) + '&to=' + encodeURIComponent(to))
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Request failed with status ' + response.status);
                    }

                    return response.json();
                })
                .then(function (payload) {
                    renderReport(payload);
                })
                .catch(function (error) {
                    console.error('Failed to generate report:', error);
                    reportData.loading = false;
                    reportData.loaded = true;
                    resetSummary();
                    renderEmptyTable('dailyTableBody', 8, 'No data available', false);
                    renderEmptyTable('temperatureTableBody', 3, 'No data available', false);
                    renderEmptyTable('waterQualityTableBody', 3, 'No data available', false);
                    renderEmptyTable('environmentTableBody', 3, 'No data available', false);
                    renderAlerts([{ message: 'No data available', severity: 'warning' }]);
                    updateMeta({ from: from, to: to, row_count: 0 }, false);
                    setButtonState(true);
                });
        }

        function getSelectedDataset() {
            var dataset = document.getElementById('reportDataset');
            return dataset ? dataset.value : 'all';
        }

        function getExportConfig() {
            var selection = getSelectedDataset();

            if (selection === 'temperature') {
                return {
                    title: 'Temperature Data',
                    headers: ['date', 'temperature', 'water_temperature'],
                    pdfHead: [['Date', 'Temperature', 'Water Temp']],
                    rows: reportData.temperature.map(function (row) {
                        return [row.date, formatNumber(row.temperature, 2), formatNumber(row.water_temperature, 2)];
                    })
                };
            }

            if (selection === 'water_quality') {
                return {
                    title: 'Water Quality Data',
                    headers: ['date', 'ph', 'ammonia'],
                    pdfHead: [['Date', 'pH', 'Ammonia']],
                    rows: reportData.water_quality.map(function (row) {
                        return [row.date, formatNumber(row.ph, 2), formatNumber(row.ammonia, 2)];
                    })
                };
            }

            if (selection === 'environment') {
                return {
                    title: 'Environment Data',
                    headers: ['date', 'humidity', 'light'],
                    pdfHead: [['Date', 'Humidity', 'Light']],
                    rows: reportData.environment.map(function (row) {
                        return [row.date, formatNumber(row.humidity, 2), formatNumber(row.light, 2)];
                    })
                };
            }

            return {
                title: 'All Data',
                headers: ['date', 'temperature', 'humidity', 'water_temperature', 'ph', 'ammonia', 'light', 'water_level'],
                pdfHead: [['Date', 'Temp', 'Humidity', 'Water Temp', 'pH', 'Ammonia', 'Light', 'Water Level']],
                rows: reportData.daily.map(function (row) {
                    return [
                        row.date,
                        formatNumber(row.temperature, 2),
                        formatNumber(row.humidity, 2),
                        formatNumber(row.water_temperature, 2),
                        formatNumber(row.ph, 2),
                        formatNumber(row.ammonia, 2),
                        formatNumber(row.light, 2),
                        formatNumber(row.water_level, 2)
                    ];
                })
            };
        }

        function exportCsv() {
            if (!reportData.loaded) {
                return;
            }

            var config = getExportConfig();
            if (!config.rows.length) {
                return;
            }

            var csv = [config.headers].concat(config.rows).map(function (row) {
                return row.map(function (cell) {
                    return '"' + String(cell).replace(/"/g, '""') + '"';
                }).join(',');
            }).join('\n');

            var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            var url = URL.createObjectURL(blob);
            var link = document.createElement('a');
            link.href = url;
            link.download = 'report_' + getSelectedDataset() + '_' + formatDateForFilename(new Date()) + '.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        function exportPdf() {
            if (!reportData.loaded || !window.jspdf || !window.jspdf.jsPDF) {
                return;
            }

            var config = getExportConfig();
            if (!config.rows.length) {
                return;
            }

            var pdf = new window.jspdf.jsPDF('l', 'mm', 'a4');
            var meta = reportData.meta || {};
            var summary = reportData.summary || {};

            pdf.setFontSize(18);
            pdf.text('Aquaponics System Report', 14, 18);

            pdf.setFontSize(11);
            pdf.text('Date Range: ' + (meta.from || '--') + ' to ' + (meta.to || '--'), 14, 27);
            pdf.text('Dataset: ' + config.title, 14, 33);

            pdf.setFontSize(12);
            pdf.text('Summary', 14, 43);

            pdf.setFontSize(10);
            var summaryLines = [
                'Days: ' + String(summary.days || 0),
                'Avg Temp: ' + formatNumber(summary.avg_temperature, 2) + ' C',
                'Avg Humidity: ' + formatNumber(summary.avg_humidity, 2) + '%',
                'Avg Water Temp: ' + formatNumber(summary.avg_water_temperature, 2) + ' C',
                'Avg pH: ' + formatNumber(summary.avg_ph, 2),
                'Avg Ammonia: ' + formatNumber(summary.avg_ammonia, 2) + ' ppm',
                'Avg Light: ' + formatNumber(summary.avg_light, 2),
                'Avg Water Level: ' + formatNumber(summary.avg_water_level, 2) + '%'
            ];

            summaryLines.forEach(function (line, index) {
                pdf.text(line, 14, 50 + (index * 6));
            });

            pdf.autoTable({
                startY: 104,
                head: config.pdfHead,
                body: config.rows,
                styles: {
                    fontSize: 9,
                    cellPadding: 2.5
                },
                headStyles: {
                    fillColor: [15, 118, 110]
                },
                alternateRowStyles: {
                    fillColor: [244, 247, 250]
                },
                margin: {
                    left: 14,
                    right: 14
                }
            });

            pdf.save('report_' + getSelectedDataset() + '_' + formatDateForFilename(new Date()) + '.pdf');
        }

        function bindEvents() {
            var range = document.getElementById('reportRange');
            var generateButton = document.getElementById('generateReport');
            var csvButton = document.getElementById('exportCsv');
            var pdfButton = document.getElementById('downloadPdf');

            if (range) {
                range.addEventListener('change', updateRangeSelection);
            }

            if (generateButton) {
                generateButton.addEventListener('click', generateReport);
            }

            if (csvButton) {
                csvButton.addEventListener('click', exportCsv);
            }

            if (pdfButton) {
                pdfButton.addEventListener('click', exportPdf);
            }
        }

        updateClock();
        setInterval(updateClock, 60000);
        bindEvents();
        setButtonState(true);
        generateReport();
    })();
</script>
<script src="<?php echo $isStandalone ? '../assets/js/app.js' : 'assets/js/app.js'; ?>"></script>

<?php if ($isStandalone): ?>
    </div>
</div>
</body>
</html>
<?php endif; ?>
