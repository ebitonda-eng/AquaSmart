(function (window, document) {
    'use strict';

    var AquaSmart = window.AquaSmart || {};
    var API_URL = '/aquaponics-dashboard/api/data.php';
    var POLL_INTERVAL = 3000;
    var MAX_HISTORY = 12;
    var liveUpdateIntervalId = null;
    var pumpSyncIntervalId = null;
    var pumpStateStore = null;

    var pumpKeys = ['heater', 'fan', 'irrigation', 'refill', 'filter'];
    var pumpLabels = {
        heater: 'Heater',
        fan: 'Fan',
        irrigation: 'Irrigation',
        refill: 'Refill',
        filter: 'Filtration'
    };

    var historyStore = {
        labels: [],
        temperature: [],
        humidity: [],
        water_temperature: [],
        ph: [],
        ammonia: [],
        light: [],
        water_level: []
    };

    function getElement(id) {
        return typeof id === 'string' ? document.getElementById(id) : null;
    }

    function hasChartJs() {
        return typeof window.Chart !== 'undefined' && typeof window.Chart.getChart === 'function';
    }

    function hasProgressBar() {
        return typeof window.ProgressBar !== 'undefined' && typeof window.ProgressBar.Circle === 'function';
    }

    function getSavedTheme() {
        try {
            return window.localStorage.getItem('theme');
        } catch (error) {
            return null;
        }
    }

    function setSavedTheme(mode) {
        try {
            window.localStorage.setItem('theme', mode);
        } catch (error) {
            // Ignore storage failures so the dashboard still works.
        }
    }

    function applyTheme(mode) {
        if (!document.body) {
            return;
        }

        document.body.classList.toggle('dark-mode', mode === 'dark');
        setSavedTheme(mode);
    }

    function createVerticalGradient(context, topColor, bottomColor, fallbackColor) {
        if (!context || typeof context.createLinearGradient !== 'function') {
            return fallbackColor;
        }

        var gradient = context.createLinearGradient(0, 0, 0, 320);
        gradient.addColorStop(0, topColor);
        gradient.addColorStop(1, bottomColor);
        return gradient;
    }

    function initTempGauge() {
        var gaugeElement = getElement('tempGauge');

        if (!gaugeElement || !hasProgressBar()) {
            return null;
        }

        if (AquaSmart.tempGauge) {
            return AquaSmart.tempGauge;
        }

        AquaSmart.tempGauge = new window.ProgressBar.Circle(gaugeElement, {
            color: '#22c55e',
            trailColor: '#e2e8f0',
            strokeWidth: 10,
            duration: 1500
        });

        AquaSmart.tempGauge.animate(0);
        return AquaSmart.tempGauge;
    }

    function initSharedDashboardChart() {
        var chartCanvas = getElement('chart');
        var chartInstance = null;

        if (!chartCanvas || !hasChartJs()) {
            return {
                chart: null,
                created: false
            };
        }

        chartInstance = window.Chart.getChart(chartCanvas);
        if (chartInstance) {
            AquaSmart.dashboardChart = chartInstance;
            return {
                chart: chartInstance,
                created: false
            };
        }

        var context = chartCanvas.getContext ? chartCanvas.getContext('2d') : null;
        var gradient = createVerticalGradient(
            context,
            'rgba(34, 197, 94, 0.18)',
            'rgba(34, 197, 94, 0.02)',
            'rgba(34, 197, 94, 0.12)'
        );

        AquaSmart.dashboardChart = new window.Chart(chartCanvas, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Temperature',
                    data: [],
                    borderColor: '#22c55e',
                    backgroundColor: gradient,
                    fill: true,
                    borderWidth: 3,
                    pointRadius: 0,
                    tension: 0.4
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        display: false
                    },
                    y: {
                        display: false
                    }
                }
            }
        });

        return {
            chart: AquaSmart.dashboardChart,
            created: true
        };
    }

    function initLegacyAnalyticsChart() {
        var chartCanvas = getElement('analyticsChart');
        var chartInstance = null;

        if (!chartCanvas || !hasChartJs()) {
            return {
                chart: null,
                created: false
            };
        }

        chartInstance = window.Chart.getChart(chartCanvas);
        if (chartInstance) {
            AquaSmart.analyticsChart = chartInstance;
            return {
                chart: chartInstance,
                created: false
            };
        }

        AquaSmart.analyticsChart = new window.Chart(chartCanvas, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Temperature',
                        data: [],
                        borderColor: '#22c55e',
                        tension: 0.4
                    },
                    {
                        label: 'Humidity',
                        data: [],
                        borderColor: '#3b82f6',
                        tension: 0.4
                    },
                    {
                        label: 'pH',
                        data: [],
                        borderColor: '#f59e0b',
                        tension: 0.4
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });

        return {
            chart: AquaSmart.analyticsChart,
            created: true
        };
    }

    function updateTextContent(elementId, value) {
        var element = getElement(elementId);
        if (element) {
            element.textContent = value;
        }
    }

    function updateNodeListValue(selector, value) {
        if (!selector) {
            return;
        }

        var nodes = document.querySelectorAll(selector);
        if (!nodes.length) {
            return;
        }

        nodes.forEach(function (node) {
            node.textContent = value;
        });
    }

    function clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    function toNumber(value) {
        var numericValue = Number(value);
        return Number.isFinite(numericValue) ? numericValue : 0;
    }

    function formatValue(value, suffix, decimals) {
        var number = toNumber(value);
        var precision = typeof decimals === 'number' ? decimals : 1;
        return number.toFixed(precision) + (suffix || '');
    }

    function formatPercent(value, decimals) {
        return formatValue(value, '%', typeof decimals === 'number' ? decimals : 0);
    }

    function formatTimestamp(timestamp) {
        if (!timestamp) {
            return 'No timestamp';
        }

        var date = new Date(String(timestamp).replace(' ', 'T'));
        if (Number.isNaN(date.getTime())) {
            return timestamp;
        }

        return date.toLocaleString([], {
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function formatRelativeTime(timestamp) {
        if (!timestamp) {
            return 'No timestamp';
        }

        var date = new Date(String(timestamp).replace(' ', 'T'));
        if (Number.isNaN(date.getTime())) {
            return timestamp;
        }

        var diffSeconds = Math.max(0, Math.round((Date.now() - date.getTime()) / 1000));
        if (diffSeconds < 60) {
            return 'Updated just now';
        }

        var diffMinutes = Math.round(diffSeconds / 60);
        if (diffMinutes < 60) {
            return 'Updated ' + diffMinutes + ' min ago';
        }

        var diffHours = Math.round(diffMinutes / 60);
        return 'Updated ' + diffHours + ' hr ago';
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

    function getAlertIconClass(severity) {
        return severity === 'critical' ? 'alert-icon critical' : 'alert-icon warning';
    }

    function getAlertTileClass(severity) {
        return severity === 'critical' ? 'tone-critical' : 'tone-warning';
    }

    function ensureHistoryPoint(label, sensors) {
        historyStore.labels.push(label);
        historyStore.temperature.push(toNumber(sensors.temperature));
        historyStore.humidity.push(toNumber(sensors.humidity));
        historyStore.water_temperature.push(toNumber(sensors.water_temperature));
        historyStore.ph.push(toNumber(sensors.ph));
        historyStore.ammonia.push(toNumber(sensors.ammonia));
        historyStore.light.push(toNumber(sensors.light));
        historyStore.water_level.push(clamp(toNumber(sensors.water_level), 0, 100));

        Object.keys(historyStore).forEach(function (key) {
            if (historyStore[key].length > MAX_HISTORY) {
                historyStore[key].shift();
            }
        });
    }

    function updateCommonSensorValues(sensors) {
        updateTextContent('temp', formatValue(sensors.temperature, '\u00B0C', 1));
        updateTextContent('humidity', formatPercent(sensors.humidity, 0));
        updateTextContent('water_temp', formatValue(sensors.water_temperature, '\u00B0C', 1));
        updateTextContent('ph', formatValue(sensors.ph, '', 1));
        updateTextContent('ammonia', formatValue(sensors.ammonia, ' ppm', 2));
        updateTextContent('light', formatValue(sensors.light, ' lux', 0));
        updateTextContent('water_level', formatPercent(sensors.water_level, 0));

        updateNodeListValue('[data-field="temperature"]', formatValue(sensors.temperature, '\u00B0C', 1));
        updateNodeListValue('[data-field="humidity"]', formatPercent(sensors.humidity, 0));
        updateNodeListValue('[data-field="water_temperature"]', formatValue(sensors.water_temperature, '\u00B0C', 1));
        updateNodeListValue('[data-field="ph"]', formatValue(sensors.ph, '', 1));
        updateNodeListValue('[data-field="ammonia"]', formatValue(sensors.ammonia, ' ppm', 2));
        updateNodeListValue('[data-field="light"]', formatValue(sensors.light, ' lux', 0));
        updateNodeListValue('[data-field="water_level"]', formatPercent(sensors.water_level, 0));
        updateNodeListValue('[data-field="timestamp"]', formatTimestamp(sensors.timestamp));
        updateNodeListValue('[data-field="updated_relative"]', formatRelativeTime(sensors.timestamp));

        updateNodeListValue('[data-water-level-badge]', formatPercent(sensors.water_level, 0));
        updateNodeListValue('[data-water-level-text]', formatPercent(sensors.water_level, 0) + ' available');
    }

    function updateWaterLevelVisual(sensors) {
        var level = clamp(toNumber(sensors.water_level), 0, 100);
        var waterBar = getElement('waterBar');

        if (waterBar) {
            waterBar.style.width = level + '%';
            waterBar.setAttribute('aria-valuenow', String(level));
        }
    }

    function updateSystemStatus(sensors, alerts) {
        var hasAlerts = Array.isArray(alerts) && alerts.length > 0;
        var healthScore = Math.max(0, 100 - ((alerts && alerts.length ? alerts.length : 0) * 10));

        updateNodeListValue('[data-field="system_status"]', hasAlerts ? 'Attention Needed' : 'Normal');
        updateNodeListValue('[data-field="active_alerts_count"]', String(hasAlerts ? alerts.length : 0));
        updateNodeListValue('[data-field="timestamp_status"]', formatRelativeTime(sensors.timestamp));
        updateTextContent('system_health_score', healthScore + '%');
    }

    function renderDashboardAlerts(alerts) {
        var container = getElement('dashboardAlerts');
        var countBadge = getElement('dashboardAlertCount');

        if (countBadge) {
            countBadge.textContent = (alerts && alerts.length ? alerts.length : 0) + ' Active';
        }

        if (!container) {
            return;
        }

        if (!alerts || !alerts.length) {
            container.innerHTML = '<div class="dashboard-alert"><div class="alert-icon warning">!</div><div><div class="d-flex justify-content-between align-items-start gap-2 mb-2"><strong>No Active Alerts</strong><span class="status-badge">Normal</span></div><p>All monitored API alerts are currently clear.</p></div></div>';
            return;
        }

        container.innerHTML = alerts.map(function (alert) {
            var severity = String(alert.severity || 'warning').toLowerCase();
            var severityLabel = severity.charAt(0).toUpperCase() + severity.slice(1);

            return '<div class="dashboard-alert">' +
                '<div class="' + getAlertIconClass(severity) + '">!</div>' +
                '<div>' +
                '<div class="d-flex justify-content-between align-items-start gap-2 mb-2">' +
                '<strong>' + String(alert.message || 'Alert') + '</strong>' +
                '<span class="' + getSeverityBadgeClass(severity) + '">' + severityLabel + '</span>' +
                '</div>' +
                '<p>Reported from the live aquaponics monitoring API.</p>' +
                '</div>' +
                '</div>';
        }).join('');
    }

    function renderPumpAlerts(alerts) {
        var container = getElement('pumpAlerts');
        var badge = getElement('pumpAlertsCount');

        if (badge) {
            badge.textContent = (alerts && alerts.length ? alerts.length : 0) + ' Alerts';
        }

        if (!container) {
            return;
        }

        if (!alerts || !alerts.length) {
            container.innerHTML = '<div class="alert-row"><div class="alert-icon">!</div><div class="alert-content"><div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-2"><h6 class="mb-0">No Active Alerts</h6><span class="status-badge">Normal</span></div><p>No live warnings were returned by the API.</p></div></div>';
            return;
        }

        container.innerHTML = alerts.map(function (alert) {
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

    function renderAlertsPage(alerts) {
        var grid = getElement('activeAlertsGrid');
        var historyBody = getElement('alertHistoryBody');
        var activeCount = alerts && alerts.length ? alerts.length : 0;
        var criticalCount = 0;
        var warningCount = 0;
        var health = 'Good';

        if (alerts) {
            alerts.forEach(function (alert) {
                var severity = String(alert.severity || 'warning').toLowerCase();
                if (severity === 'critical') {
                    criticalCount += 1;
                    health = 'Critical';
                } else {
                    warningCount += 1;
                    if (health !== 'Critical') {
                        health = 'Warning';
                    }
                }
            });
        }

        updateTextContent('alertsOverviewCount', String(activeCount));
        updateTextContent('alertsOverviewHealth', health);
        updateTextContent('alertsSystemHealth', health);
        updateTextContent('alertsTotalToday', String(activeCount));
        updateTextContent('alertsActiveCount', String(activeCount));
        updateTextContent('alertsCriticalCount', String(criticalCount));
        updateTextContent('alertsWarningCount', String(warningCount));
        updateTextContent('alertsLowCount', '0');
        updateTextContent('alertsHistoryCount', activeCount + ' logged today');
        updateTextContent('alertsSystemBadge', health + ' System');

        if (grid) {
            if (!alerts || !alerts.length) {
                grid.innerHTML = '<div class="alert-tile tone-warning" style="--alert-accent:#0ea5e9; --alert-soft:rgba(14,165,233,0.16);"><div class="alert-topline"><div class="alert-icon">&#9888;&#65039;</div><span class="status-badge">Normal</span></div><div class="alert-message">No Active Alerts</div><p class="alert-detail">The backend API did not return any open incidents.</p><div class="alert-meta"><span><strong>System</strong> status</span><span>Live</span></div></div>';
            } else {
                grid.innerHTML = alerts.map(function (alert) {
                    var severity = String(alert.severity || 'warning').toLowerCase();
                    var severityLabel = severity.charAt(0).toUpperCase() + severity.slice(1);
                    var accent = severity === 'critical' ? '#ef4444' : '#f59e0b';
                    var soft = severity === 'critical' ? 'rgba(239, 68, 68, 0.16)' : 'rgba(245, 158, 11, 0.16)';

                    return '<div class="alert-tile ' + getAlertTileClass(severity) + '" style="--alert-accent:' + accent + '; --alert-soft:' + soft + ';">' +
                        '<div class="alert-topline">' +
                        '<div class="alert-icon">&#9888;&#65039;</div>' +
                        '<span class="' + getSeverityBadgeClass(severity) + '">' + severityLabel + '</span>' +
                        '</div>' +
                        '<div class="alert-message">' + String(alert.message || 'Alert') + '</div>' +
                        '<p class="alert-detail">Live alert generated by the backend rule engine.</p>' +
                        '<div class="alert-meta"><span><strong>API</strong> alert</span><span>Live</span></div>' +
                        '</div>';
                }).join('');
            }
        }

        if (historyBody) {
            if (!alerts || !alerts.length) {
                historyBody.innerHTML = '<tr><td><div class="history-entry"><span class="history-icon tone-resolved">&#9888;&#65039;</span><div><div class="history-alert">No Active Alerts</div><div class="history-subtext">The API returned an empty alert array.</div></div></div></td><td><span class="type-chip">System</span></td><td class="text-secondary">Live</td><td><span class="status-badge badge-resolved">Resolved</span></td></tr>';
            } else {
                historyBody.innerHTML = alerts.map(function (alert) {
                    var severity = String(alert.severity || 'warning').toLowerCase();
                    var severityLabel = severity.charAt(0).toUpperCase() + severity.slice(1);

                    return '<tr>' +
                        '<td><div class="history-entry"><span class="history-icon ' + getAlertTileClass(severity) + '">&#9888;&#65039;</span><div><div class="history-alert">' + String(alert.message || 'Alert') + '</div><div class="history-subtext">Live API alert</div></div></div></td>' +
                        '<td><span class="type-chip">API</span></td>' +
                        '<td class="text-secondary">Live</td>' +
                        '<td><span class="' + getSeverityBadgeClass(severity) + '">' + severityLabel + '</span></td>' +
                        '</tr>';
                }).join('');
            }
        }
    }

    function updateDashboardWidgets(sensors) {
        updateTextContent('farmWidgetTemp', formatValue(sensors.temperature, '\u00B0C', 1));
        updateTextContent('farmWidgetHumidity', formatPercent(sensors.humidity, 0));
        updateTextContent('farmWidgetPh', formatValue(sensors.ph, '', 1));
    }

    function updatePumpPage(sensors) {
        var level = clamp(toNumber(sensors.water_level), 0, 100);
        updateTextContent('pumpFlowRate', formatPercent(level, 0));
        updateTextContent('pumpFlowDetail', 'Current water level returned by the live API.');
        updateTextContent('pumpEfficiency', formatValue(sensors.light, ' lux', 0));
        updateTextContent('pumpEfficiencyDetail', 'Latest light sensor reading from the API feed.');
    }

    function updateSensorsPage(sensors, alerts) {
        updateTextContent('sensorNetworkHealth', alerts && alerts.length ? 'Warning' : 'Stable');
        updateTextContent('sensorNetworkHealthDetail', formatRelativeTime(sensors.timestamp));
        updateTextContent('sensorOnlineCount', '1 Online');
        updateTextContent('sensorOfflineCount', '0');
        updateTextContent('sensorLatencyValue', formatRelativeTime(sensors.timestamp));
    }

    function updateAnalyticsSummary(sensors, alerts) {
        updateTextContent('analyticsEfficiency', alerts && alerts.length ? 'Warning' : 'Healthy');
        updateTextContent('analyticsEfficiencyDetail', formatRelativeTime(sensors.timestamp));
        updateTextContent('analyticsAvgTemp', formatValue(sensors.temperature, '\u00B0C', 1));
        updateTextContent('analyticsAvgHumidity', formatPercent(sensors.humidity, 0));
        updateTextContent('analyticsAvgPh', formatValue(sensors.ph, '', 1));
        updateTextContent('analyticsWaterUsage', formatPercent(sensors.water_level, 0));
        updateTextContent('analyticsPeakTemperature', formatValue(sensors.temperature, '\u00B0C', 1));
        updateTextContent('analyticsWaterTempAvg', formatValue(sensors.water_temperature, '\u00B0C', 1));
        updateTextContent('analyticsHumidityRange', formatPercent(sensors.humidity, 0));
        updateTextContent('analyticsPhDrift', formatValue(sensors.ph, '', 1));
        updateTextContent('analyticsAmmoniaRange', formatValue(sensors.ammonia, ' ppm', 2));
    }

    function getPumpDisplayName(key) {
        return pumpLabels[key] || key;
    }

    function normalizePumpPayload(pumps) {
        var payload = pumps || {};
        var normalized = {
            mode: payload.mode === 'manual' ? 'manual' : 'auto',
            updated_at: payload.updated_at || null
        };

        pumpKeys.forEach(function (key) {
            normalized[key] = toNumber(payload[key]) > 0 ? 1 : 0;
        });

        return normalized;
    }

    function updatePumpButton(button, pumpKey, pumpState) {
        if (!button) {
            return;
        }

        var isAuto = pumpState.mode === 'auto';
        var isOn = pumpState[pumpKey] === 1;

        button.disabled = isAuto;
        button.classList.remove('is-on', 'is-off', 'is-danger');
        button.classList.add(isAuto ? 'is-danger' : (isOn ? 'is-on' : 'is-off'));
        button.textContent = getPumpDisplayName(pumpKey) + ' ' + (isOn ? 'ON' : 'OFF');
        button.setAttribute('aria-pressed', isOn ? 'true' : 'false');
    }

    function updatePumpModeIndicators(mode, updatedAt) {
        var isAuto = mode === 'auto';
        var dashboardIndicator = getElement('dashboardPumpModeIndicator');
        var dashboardStatusText = getElement('dashboardPumpStatusText');
        var pumpAutoIndicator = getElement('pumpAutoIndicator');
        var pumpModeBadge = getElement('pumpModeBadge');
        var pumpControlLockBadge = getElement('pumpControlLockBadge');
        var settingsBadge = getElement('settingsPumpModeBadge');
        var pumpModeTitle = getElement('pumpModeTitle');
        var pumpModeText = getElement('pumpModeText');
        var pumpModeToggle = getElement('pumpModeToggle');

        if (dashboardIndicator) {
            dashboardIndicator.textContent = isAuto ? 'AUTO MODE ACTIVE' : 'MANUAL MODE ACTIVE';
            dashboardIndicator.classList.toggle('is-manual', !isAuto);
        }

        if (dashboardStatusText) {
            dashboardStatusText.textContent = isAuto
                ? 'Auto mode is active. Manual dashboard controls are locked.'
                : 'Manual mode is active. Dashboard toggles can control hardware.';
        }

        if (pumpAutoIndicator) {
            pumpAutoIndicator.textContent = isAuto ? 'AUTO MODE ACTIVE' : 'MANUAL MODE ACTIVE';
            pumpAutoIndicator.classList.toggle('is-manual', !isAuto);
        }

        if (pumpModeBadge) {
            pumpModeBadge.textContent = isAuto ? 'Auto' : 'Manual';
            pumpModeBadge.className = isAuto ? 'status-badge badge-danger' : 'status-badge badge-neutral';
        }

        if (pumpControlLockBadge) {
            pumpControlLockBadge.textContent = isAuto ? 'Manual Locked' : 'Manual Ready';
            pumpControlLockBadge.className = isAuto ? 'status-badge badge-danger' : 'status-badge badge-neutral';
        }

        if (settingsBadge) {
            settingsBadge.textContent = isAuto ? 'Auto Mode' : 'Manual Mode';
            settingsBadge.className = isAuto ? 'status-badge badge-danger' : 'status-badge badge-neutral';
        }

        if (pumpModeTitle) {
            pumpModeTitle.textContent = isAuto ? 'Auto Mode Active' : 'Manual Mode Active';
        }

        if (pumpModeText) {
            pumpModeText.textContent = isAuto
                ? 'Manual pump controls are disabled while the ESP32 follows automatic sensor logic.'
                : 'Manual pump controls are enabled and the dashboard can write relay states to the database.';
        }

        if (pumpModeToggle) {
            if (pumpModeToggle.getAttribute('data-pending-mode') !== 'true') {
                pumpModeToggle.checked = isAuto;
            }
        }

        updateNodeListValue('[data-pump-updated]', updatedAt ? formatRelativeTime(updatedAt) : 'Waiting for sync');
    }

    function updatePumpStateUI(pumps) {
        var pumpState = normalizePumpPayload(pumps);
        var activeCount = 0;

        pumpStateStore = pumpState;

        pumpKeys.forEach(function (key) {
            var isOn = pumpState[key] === 1;
            var isAuto = pumpState.mode === 'auto';
            var card = document.querySelector('[data-pump-card="' + key + '"]');
            var badge = document.querySelector('[data-pump-badge="' + key + '"]');
            var label = document.querySelector('[data-pump-label="' + key + '"]');
            var dot = document.querySelector('[data-pump-dot="' + key + '"]');
            var rowStatus = document.querySelector('[data-pump-row-status="' + key + '"]');
            var power = document.querySelector('[data-pump-power="' + key + '"]');

            if (isOn) {
                activeCount += 1;
            }

            document.querySelectorAll('[data-pump-toggle="' + key + '"]').forEach(function (button) {
                updatePumpButton(button, key, pumpState);
            });

            if (card) {
                card.classList.toggle('is-on', isOn);
                card.classList.toggle('is-danger', isAuto);
            }

            if (badge) {
                badge.textContent = isOn ? 'ON' : 'OFF';
                badge.className = isOn ? 'status-badge' : (isAuto ? 'status-badge badge-danger' : 'status-badge badge-neutral');
            }

            if (label) {
                label.textContent = isOn ? 'ON' : 'OFF';
            }

            if (dot) {
                dot.classList.toggle('is-on', isOn);
                dot.classList.toggle('is-danger', isAuto);
            }

            if (rowStatus) {
                rowStatus.textContent = isOn ? 'Running' : 'Stopped';
                rowStatus.className = isOn ? 'status-badge' : 'status-badge badge-neutral';
            }

            if (power) {
                power.textContent = isAuto ? 'ESP32 Auto' : 'Manual DB';
            }
        });

        updateTextContent('pumpNetworkValue', activeCount + ' / ' + pumpKeys.length);
        updateTextContent(
            'pumpNetworkDetail',
            pumpState.mode === 'auto'
                ? 'ESP32 automatic logic is controlling outputs'
                : 'Manual dashboard control is live from the database'
        );
        updatePumpModeIndicators(pumpState.mode, pumpState.updated_at);
    }

    function updateSharedAndPageSpecificUI(payload) {
        if (!payload || !payload.sensors) {
            return;
        }

        var sensors = payload.sensors;
        var alerts = Array.isArray(payload.alerts) ? payload.alerts : [];

        updateCommonSensorValues(sensors);
        updateWaterLevelVisual(sensors);
        updateSystemStatus(sensors, alerts);
        updateDashboardWidgets(sensors);
        updatePumpPage(sensors);
        updateSensorsPage(sensors, alerts);
        updateAnalyticsSummary(sensors, alerts);
        renderDashboardAlerts(alerts);
        renderPumpAlerts(alerts);
        renderAlertsPage(alerts);

        if (payload.pumps) {
            updatePumpStateUI(payload.pumps);
        }

        if (AquaSmart.tempGauge) {
            AquaSmart.tempGauge.animate(clamp(toNumber(sensors.temperature) / 40, 0, 1));
        }
    }

    function updateDashboardChart() {
        var chart = AquaSmart.dashboardChart;
        if (!chart || !chart.data || !chart.data.datasets || !chart.data.datasets[0]) {
            return;
        }

        chart.data.labels = historyStore.labels.slice();
        chart.data.datasets[0].data = historyStore.temperature.slice();
        chart.update();
    }

    function updateLegacyAnalyticsChart() {
        var chart = AquaSmart.analyticsChart;
        if (!chart || !chart.data || !chart.data.datasets) {
            return;
        }

        chart.data.labels = historyStore.labels.slice();
        if (chart.data.datasets[0]) {
            chart.data.datasets[0].data = historyStore.temperature.slice();
        }
        if (chart.data.datasets[1]) {
            chart.data.datasets[1].data = historyStore.humidity.slice();
        }
        if (chart.data.datasets[2]) {
            chart.data.datasets[2].data = historyStore.ph.slice();
        }
        chart.update();
    }

    function updateChartById(chartId, labels, datasets) {
        var canvas = getElement(chartId);
        if (!canvas || !hasChartJs()) {
            return;
        }

        var chart = window.Chart.getChart(canvas);
        if (!chart) {
            return;
        }

        chart.data.labels = labels.slice();
        chart.data.datasets.forEach(function (dataset, index) {
            if (datasets[index]) {
                dataset.data = datasets[index].slice();
            }
        });
        chart.update();
    }

    function updateCharts() {
        updateDashboardChart();
        updateLegacyAnalyticsChart();
        updateChartById('sensorTrendsChart', historyStore.labels, [
            historyStore.temperature,
            historyStore.water_temperature,
            historyStore.humidity,
            historyStore.ph,
            historyStore.ammonia,
            historyStore.light
        ]);
        updateChartById('analyticsTrendChart', historyStore.labels, [
            historyStore.temperature,
            historyStore.water_temperature,
            historyStore.humidity,
            historyStore.ph,
            historyStore.ammonia
        ]);
        updateChartById('waterUsageChart', historyStore.labels, [
            historyStore.water_level
        ]);
        updateChartById('distributionChart', ['Temperature', 'Humidity', 'pH', 'Water Temp', 'Ammonia'], [[
            toNumber(historyStore.temperature[historyStore.temperature.length - 1] || 0),
            toNumber(historyStore.humidity[historyStore.humidity.length - 1] || 0),
            toNumber(historyStore.ph[historyStore.ph.length - 1] || 0),
            toNumber(historyStore.water_temperature[historyStore.water_temperature.length - 1] || 0),
            toNumber(historyStore.ammonia[historyStore.ammonia.length - 1] || 0)
        ]]);
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
                    error.status = response.status;
                    throw error;
                }

                return payload;
            });
        });
    }

    function fetchLiveData() {
        return fetchJson(API_URL, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
    }

    function fetchPumpData() {
        return fetchJson(API_URL + '?action=get_pumps', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        }).then(function (payload) {
            if (payload && typeof payload.mode !== 'undefined') {
                updatePumpStateUI(payload);
            }

            return payload;
        }).catch(function (error) {
            window.console.error('Failed to fetch pump state:', error);
            return null;
        });
    }

    function buildFormBody(values) {
        var body = new window.URLSearchParams();

        Object.keys(values).forEach(function (key) {
            if (typeof values[key] !== 'undefined' && values[key] !== null) {
                body.append(key, values[key]);
            }
        });

        return body;
    }

    function setPumpMode(mode) {
        return fetchJson(API_URL + '?action=set_mode', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
            },
            body: buildFormBody({
                mode: mode
            }).toString()
        }).then(function (payload) {
            if (payload && typeof payload.mode !== 'undefined') {
                updatePumpStateUI(payload);
            }

            return payload;
        }).catch(function (error) {
            window.console.error('Failed to set pump mode:', error);
            if (error && error.payload && typeof error.payload.mode !== 'undefined') {
                updatePumpStateUI(error.payload);
            }
            throw error;
        });
    }

    function setPumpState(pump, state) {
        return fetchJson(API_URL + '?action=set_pump', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
            },
            body: buildFormBody({
                pump: pump,
                state: state
            }).toString()
        }).then(function (payload) {
            if (payload && typeof payload.mode !== 'undefined') {
                updatePumpStateUI(payload);
            }

            return payload;
        }).catch(function (error) {
            window.console.error('Failed to set pump state:', error);
            if (error && error.payload && typeof error.payload.mode !== 'undefined') {
                updatePumpStateUI(error.payload);
            } else {
                fetchPumpData();
            }
            throw error;
        });
    }

    function startLiveUpdates() {
        if (liveUpdateIntervalId) {
            return;
        }

        function refresh() {
            fetchLiveData().then(function (payload) {
                if (!payload || !payload.sensors) {
                    return;
                }

                var timestampLabel = new Date().toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });

                ensureHistoryPoint(timestampLabel, payload.sensors);
                updateSharedAndPageSpecificUI(payload);
                updateCharts();
            }).catch(function (error) {
                window.console.error('Failed to fetch live aquaponics data:', error);
            });
        }

        refresh();
        liveUpdateIntervalId = window.setInterval(refresh, POLL_INTERVAL);
    }

    function startPumpSync() {
        if (pumpSyncIntervalId) {
            return;
        }

        fetchPumpData();
        pumpSyncIntervalId = window.setInterval(fetchPumpData, POLL_INTERVAL);
    }

    function togglePump(pump, state) {
        if (!pump) {
            return Promise.resolve(null);
        }

        var nextState = state;
        if (typeof nextState === 'undefined' || nextState === null) {
            nextState = pumpStateStore && pumpStateStore[pump] ? 0 : 1;
        }

        return setPumpState(pump, nextState);
    }

    function toggleDarkMode(nextMode) {
        var resolvedMode = nextMode;

        if (typeof resolvedMode === 'boolean') {
            resolvedMode = resolvedMode ? 'dark' : 'light';
        }

        if (resolvedMode !== 'dark' && resolvedMode !== 'light') {
            resolvedMode = document.body && document.body.classList.contains('dark-mode') ? 'light' : 'dark';
        }

        applyTheme(resolvedMode);
        return resolvedMode;
    }

    function previewImage(input, targetId) {
        var target = getElement(targetId);
        var file = input && input.files ? input.files[0] : null;

        if (!file || !target) {
            return;
        }

        if (!window.URL || typeof window.URL.createObjectURL !== 'function') {
            return;
        }

        var objectUrl = window.URL.createObjectURL(file);
        target.src = objectUrl;
        target.onload = function () {
            window.URL.revokeObjectURL(objectUrl);
            target.onload = null;
        };
    }

    function bindPumpControlEvents() {
        document.querySelectorAll('[data-pump-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                var pump = button.getAttribute('data-pump-toggle');
                if (!pumpStateStore || pumpStateStore.mode === 'auto') {
                    fetchPumpData();
                    return;
                }

                button.disabled = true;
                togglePump(pump).finally(function () {
                    fetchPumpData().finally(function () {
                        if (pumpStateStore && pumpStateStore.mode === 'manual') {
                            button.disabled = false;
                        }
                    });
                });
            });
        });

    }

    function initSharedFeatures() {
        initSharedDashboardChart();
        initLegacyAnalyticsChart();
        initTempGauge();
        bindPumpControlEvents();
        startLiveUpdates();
        startPumpSync();
    }

    document.addEventListener('DOMContentLoaded', function () {
        var savedTheme = getSavedTheme();
        if (savedTheme === 'dark' || savedTheme === 'light') {
            applyTheme(savedTheme);
        }
    });

    window.addEventListener('load', initSharedFeatures);

    AquaSmart.getElement = getElement;
    AquaSmart.initTempGauge = initTempGauge;
    AquaSmart.initSharedDashboardChart = initSharedDashboardChart;
    AquaSmart.initLegacyAnalyticsChart = initLegacyAnalyticsChart;
    AquaSmart.startLiveUpdates = startLiveUpdates;
    AquaSmart.startPumpSync = startPumpSync;
    AquaSmart.togglePump = togglePump;
    AquaSmart.toggleDarkMode = toggleDarkMode;
    AquaSmart.previewImage = previewImage;
    AquaSmart.fetchLiveData = fetchLiveData;
    AquaSmart.fetchPumpData = fetchPumpData;
    AquaSmart.setPumpMode = setPumpMode;

    window.AquaSmart = AquaSmart;
    window.togglePump = togglePump;
    window.toggleDarkMode = toggleDarkMode;
    window.previewImage = previewImage;
})(window, document);
