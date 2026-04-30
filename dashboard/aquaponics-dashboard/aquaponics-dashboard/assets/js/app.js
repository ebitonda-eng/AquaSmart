(function (window, document) {
    'use strict';

    var AquaSmart = window.AquaSmart || {};
    var liveUpdateIntervalId = null;

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

        AquaSmart.tempGauge.animate(0.7);
        return AquaSmart.tempGauge;
    }

    function initSharedDashboardChart() {
        var chartCanvas = getElement('chart');
        var created = false;
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

        created = true;

        return {
            chart: AquaSmart.dashboardChart,
            created: created
        };
    }

    function initLegacyAnalyticsChart() {
        var chartCanvas = getElement('analyticsChart');
        var created = false;
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

        created = true;

        return {
            chart: AquaSmart.analyticsChart,
            created: created
        };
    }

    function updateTextContent(elementId, value) {
        var element = getElement(elementId);
        if (element) {
            element.textContent = value;
        }
    }

    function startLiveUpdates(chartInstance) {
        var temperatureElement = getElement('temp');
        var humidityElement = getElement('humidity');
        var phElement = getElement('ph');
        var hasValueTargets = !!(temperatureElement || humidityElement || phElement);
        var activeChart = chartInstance || AquaSmart.dashboardChart || null;
        var activeGauge = AquaSmart.tempGauge || null;

        if (!activeChart && !activeGauge && !hasValueTargets) {
            return;
        }

        if (liveUpdateIntervalId) {
            return;
        }

        liveUpdateIntervalId = window.setInterval(function () {
            var time = new Date().toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            var temperature = (20 + Math.random() * 10).toFixed(1);
            var humidity = Math.round(55 + Math.random() * 15);
            var ph = (6.5 + Math.random() * 0.6).toFixed(1);

            updateTextContent('temp', temperature + '\u00B0C');
            updateTextContent('humidity', humidity + '%');
            updateTextContent('ph', ph);

            if (activeChart && activeChart.data && activeChart.data.datasets && activeChart.data.datasets[0]) {
                activeChart.data.labels.push(time);
                activeChart.data.datasets[0].data.push(Number(temperature));

                if (activeChart.data.datasets[1]) {
                    activeChart.data.datasets[1].data.push(Number(humidity));
                }

                if (activeChart.data.datasets[2]) {
                    activeChart.data.datasets[2].data.push(Number(ph));
                }

                if (activeChart.data.labels.length > 12) {
                    activeChart.data.labels.shift();
                    activeChart.data.datasets.forEach(function (dataset) {
                        if (Array.isArray(dataset.data) && dataset.data.length > 0) {
                            dataset.data.shift();
                        }
                    });
                }

                activeChart.update();
            }

            if (activeGauge) {
                activeGauge.animate(Number(temperature) / 40);
            }
        }, 2000);
    }

    function togglePump(pump, state) {
        if (!pump || !state) {
            return;
        }

        window.console.log(String(pump) + ' -> ' + String(state));
        // later connect to ESP32 / API
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

    function initSharedFeatures() {
        var dashboardChartResult = initSharedDashboardChart();
        var analyticsChartResult = initLegacyAnalyticsChart();
        var liveChart = dashboardChartResult.chart || analyticsChartResult.chart || null;

        initTempGauge();

        if (dashboardChartResult.created || analyticsChartResult.created || AquaSmart.tempGauge) {
            startLiveUpdates(liveChart);
        }
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
    AquaSmart.togglePump = togglePump;
    AquaSmart.toggleDarkMode = toggleDarkMode;
    AquaSmart.previewImage = previewImage;

    window.AquaSmart = AquaSmart;
    window.togglePump = togglePump;
    window.toggleDarkMode = toggleDarkMode;
    window.previewImage = previewImage;
})(window, document);
