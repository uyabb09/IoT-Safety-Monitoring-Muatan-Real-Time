#include <WiFi.h>
#include <HTTPClient.h>

// --- KREDENSIAL WIFI ---
const char* ssid = "BERAKK";
const char* pass = "bayutakterkalahkan";

// --- ALAMAT SERVER ---
const char* serverName = "http://10.207.32.229/monitoring_perahu/post_data.php";

// --- DEFINISI PIN ---
#define TRIG 17
#define ECHO 16
#define BUZZER 19
#define LEDPIN 18

// --- KONFIGURASI BATAS JARAK ---
float batas_bahaya = 4.0; // 0 - 5 cm = Bahaya
float batas_normal = 10.0; // 5.1 - 8 cm = Normal

unsigned long lastTime = 0;
unsigned long timerDelay = 2000; 

void setup() {
  Serial.begin(115200);

  pinMode(TRIG, OUTPUT);
  pinMode(ECHO, INPUT);
  pinMode(BUZZER, OUTPUT);
  pinMode(LEDPIN, OUTPUT);

  digitalWrite(BUZZER, LOW);
  digitalWrite(LEDPIN, LOW);

  WiFi.mode(WIFI_STA); // Memastikan ESP32 bertindak sebagai client
  WiFi.begin(ssid, pass);
  Serial.print("Menghubungkan ke WiFi: ");
  Serial.println(ssid);
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nTerhubung ke WiFi!");
}

long bacaJarak() {
  digitalWrite(TRIG, LOW);
  delayMicroseconds(2);
  digitalWrite(TRIG, HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIG, LOW);
  long durasi = pulseIn(ECHO, HIGH, 30000);  
  if (durasi == 0) return 999; 
  return durasi * 0.034 / 2;
}

void loop() {
  if ((millis() - lastTime) > timerDelay) {
    if (WiFi.status() == WL_CONNECTED) {
      
      long jarak = bacaJarak();
      int s_led = 0;
      int s_buzzer = 0;

      // --- LOGIKA PERBAIKAN (SINKRON DENGAN WEB) ---
      
      if (jarak <= batas_bahaya) {
        // KONDISI BAHAYA (0 - 5 cm)
        s_led = 1;
        s_buzzer = 1;
        Serial.println("Kondisi: BAHAYA (Overload)");
      } 
      else if (jarak <= batas_normal) {
        // KONDISI NORMAL (6, 7, 8 cm AKAN MASUK SINI)
        // LED Nyala, Buzzer Mati
        s_led = 1;  
        s_buzzer = 0;
        Serial.println("Kondisi: NORMAL");
      } 
      else {
        // KONDISI AMAN (> 8 cm)
        s_led = 0;
        s_buzzer = 0;
        Serial.println("Kondisi: AMAN / KOSONG");
      }

      // Kontrol Fisik
      digitalWrite(LEDPIN, s_led);
      digitalWrite(BUZZER, s_buzzer);

      // Kirim ke Web
      HTTPClient http;
      http.begin(serverName);
      http.addHeader("Content-Type", "application/x-www-form-urlencoded");

      String httpRequestData = "jarak=" + String(jarak) + 
                               "&led=" + String(s_led) + 
                               "&buzzer=" + String(s_buzzer);

      int httpResponseCode = http.POST(httpRequestData);
      
      // Cek di Serial Monitor, pastikan LED Status bernilai 1 saat jarak 6-8 cm
      Serial.print("Jarak: "); Serial.print(jarak);
      Serial.print(" cm | LED Status: "); Serial.print(s_led); 
      Serial.print(" | HTTP: "); Serial.println(httpResponseCode);

      http.end();
    }
    lastTime = millis();
  }
}