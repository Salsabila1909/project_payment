#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <ArduinoJson.h>
#include <Adafruit_Fingerprint.h>
#include <SoftwareSerial.h>

// ================= WIFI CONFIG =================
const char* ssid     = "Aditia";
const char* password = "Rayyangemoi";

// ================= SERVER CONFIG =================
const char* SERVER_PENDING  = "http://192.168.1.8:8000/api/fingerprint/pending";
const char* SERVER_REGISTER = "http://192.168.1.8:8000/api/fingerprint/register";

// ================= FINGERPRINT =================
SoftwareSerial fingerSerial(D1, D2); // RX, TX
Adafruit_Fingerprint finger(&fingerSerial);

WiFiClient wifiClient;

int siswa_id = -1;
int fingerprint_id = 1;

// =================================================
void setup() {
  Serial.begin(115200);
  fingerSerial.begin(57600);

  // ===== CONNECT WIFI =====
  WiFi.begin(ssid, password);
  Serial.print("Connecting WiFi");

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\nWiFi connected!");

  // ===== INIT FINGERPRINT =====
  finger.begin(57600);
  if (finger.verifyPassword()) {
    Serial.println("Sensor fingerprint siap!");
  } else {
    Serial.println("Sensor fingerprint TIDAK terdeteksi!");
    while (1);
  }
}

void loop() {

  // 1️⃣ AMBIL SISWA BELUM TERDAFTAR
  siswa_id = getPendingSiswa();

  if (siswa_id == -1) {
    Serial.println("Tidak ada siswa menunggu, code: -1");
    delay(5000);
    return;
  }

  Serial.print("Siswa ditemukan ID: ");
  Serial.println(siswa_id);

  // 2️⃣ ENROLL FINGERPRINT
  if (enrollFingerprint(fingerprint_id)) {
    Serial.println("Enroll berhasil!");

    // 3️⃣ KIRIM KE SERVER
    sendRegisterFingerprint(siswa_id, fingerprint_id);
    fingerprint_id++; // naikkan ID fingerprint
  }

  delay(5000);
}

// =================================================
// AMBIL SISWA STATUS = "Belum Terdaftar"
// =================================================
int getPendingSiswa() {

  if (WiFi.status() != WL_CONNECTED) return -1;

  HTTPClient http;
  http.begin(wifiClient, SERVER_PENDING);

  int httpCode = http.GET();
  Serial.print("GET Pending HTTP Code: ");
  Serial.println(httpCode);

  if (httpCode != 200) {
    http.end();
    return -1;
  }

  String payload = http.getString();
  http.end();

  Serial.println("Response:");
  Serial.println(payload);

  StaticJsonDocument<256> doc;
  DeserializationError error = deserializeJson(doc, payload);

  if (error) {
    Serial.println("JSON parse error");
    return -1;
  }

  if (!doc["success"]) return -1;

  return doc["data"]["siswa_id"];
}

// =================================================
// PROSES ENROLL FINGERPRINT
// =================================================
bool enrollFingerprint(int id) {
  int p = -1;
  Serial.println("Tempelkan jari...");

  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    if (p == FINGERPRINT_NOFINGER) continue;
    if (p != FINGERPRINT_OK) return false;
  }

  if (finger.image2Tz(1) != FINGERPRINT_OK) return false;

  Serial.println("Angkat jari...");
  delay(2000);

  p = -1;
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
  }

  if (finger.image2Tz(2) != FINGERPRINT_OK) return false;

  if (finger.createModel() != FINGERPRINT_OK) return false;

  if (finger.storeModel(id) != FINGERPRINT_OK) return false;

  return true;
}

// =================================================
// KIRIM HASIL REGISTER KE SERVER
// =================================================
void sendRegisterFingerprint(int siswaID, int fingerID) {

  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  http.begin(wifiClient, SERVER_REGISTER);
  http.addHeader("Content-Type", "application/json");

  StaticJsonDocument<200> doc;
  doc["siswa_id"] = siswaID;
  doc["fingerprint_id"] = fingerID;

  String json;
  serializeJson(doc, json);

  int httpCode = http.POST(json);
  Serial.print("Register HTTP Code: ");
  Serial.println(httpCode);

  http.end();
}
