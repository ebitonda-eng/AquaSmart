# AquaSmart

![PHP](https://img.shields.io/badge/PHP-Backend-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Data%20Layer-4479A1?logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-UI-7952B3?logo=bootstrap&logoColor=white)
![Chart.js](https://img.shields.io/badge/Chart.js-Analytics-FF6384?logo=chartdotjs&logoColor=white)
![ESP32](https://img.shields.io/badge/ESP32-IoT-E7352C?logo=espressif&logoColor=white)
![IoT](https://img.shields.io/badge/Domain-Smart%20Agriculture-16a34a)

> **AquaSmart is a smart aquaponics platform that unites live environmental monitoring, automation, and data-driven decision making in one elegant control center.**

## Overview

AquaSmart was built to bring intelligence, clarity, and control to modern aquaponics. It blends a real-time PHP dashboard, MySQL-powered APIs, interactive analytics, pump automation, and ESP32-based device logic into a complete digital farming experience.

From live sensor visibility to relay-based pump control and exportable reporting, AquaSmart reflects a practical vision for sustainable agriculture: systems that are connected, responsive, and designed to help growers operate with confidence.

## Main Features

- Real-time aquaponics dashboard with live system indicators
- Environmental monitoring for temperature, humidity, water temperature, pH, ammonia, light, and water level
- Automated alert generation based on sensor thresholds and data freshness
- Pump control management for heater, fan, irrigation, refill, and filtration
- Auto and manual operating modes for device control
- Dedicated analytics and sensor-trend pages powered by Chart.js
- Structured reporting with date filtering, grouped datasets, CSV export, and PDF export
- Settings interface for theme preference, system profile inputs, and dashboard media preview

## Smart Dashboard Features

- A polished multi-page dashboard experience built around `Dashboard`, `Sensors`, `Analytics`, `Pumps`, `Alerts`, `Reports`, and `Settings`
- Live metric cards for key farm conditions and overall system health
- Visual water-level progress tracking and quick-status indicators
- Dynamic alert panels with severity-based presentation
- Multi-chart analytics for temperature, humidity, pH, ammonia, water usage, and sensor distribution
- Persistent light and dark interface behavior through browser-side theme storage
- Clean operator workflow for monitoring, control, reporting, and configuration

## ESP32 / IoT Integration

AquaSmart extends beyond the browser into the physical environment through ESP32-based control logic.

- Wi-Fi enabled device communication using `WiFi.h` and `HTTPClient.h`
- JSON handling through `ArduinoJson`
- Relay output management for Heater, Fan, Irrigation, Refill, and Filtration
- Automatic pump decision logic based on water temperature, air temperature, and water level
- HTTP-based pump control coordination between the IoT layer and the web platform

Default relay mappings in the firmware:

```cpp
HEATER_PIN = 14
FAN_PIN = 27
IRRIGATION_PIN = 26
REFILL_PIN = 25
FILTER_PIN = 33
```

## Technologies Used

| Layer | Technology |
|---|---|
| Frontend | HTML, CSS, Bootstrap 5, Vanilla JavaScript |
| Charts | Chart.js |
| Backend | PHP |
| Database | MySQL / MariaDB |
| Reports | jsPDF, jsPDF AutoTable |
| IoT | ESP32, Arduino Framework |
| Connectivity | HTTP, Wi-Fi |

## Folder Structure

```text
AquaSmart/
├── index.php
├── api/
│   ├── data.php
│   └── reports.php
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── app.js
│   ├── images/
│   └── Folder Structure/
├── components/
│   ├── navbar.php
│   └── sidebar.php
├── database/
│   └── pump_control.sql
├── esp32/
│   └── pump_control_example.ino
└── pages/
    ├── alerts.php
    ├── analytics.php
    ├── pumps.php
    ├── reports.php
    ├── sensors.php
    └── settings.php
```

## Installation & Setup

### 1. Place the project in your web root

```text
C:\xampp\htdocs\aquaponics-dashboard
```

### 2. Start your local stack

Run Apache and MySQL through XAMPP or your preferred PHP development environment.

### 3. Create the database

```sql
CREATE DATABASE aquaponics;
```

### 4. Load the control table

Import the included SQL file:

```text
database/pump_control.sql
```

### 5. Prepare the sensor data layer

Provision the `sensor_data` table used by the application with the fields consumed by the API:

```text
temperature
humidity
ammonia
water_temperature
ph
light
water_level
created_at
```

### 6. Open the dashboard

```text
http://localhost/aquaponics-dashboard/
```

### 7. Configure the ESP32 connection

Update the Wi-Fi and server constants in:

```text
esp32/pump_control_example.ino
```

## How It Works

### Live Monitoring Flow

1. Sensor readings are sent to the backend through `api/data.php?action=insert`.
2. The backend stores the latest environmental values in MySQL.
3. The dashboard polls the API every few seconds to retrieve fresh readings.
4. JavaScript updates system cards, charts, alert panels, pump views, and analytics in real time.

### Control Flow

1. Pump states are managed through the backend API.
2. The dashboard can read live pump status with `action=get_pumps`.
3. Operating mode is switched with `action=set_mode`.
4. Individual devices are controlled through `action=set_pump`.
5. The ESP32 applies the active logic and drives the relay outputs.

### Reporting Flow

1. The reports page queries `api/reports.php`.
2. Records are grouped by day for structured analysis.
3. Summary values, environmental datasets, and alert-aware reporting are rendered in the browser.
4. Reports can be exported as CSV or PDF for documentation and operational review.

## Real World Impact

AquaSmart speaks directly to the future of food systems.

- It supports smarter water management through continuous water-level awareness.
- It helps protect aquatic and plant health through threshold-based monitoring.
- It improves operator visibility by turning raw sensor data into clear, visual intelligence.
- It strengthens farm responsiveness by linking environmental readings to actionable control logic.
- It brings digital infrastructure to sustainable agriculture in a practical, understandable way.

## Why This Project Matters

Aquaponics is one of the most promising models for efficient, resilient food production. To succeed at scale, it needs more than sensors alone. It needs software that can translate conditions into insight, and insight into action.

AquaSmart was designed with that mindset. It is not just a dashboard. It is a connected farming interface built around sustainability, automation, and operational clarity. It shows how software engineering, embedded systems, and environmental thinking can come together to create meaningful tools for the real world.

## About the Developer

**Developed by Elie Bitonda Tuyizere & Ruganji Prince**

We have built AquaSmart as a vision-driven smart agriculture platform that combines software craftsmanship with practical environmental impact. The project reflects a strong interest in intelligent systems, clean interfaces, IoT integration, and technology that serves real human needs.

AquaSmart represents more than technical execution. It represents initiative, ownership, and a clear belief that thoughtful engineering can help shape a more sustainable future.

