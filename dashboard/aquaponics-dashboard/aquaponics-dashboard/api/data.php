<?php

// -----------------------------
// DATABASE CONNECTION
// -----------------------------
$conn = new mysqli("localhost", "root", "", "aquaponics");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// -----------------------------
// INSERT DATA (FROM ESP32)
// -----------------------------
if (isset($_GET['action']) && $_GET['action'] == "insert") {

    $temperature = $_POST['temperature'] ?? null;
    $humidity = $_POST['humidity'] ?? null;
    $ammonia = $_POST['ammonia'] ?? null;

    // Validate required fields
    if ($temperature === null || $humidity === null) {
        echo json_encode(["error" => "Missing temperature or humidity"]);
        exit;
    }

    // Default ammonia if not sent
    if ($ammonia === null) {
        $ammonia = 0;
    }

    $stmt = $conn->prepare("
        INSERT INTO sensor_data (temperature, humidity, ammonia)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param("ddd", $temperature, $humidity, $ammonia);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["error" => "Insert failed"]);
    }

    exit;
}

// -----------------------------
// FETCH LATEST DATA
// -----------------------------
$result = $conn->query("
    SELECT * FROM sensor_data 
    ORDER BY created_at DESC 
    LIMIT 1
");

$sensor = $result ? $result->fetch_assoc() : null;

// -----------------------------
// ALERT SYSTEM
// -----------------------------
$alerts = [];

if ($sensor) {

    // -------------------------
    // MISSING DATA ALERT
    // -------------------------
    $lastTime = strtotime($sensor['created_at']);
    $now = time();
    $diff = $now - $lastTime;

    if ($diff > 30) {
        $alerts[] = [
            "message" => "No sensor data received",
            "severity" => "critical"
        ];
    } elseif ($diff > 10) {
        $alerts[] = [
            "message" => "Sensor delay detected",
            "severity" => "warning"
        ];
    }

    // -------------------------
    // AMMONIA ALERTS
    // -------------------------
    if (isset($sensor['ammonia'])) {

        $ammonia = floatval($sensor['ammonia']);

        if ($ammonia > 1.0) {
            $alerts[] = [
                "message" => "High Ammonia Level",
                "severity" => "critical"
            ];
        } elseif ($ammonia > 0.5) {
            $alerts[] = [
                "message" => "Ammonia Rising",
                "severity" => "warning"
            ];
        }
    }

    // -------------------------
    // TEMPERATURE ALERTS
    // -------------------------
    if (isset($sensor['temperature'])) {

        $temp = floatval($sensor['temperature']);

        if ($temp > 35) {
            $alerts[] = [
                "message" => "High Air Temperature",
                "severity" => "critical"
            ];
        } elseif ($temp < 15) {
            $alerts[] = [
                "message" => "Low Air Temperature",
                "severity" => "warning"
            ];
        }
    }
}

// -----------------------------
// RETURN JSON
// -----------------------------
echo json_encode([
    "sensors" => $sensor,
    "alerts" => $alerts
]);

$conn->close();

?>