#include <WiFi.h>
#include <HTTPClient.h>
#include "DHT.h"
#include <OneWire.h>
#include <DallasTemperature.h>

// -------- DHT --------
#define DHTPIN 4
#define DHTTYPE DHT11
DHT dht(DHTPIN, DHTTYPE);

// -------- MQ135 --------
#define MQ135_PIN 34

// -------- DS18B20 --------
#define ONE_WIRE_BUS 5
OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature waterSensor(&oneWire);

// -------- pH Sensor --------
#define PH_PIN 35

// -------- LDR --------
#define LDR_PIN 32

// -------- ULTRASONIC --------
#define TRIG_PIN 18
#define ECHO_PIN 19

// -------- RELAYS --------
#define RELAY_HEATER 23
#define RELAY_FAN 22
#define RELAY_IRRIGATION 21
#define RELAY_REFILL 25
#define RELAY_FILTER 26

#define ON LOW
#define OFF HIGH

// -------- WiFi --------
const char* ssid = "Elijah";
const char* password = "thankyou12";

// -------- SERVER --------
const char* serverInsert = "http://192.168.230.35/aquaponics-dashboard/api/data.php?action=insert";
const char* serverControl = "http://192.168.230.35/aquaponics-dashboard/api/data.php?action=get_pumps";

// -------- GLOBAL MODE (DEFAULT AUTO) --------
String mode = "auto"; 

// -------- CONTROL VARIABLES --------
int heater = 0, fan = 0, irrigation = 0, refill = 0, filter = 0;

// -------- WATER LEVEL --------
float getWaterLevel() {
  long duration;
  float distance;

  digitalWrite(TRIG_PIN, LOW);
  delayMicroseconds(2);

  digitalWrite(TRIG_PIN, HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIG_PIN, LOW);

  duration = pulseIn(ECHO_PIN, HIGH);
  distance = duration * 0.034 / 2;

  float maxDistance = 50.0;
  float level = ((maxDistance - distance) / maxDistance) * 100;

  if (level > 100) level = 100;
  if (level < 0) level = 0;

  return level;
}

// -------- WIFI --------
void connectWiFi() {
  WiFi.begin(ssid, password);
  Serial.print("Connecting...");

  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }

  Serial.println("Connected!");
}

// -------- FETCH MODE FROM DB --------
void fetchControl() {

  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  http.begin(serverControl);

  int code = http.GET();

  if (code > 0) {
    String payload = http.getString();

    Serial.println("CONTROL: " + payload);

    // MODE
    if (payload.indexOf("manual") > 0) mode = "manual";
    else mode = "auto";

    // STATES
    heater = payload.indexOf("\"heater\":1") > 0;
    fan = payload.indexOf("\"fan\":1") > 0;
    irrigation = payload.indexOf("\"irrigation\":1") > 0;
    refill = payload.indexOf("\"refill\":1") > 0;
    filter = payload.indexOf("\"filter\":1") > 0;
  }

  http.end();
}

// -------- AUTO LOGIC --------
void autoControl(float temp, float ph, float ammonia, float water_level) {

  bool danger = false;

  // HEATER
  if (temp < 22) {
    heater = 1;
    digitalWrite(RELAY_HEATER, ON);
  } else {
    heater = 0;
    digitalWrite(RELAY_HEATER, OFF);
  }

  // FAN
  if (temp > 26) {
    fan = 1;
    digitalWrite(RELAY_FAN, ON);
  } else {
    fan = 0;
    digitalWrite(RELAY_FAN, OFF);
  }

  // IRRIGATION
  if (ph >= 6.2 && ph <= 6.8 && ammonia >= 0.1 && ammonia <= 0.25) {
    irrigation = 1;
    digitalWrite(RELAY_IRRIGATION, ON);
  } else {
    irrigation = 0;
    digitalWrite(RELAY_IRRIGATION, OFF);
    danger = true;
  }

  // REFILL
  if (water_level < 50) {
    refill = 1;
    digitalWrite(RELAY_REFILL, ON);
  } else {
    refill = 0;
    digitalWrite(RELAY_REFILL, OFF);
  }

  // FILTER
  if (danger) {
    filter = 1;
    digitalWrite(RELAY_FILTER, ON);
  } else {
    filter = 0;
    digitalWrite(RELAY_FILTER, OFF);
  }
}

// -------- MANUAL CONTROL --------
void manualControl() {
  digitalWrite(RELAY_HEATER, heater ? ON : OFF);
  digitalWrite(RELAY_FAN, fan ? ON : OFF);
  digitalWrite(RELAY_IRRIGATION, irrigation ? ON : OFF);
  digitalWrite(RELAY_REFILL, refill ? ON : OFF);
  digitalWrite(RELAY_FILTER, filter ? ON : OFF);
}

void setup() {
  Serial.begin(115200);

  dht.begin();
  waterSensor.begin();

  pinMode(MQ135_PIN, INPUT);
  pinMode(PH_PIN, INPUT);
  pinMode(LDR_PIN, INPUT);

  pinMode(TRIG_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);

  pinMode(RELAY_HEATER, OUTPUT);
  pinMode(RELAY_FAN, OUTPUT);
  pinMode(RELAY_IRRIGATION, OUTPUT);
  pinMode(RELAY_REFILL, OUTPUT);
  pinMode(RELAY_FILTER, OUTPUT);

  digitalWrite(RELAY_HEATER, OFF);
  digitalWrite(RELAY_FAN, OFF);
  digitalWrite(RELAY_IRRIGATION, OFF);
  digitalWrite(RELAY_REFILL, OFF);
  digitalWrite(RELAY_FILTER, OFF);

  connectWiFi();
}

void loop() {

  if (WiFi.status() != WL_CONNECTED) {
    connectWiFi();
  }

  // -------- FETCH MODE --------
  fetchControl();

  // -------- READ SENSORS --------
  float temperature = dht.readTemperature();
  float humidity = dht.readHumidity();

  int gasRaw = analogRead(MQ135_PIN);
  float ammonia = (gasRaw / 4095.0) * 5.0;

  waterSensor.requestTemperatures();
  float water_temperature = waterSensor.getTempCByIndex(0);

  int phRaw = analogRead(PH_PIN);
  float phVoltage = phRaw * (3.3 / 4095.0);
  float ph = 7 + ((2.5 - phVoltage) / 0.18);

  int ldrRaw = analogRead(LDR_PIN);
  float light = (ldrRaw / 4095.0) * 100;

  float water_level = getWaterLevel();

  // -------- MODE SWITCH --------
  if (mode == "auto") {
    Serial.println("MODE: AUTO");
    autoControl(temperature, ph, ammonia, water_level);

    Serial.println(
      "AUTO: {\"heater\":" + String(heater) +
      ",\"fan\":" + String(fan) +
      ",\"irrigation\":" + String(irrigation) +
      ",\"refill\":" + String(refill) +
      ",\"filter\":" + String(filter) + "}"
    );
  } else {
    Serial.println("MODE: MANUAL");
    manualControl();
  }

  // -------- SEND DATA --------
  HTTPClient http;
  http.begin(serverInsert);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  String postData =
    "temperature=" + String(temperature, 2) +
    "&humidity=" + String(humidity, 2) +
    "&ammonia=" + String(ammonia, 2) +
    "&water_temperature=" + String(water_temperature, 2) +
    "&ph=" + String(ph, 2) +
    "&light=" + String(light, 2) +
    "&water_level=" + String(water_level, 2) +


    "&heater=" + String(heater) +
    "&fan=" + String(fan) +
    "&irrigation=" + String(irrigation) +
    "&refill=" + String(refill) +
    "&filter=" + String(filter) +
    "&mode=" + mode;

  http.POST(postData);
  http.end();

  delay(5000);
}
