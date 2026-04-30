<?php

header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "aquaponics");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$pumpColumns = ["heater", "fan", "irrigation", "refill", "filter"];

function respond_json($payload, $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

function request_value($key, $default = null)
{
    if (isset($_POST[$key])) {
        return $_POST[$key];
    }

    if (isset($_GET[$key])) {
        return $_GET[$key];
    }

    return $default;
}

function normalize_binary_value($value)
{
    if ($value === null) {
        return null;
    }

    if ($value === "1" || $value === 1 || $value === true || $value === "true" || $value === "on") {
        return 1;
    }

    if ($value === "0" || $value === 0 || $value === false || $value === "false" || $value === "off") {
        return 0;
    }

    return null;
}

function ensure_pump_control_table($conn)
{
    $sql = "
        CREATE TABLE IF NOT EXISTS pump_control (
            id INT PRIMARY KEY AUTO_INCREMENT,
            mode VARCHAR(10) DEFAULT 'auto',
            heater TINYINT(1) DEFAULT 0,
            fan TINYINT(1) DEFAULT 0,
            irrigation TINYINT(1) DEFAULT 0,
            refill TINYINT(1) DEFAULT 0,
            filter TINYINT(1) DEFAULT 0,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ";

    if (!$conn->query($sql)) {
        respond_json(["error" => "Failed to prepare pump control table"], 500);
    }

    $checkResult = $conn->query("SELECT id FROM pump_control WHERE id = 1 LIMIT 1");
    $hasPrimaryRow = $checkResult && $checkResult->fetch_assoc();

    if (!$hasPrimaryRow) {
        $insertSql = "
            INSERT INTO pump_control (id, mode, heater, fan, irrigation, refill, filter)
            VALUES (1, 'auto', 0, 0, 0, 0, 0)
            ON DUPLICATE KEY UPDATE id = id
        ";

        if (!$conn->query($insertSql)) {
            respond_json(["error" => "Failed to initialize pump control row"], 500);
        }
    }
}

function get_current_pump_state($conn)
{
    $result = $conn->query("
        SELECT mode, heater, fan, irrigation, refill, filter, updated_at
        FROM pump_control
        WHERE id = 1
        LIMIT 1
    ");

    $pumpState = $result ? $result->fetch_assoc() : null;

    if (!$pumpState) {
        return [
            "mode" => "auto",
            "heater" => 0,
            "fan" => 0,
            "irrigation" => 0,
            "refill" => 0,
            "filter" => 0,
            "updated_at" => null
        ];
    }

    return [
        "mode" => $pumpState["mode"] === "manual" ? "manual" : "auto",
        "heater" => (int) $pumpState["heater"],
        "fan" => (int) $pumpState["fan"],
        "irrigation" => (int) $pumpState["irrigation"],
        "refill" => (int) $pumpState["refill"],
        "filter" => (int) $pumpState["filter"],
        "updated_at" => $pumpState["updated_at"]
    ];
}

function get_public_pump_state($conn)
{
    $state = get_current_pump_state($conn);

    return [
        "mode" => $state["mode"],
        "heater" => $state["heater"],
        "fan" => $state["fan"],
        "irrigation" => $state["irrigation"],
        "refill" => $state["refill"],
        "filter" => $state["filter"]
    ];
}

function update_mode($conn, $mode)
{
    $stmt = $conn->prepare("UPDATE pump_control SET mode = ?, updated_at = NOW() WHERE id = 1");

    if (!$stmt) {
        respond_json(["error" => "Failed to prepare mode update"], 500);
    }

    $stmt->bind_param("s", $mode);

    if (!$stmt->execute()) {
        $stmt->close();
        respond_json(["error" => "Failed to update mode"], 500);
    }

    $stmt->close();
}

function update_pump_value($conn, $pump, $state)
{
    $sql = "UPDATE pump_control SET `$pump` = ?, updated_at = NOW() WHERE id = 1";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        respond_json(["error" => "Failed to prepare pump update"], 500);
    }

    $stmt->bind_param("i", $state);

    if (!$stmt->execute()) {
        $stmt->close();
        respond_json(["error" => "Failed to update pump"], 500);
    }

    $stmt->close();
}

ensure_pump_control_table($conn);

$action = request_value("action", "");

if ($action === "insert") {
    $temperature = request_value("temperature");
    $humidity = request_value("humidity");
    $ammonia = request_value("ammonia", 0);
    $water_temperature = request_value("water_temperature", 0);
    $ph = request_value("ph", 0);
    $light = request_value("light", 0);
    $water_level = request_value("water_level", 0);

    if ($temperature === null || $humidity === null) {
        respond_json(["error" => "Missing required data"], 400);
    }

    $stmt = $conn->prepare("
        INSERT INTO sensor_data
        (temperature, humidity, ammonia, water_temperature, ph, light, water_level)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        respond_json(["error" => "Failed to prepare insert"], 500);
    }

    $stmt->bind_param(
        "ddddddd",
        $temperature,
        $humidity,
        $ammonia,
        $water_temperature,
        $ph,
        $light,
        $water_level
    );

    if (!$stmt->execute()) {
        $stmt->close();
        respond_json(["error" => "Insert failed"], 500);
    }

    $stmt->close();
    respond_json(["status" => "success"]);
}

if ($action === "get_pumps") {
    respond_json(get_public_pump_state($conn));
}

if ($action === "set_mode") {
    $mode = strtolower((string) request_value("mode", ""));

    if ($mode !== "auto" && $mode !== "manual") {
        respond_json(["error" => "Mode must be auto or manual"], 400);
    }

    update_mode($conn, $mode);
    respond_json(get_public_pump_state($conn));
}

if ($action === "set_pump") {
    $pump = strtolower((string) request_value("pump", ""));
    $state = normalize_binary_value(request_value("state"));

    if (!in_array($pump, $pumpColumns, true)) {
        respond_json(["error" => "Invalid pump name"], 400);
    }

    if ($state === null) {
        respond_json(["error" => "State must be 0 or 1"], 400);
    }

    $currentState = get_current_pump_state($conn);
    if ($currentState["mode"] !== "manual") {
        respond_json(["error" => "AUTO MODE ACTIVE"], 409);
    }

    update_pump_value($conn, $pump, $state);
    respond_json(get_public_pump_state($conn));
}

$result = $conn->query("
    SELECT * FROM sensor_data
    ORDER BY created_at DESC
    LIMIT 1
");

$sensor = $result ? $result->fetch_assoc() : null;
$alerts = [];

if ($sensor) {
    $now = time();
    $lastTime = strtotime($sensor["created_at"]);
    $diff = $now - $lastTime;

    if ($diff > 30) {
        $alerts[] = ["message" => "No sensor data received", "severity" => "critical"];
    } elseif ($diff > 10) {
        $alerts[] = ["message" => "Sensor delay detected", "severity" => "warning"];
    }

    $ammonia = (float) $sensor["ammonia"];
    if ($ammonia > 1.0) {
        $alerts[] = ["message" => "High Ammonia Level", "severity" => "critical"];
    } elseif ($ammonia > 0.5) {
        $alerts[] = ["message" => "Ammonia Rising", "severity" => "warning"];
    }

    $waterTemp = (float) $sensor["water_temperature"];
    if ($waterTemp > 30) {
        $alerts[] = ["message" => "High Water Temperature", "severity" => "critical"];
    } elseif ($waterTemp < 20) {
        $alerts[] = ["message" => "Low Water Temperature", "severity" => "warning"];
    }

    $ph = (float) $sensor["ph"];
    if ($ph < 6) {
        $alerts[] = ["message" => "Low pH Level", "severity" => "warning"];
    } elseif ($ph > 8) {
        $alerts[] = ["message" => "High pH Level", "severity" => "warning"];
    }

    $light = (float) $sensor["light"];
    if ($light < 20) {
        $alerts[] = ["message" => "Low Light Level", "severity" => "warning"];
    }

    $water_level = (float) $sensor["water_level"];
    if ($water_level < 20) {
        $alerts[] = ["message" => "Low Water Level", "severity" => "critical"];
    } elseif ($water_level < 40) {
        $alerts[] = ["message" => "Water Level Dropping", "severity" => "warning"];
    }

    $temp = (float) $sensor["temperature"];
    if ($temp > 35) {
        $alerts[] = ["message" => "High Air Temperature", "severity" => "critical"];
    } elseif ($temp < 15) {
        $alerts[] = ["message" => "Low Air Temperature", "severity" => "warning"];
    }
}

$response = [
    "sensors" => [
        "temperature" => (float) ($sensor["temperature"] ?? 0),
        "humidity" => (float) ($sensor["humidity"] ?? 0),
        "water_temperature" => (float) ($sensor["water_temperature"] ?? 0),
        "ph" => (float) ($sensor["ph"] ?? 0),
        "ammonia" => (float) ($sensor["ammonia"] ?? 0),
        "light" => (float) ($sensor["light"] ?? 0),
        "water_level" => (float) ($sensor["water_level"] ?? 0),
        "timestamp" => $sensor["created_at"] ?? null
    ],
    "pumps" => get_public_pump_state($conn),
    "alerts" => $alerts
];

echo json_encode($response);

$conn->close();
?>
