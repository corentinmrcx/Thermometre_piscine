#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>
#include <ESP8266HTTPClient.h>

// Information de connexion
#include "config.h"

ESP8266WebServer server(80);
WiFiClient wifiClient;

float temperature = 0.0;

void handleRoot(){
  String html = "<html><body><h1>Température actuelle : " + String(temperature) + " °C</h1></body></html>";
  server.send(200, "text/html", html);
}

void handleTemperature(){
  if (server.arg("value") != ""){
    temperature = server.arg("value").toFloat();
    sendTemperatureToWebServer(temperature);
  }
  handleRoot();
}

void sendTemperatureToWebServer(float temp){
    HTTPClient http;
    http.begin(wifiClient, webServerURL);

    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    String data = "key=" + KeyValue + "&value=" + String(temp);
    int httpResponseCode = http.POST(data);
    
    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println("Response: " + response);
    } else {
      Serial.println("Error on sending POST: " + String(httpResponseCode));
    }
  
    http.end();
}

void setup() {
  Serial.begin(115200);
  WiFi.begin(SSID, PASSWORD);
  
  while (WiFi.status()!= WL_CONNECTED){
    delay(1000);
    Serial.println("Connexion en cours");
  }
    Serial.println("Connecté au WIFI !");

    server.on("/", handleRoot);
    server.on("/temperature", handleTemperature);
    server.begin();
}

void loop() {
    server.handleClient();
}
