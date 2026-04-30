<?php

header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "aquaponics");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

function reports_add_alert(&$alerts, $message, $severity)
{
    foreach ($alerts as $alert) {
        if ($alert["message"] === $message && $alert["severity"] === $severity) {
            return;
        }
    }

    $alerts[] = [
        "message" => $message,
        "severity" => $severity
    ];
}

function reports_build_empty_response($from, $to, $alerts = [])
{
    return [
        "summary" => [
            "avg_temperature" => 0,
            "avg_humidity" => 0,
            "avg_ph" => 0,
            "avg_ammonia" => 0,
            "avg_water_temperature" => 0,
            "avg_light" => 0,
            "avg_water_level" => 0,
            "days" => 0
        ],
        "daily" => [],
        "temperature" => [],
        "water_quality" => [],
        "environment" => [],
        "alerts" => $alerts,
        "meta" => [
            "from" => $from,
            "to" => $to,
            "row_count" => 0
        ]
    ];
}

$action = $_GET["action"] ?? "";

if ($action !== "report") {
    http_response_code(400);
    echo json_encode(["error" => "Unsupported action"]);
    $conn->close();
    exit;
}

$from = $_GET["from"] ?? "";
$to = $_GET["to"] ?? "";

if (!$from || !$to) {
    http_response_code(400);
    echo json_encode(["error" => "Missing from or to date"]);
    $conn->close();
    exit;
}

$fromDate = DateTime::createFromFormat("Y-m-d", $from);
$toDate = DateTime::createFromFormat("Y-m-d", $to);

if (!$fromDate || !$toDate || $fromDate->format("Y-m-d") !== $from || $toDate->format("Y-m-d") !== $to) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid date format"]);
    $conn->close();
    exit;
}

if ($fromDate > $toDate) {
    http_response_code(400);
    echo json_encode(["error" => "From date must be before or equal to to date"]);
    $conn->close();
    exit;
}

$fromDateTime = $from . " 00:00:00";
$toDateTime = $to . " 23:59:59";

$dailySql = "
    SELECT
        DATE(created_at) AS date,
        AVG(temperature) AS temperature,
        AVG(humidity) AS humidity,
        AVG(water_temperature) AS water_temperature,
        AVG(ph) AS ph,
        AVG(ammonia) AS ammonia,
        AVG(light) AS light,
        AVG(water_level) AS water_level
    FROM sensor_data
    WHERE created_at BETWEEN ? AND ?
    GROUP BY DATE(created_at)
    ORDER BY date ASC
";

$dailyStmt = $conn->prepare($dailySql);

if (!$dailyStmt) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to prepare daily report query"]);
    $conn->close();
    exit;
}

$dailyStmt->bind_param("ss", $fromDateTime, $toDateTime);
$dailyStmt->execute();
$dailyResult = $dailyStmt->get_result();

$daily = [];
while ($row = $dailyResult->fetch_assoc()) {
    $daily[] = [
        "date" => $row["date"],
        "temperature" => round((float) $row["temperature"], 2),
        "humidity" => round((float) $row["humidity"], 2),
        "water_temperature" => round((float) $row["water_temperature"], 2),
        "ph" => round((float) $row["ph"], 2),
        "ammonia" => round((float) $row["ammonia"], 2),
        "light" => round((float) $row["light"], 2),
        "water_level" => round((float) $row["water_level"], 2)
    ];
}

$alerts = [];

if (!$daily) {
    reports_add_alert($alerts, "No data available for the selected date range", "warning");
    echo json_encode(reports_build_empty_response($from, $to, $alerts));
    $dailyStmt->close();
    $conn->close();
    exit;
}

$rawStmt = $conn->prepare("
    SELECT temperature, humidity, ammonia, water_temperature, ph, light, water_level, created_at
    FROM sensor_data
    WHERE created_at BETWEEN ? AND ?
    ORDER BY created_at ASC
");

if (!$rawStmt) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to prepare raw report query"]);
    $dailyStmt->close();
    $conn->close();
    exit;
}

$rawStmt->bind_param("ss", $fromDateTime, $toDateTime);
$rawStmt->execute();
$rawResult = $rawStmt->get_result();

$previousTimestamp = null;
while ($row = $rawResult->fetch_assoc()) {
    $temperature = (float) $row["temperature"];
    $ammonia = (float) $row["ammonia"];
    $waterLevel = (float) $row["water_level"];

    if ($waterLevel < 20) {
        reports_add_alert($alerts, "Low water level detected in report range", "critical");
    } elseif ($waterLevel < 40) {
        reports_add_alert($alerts, "Water level dropped during the report range", "warning");
    }

    if ($temperature > 35) {
        reports_add_alert($alerts, "High temperature detected in report range", "critical");
    } elseif ($temperature < 15) {
        reports_add_alert($alerts, "Low temperature detected in report range", "warning");
    }

    if ($ammonia > 1.0) {
        reports_add_alert($alerts, "Ammonia spike detected in report range", "critical");
    } elseif ($ammonia > 0.5) {
        reports_add_alert($alerts, "Ammonia increased during the report range", "warning");
    }

    if ($previousTimestamp !== null) {
        $gap = strtotime($row["created_at"]) - strtotime($previousTimestamp);
        if ($gap > 3600) {
            reports_add_alert($alerts, "Missing data gap detected in report range", "warning");
        }
    }

    $previousTimestamp = $row["created_at"];
}

$summary = [
    "avg_temperature" => 0,
    "avg_humidity" => 0,
    "avg_ph" => 0,
    "avg_ammonia" => 0,
    "avg_water_temperature" => 0,
    "avg_light" => 0,
    "avg_water_level" => 0,
    "days" => count($daily)
];

$temperature = [];
$waterQuality = [];
$environment = [];

foreach ($daily as $row) {
    $summary["avg_temperature"] += $row["temperature"];
    $summary["avg_humidity"] += $row["humidity"];
    $summary["avg_ph"] += $row["ph"];
    $summary["avg_ammonia"] += $row["ammonia"];
    $summary["avg_water_temperature"] += $row["water_temperature"];
    $summary["avg_light"] += $row["light"];
    $summary["avg_water_level"] += $row["water_level"];

    $temperature[] = [
        "date" => $row["date"],
        "temperature" => $row["temperature"],
        "water_temperature" => $row["water_temperature"]
    ];

    $waterQuality[] = [
        "date" => $row["date"],
        "ph" => $row["ph"],
        "ammonia" => $row["ammonia"]
    ];

    $environment[] = [
        "date" => $row["date"],
        "humidity" => $row["humidity"],
        "light" => $row["light"]
    ];
}

$dayCount = max(1, count($daily));

$summary["avg_temperature"] = round($summary["avg_temperature"] / $dayCount, 2);
$summary["avg_humidity"] = round($summary["avg_humidity"] / $dayCount, 2);
$summary["avg_ph"] = round($summary["avg_ph"] / $dayCount, 2);
$summary["avg_ammonia"] = round($summary["avg_ammonia"] / $dayCount, 2);
$summary["avg_water_temperature"] = round($summary["avg_water_temperature"] / $dayCount, 2);
$summary["avg_light"] = round($summary["avg_light"] / $dayCount, 2);
$summary["avg_water_level"] = round($summary["avg_water_level"] / $dayCount, 2);

$response = [
    "summary" => $summary,
    "daily" => $daily,
    "temperature" => $temperature,
    "water_quality" => $waterQuality,
    "environment" => $environment,
    "alerts" => $alerts,
    "meta" => [
        "from" => $from,
        "to" => $to,
        "row_count" => count($daily)
    ]
];

echo json_encode($response);

$rawStmt->close();
$dailyStmt->close();
$conn->close();
?>
