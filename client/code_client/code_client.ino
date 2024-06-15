// Bibliothèques DS18B20
#include "OneWire.h"
#include "DallasTemperature.h"

// Bibliothèques Wifi
#include <ESP8266WiFi.h>
#include <WiFiClient.h>

// Information de connexion
#include "config.h"

#define ONE_WIRE_BUS 4
OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature sensors(&oneWire);

WiFiClient client;

void setup() {
  Serial.begin(115200);
  sensors.begin();
  delay(10);

  Wifi.begin(ssid, password);
  while (WiFi.status()!= WL_CONNECTED){
    delay(1000)
    Serial.println("Connexion en cours");
  }
  Serial.println("Connecté au WIFI !");
  
}

void loop() {
  sensors.requestTemperatures();
  float temperature = sensors.getTempCByIndex(0);

  if (client.connect(serverIP, 80)){
  String url = "/temperature?value=" + String(temperature);
  client.print(String("GET ") + url + " HTTP/1.1\r\n" +
               "Host: " + serverIP + "\r\n" +
               "Connection: close\r\n\r\n");
}
delay(5000);
}
