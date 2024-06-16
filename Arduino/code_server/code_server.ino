#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>

// Information de connexion
#include "config.h"

ESP8266WebServer server(80);

float temperature = 0.0;

void handleRoot(){
  String html = "<html><body><h1>Température actuelle : " + String(temperature) + " °C</h1></body></html>";
  server.send(200, "text/html", html);
}

void handleTemperature(){
  if (server.arg("value") != ""){
    temperature = server.arg("value").toFloat();
  }
  handleRoot();
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
