#include <SPI.h>
#include <MFRC522.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <Wire.h>
#include <Adafruit_SSD1306.h>

#define RST_PIN 22
#define SS_PIN 21
MFRC522 mfrc522(SS_PIN, RST_PIN);

#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64
Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, -1);

#define GREEN_LED 2
#define RED_LED 4
#define BUZZER 5

const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";
const String serverURL = "http://192.168.1.100/animal.php"; // replace with your XAMPP server IP

void setup() {
  Serial.begin(115200);
  SPI.begin();
  mfrc522.PCD_Init();
  pinMode(GREEN_LED, OUTPUT);
  pinMode(RED_LED, OUTPUT);
  pinMode(BUZZER, OUTPUT);

  if(!display.begin(SSD1306_SWITCHCAPVCC, 0x3C)) {
    Serial.println(F("SSD1306 allocation failed"));
    while(true);
  }
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(WHITE);
  display.display();

  WiFi.begin(ssid, password);
  Serial.print("Connecting to Wi-Fi");
  while(WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("Connected!");
}

void loop() {
  if ( ! mfrc522.PICC_IsNewCardPresent() || ! mfrc522.PICC_ReadCardSerial()) {
    return;
  }
  String tagId = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    tagId += String(mfrc522.uid.uidByte[i], HEX);
  }
  tagId.toUpperCase();
  Serial.println("Tag scanned: " + tagId);

  if(WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = serverURL + "?tagId=" + tagId;
    http.begin(url);
    int httpCode = http.GET();
    if(httpCode > 0) {
      String payload = http.getString();
      Serial.println(payload);

      DynamicJsonDocument doc(1024);
      deserializeJson(doc, payload);

      display.clearDisplay();
      display.setCursor(0,0);

      if(doc.containsKey("error")) {
        display.println("Unregistered Tag");
        digitalWrite(RED_LED, HIGH);
        tone(BUZZER, 1000, 200);
      } else {
        display.println("Name: " + String(doc["name"].as<const char*>()));
        display.println("Age: " + String(doc["age"].as<int>()));
        bool isSick = doc["isSick"];
        bool isPreg = doc["isPregnant"];

        if(isSick) {
          digitalWrite(RED_LED, HIGH);
          tone(BUZZER, 1000, 300);
        } else {
          digitalWrite(GREEN_LED, HIGH);
          tone(BUZZER, 500, 100);
        }
      }
      display.display();
    }
    http.end();
  } else {
    display.clearDisplay();
    display.setCursor(0,0);
    display.println("Wi-Fi unavailable");
    display.println("Tag ID: " + tagId);
    display.display();
  }

  delay(3000);
  digitalWrite(GREEN_LED, LOW);
  digitalWrite(RED_LED, LOW);
}