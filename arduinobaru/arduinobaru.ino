#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <ArduinoJson.h>

#include "DHT.h"

#define DHTPIN D5
#define DHTTYPE DHT22
DHT dht(DHTPIN, DHTTYPE);

const int lampu = D6;
int ADC_potensio = 0;

// WiFi
const char* ssid = "SIKC";
const char* password = "";

WiFiClient wifiClient;

String apiBase = "http://10.75.230.208/laravel-iot/public/api";
String ledUrl = apiBase + "/led";
String sensorReadingsUrl = apiBase + "/sensor-readings";

void setup() {
  Serial.begin(115200);
  dht.begin();

  pinMode(lampu, OUTPUT);

  // Koneksi WiFi langsung
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  Serial.print("Menghubungkan ke WiFi");

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println();
  Serial.println("Terhubung ke WiFi!");
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  kirimDataSensor();
  bacaStatusLed();

  delay(2000);
}

void kirimDataSensor() {
  float h = dht.readHumidity();
  float t = dht.readTemperature();

  if (isnan(h) || isnan(t)) {
    Serial.println(F("Gagal mendapatkan nilai suhu dan kelembapan"));
    return;
  }

  Serial.print("Kelembapan: ");
  Serial.println(h);

  Serial.print("Suhu: ");
  Serial.println(t);

  ADC_potensio = analogRead(A0);
  Serial.print("Potensio: ");
  Serial.println(ADC_potensio);

  if (WiFi.status() == WL_CONNECTED) {

    StaticJsonDocument<128> doc;

    doc["suhu"] = t;
    doc["kelembapan"] = h;
    doc["potensio"] = ADC_potensio;

    String body;
    serializeJson(doc, body);

    HTTPClient http;

    http.begin(wifiClient, sensorReadingsUrl);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");

    int httpResponseCode = http.POST(body);

    if (httpResponseCode > 0) {
      Serial.print("Data sensor terkirim, kode: ");
      Serial.println(httpResponseCode);
    } else {
      Serial.print("Gagal kirim data sensor, kode: ");
      Serial.println(httpResponseCode);
    }

    http.end();
  } else {
    Serial.println("WiFi tidak terhubung");
  }
}

void bacaStatusLed() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("WiFi tidak terhubung");
    return;
  }

  HTTPClient http;

  http.begin(wifiClient, ledUrl);
  http.addHeader("Accept", "application/json");

  int httpCode = http.GET();

  if (httpCode == HTTP_CODE_OK) {

    String payload = http.getString();

    Serial.print("Response LED: ");
    Serial.println(payload);

    StaticJsonDocument<256> doc;

    DeserializationError err = deserializeJson(doc, payload);

    if (!err) {

      const char* status = doc["data"]["status"];

      if (status && strcmp(status, "ON") == 0) {

        analogWrite(lampu, ADC_potensio);

        Serial.print("LED ON, brightness: ");
        Serial.println(ADC_potensio);

      } else {

        analogWrite(lampu, 0);

        Serial.println("LED OFF");
      }

    } else {
      Serial.println("Gagal parse JSON status LED");
    }

  } else {

    Serial.print("Gagal melakukan permintaan HTTP, kode: ");
    Serial.println(httpCode);
  }

  http.end();
}