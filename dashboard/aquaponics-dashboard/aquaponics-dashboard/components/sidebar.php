<?php
$basePath = '/aquaponics-dashboard/';
$currentPage = basename($_SERVER['SCRIPT_NAME'] ?? '');

$navItems = [
    ['label' => 'Dashboard', 'path' => 'index.php'],
    ['label' => 'Sensors', 'path' => 'pages/sensors.php'],
    ['label' => 'Analytics', 'path' => 'pages/analytics.php'],
    ['label' => 'Pumps', 'path' => 'pages/pumps.php'],
    ['label' => 'Alerts', 'path' => 'pages/alerts.php'],
    ['label' => 'Reports', 'path' => 'pages/reports.php'],
    ['label' => 'Settings', 'path' => 'pages/settings.php'],
];
?>
<div class="sidebar">
    <h3>AquaSmart</h3>

    <?php foreach ($navItems as $item): ?>
        <?php
        $href = $basePath . $item['path'];
        $isActive = $currentPage === basename($item['path']);
        ?>
        <a href="<?php echo htmlspecialchars($href, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $isActive ? ' class="active"' : ''; ?>>
            <?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>
        </a>
    <?php endforeach; ?>
</div>
