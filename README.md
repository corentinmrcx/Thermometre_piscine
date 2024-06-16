# Thermomètre de Piscine
Projet Arduino d'un thermomètre connecté pour surveiller la température de ma piscine à distance depuis un site internet.

## 1. Configuration du matériel :
### Branchement ESP --> thermomètre DS18B20 :
- Connecter le capteur DS18B20 à l'ESP8266 selon le schéma de câblage.
![Cablage DS18B20](img/cablage_ds18b20.png)
![Shema ESP8266](img/esp8266_shema.png)

## 2.Programmation de l’ESP8266
### Récupérer une température sur l'ESP client :

- Écrire un programme Arduino pour lire la température du capteur DS18B20.
    ```
    #include "OneWire.h"
    #include "DallasTemperature.h"

    #define ONE_WIRE_BUS 4
    OneWire oneWire(ONE_WIRE_BUS);
    DallasTemperature sensors(&oneWire);

    void setup() {
    Serial.begin(115200);
    sensors.begin();
    delay(10);
    
    }

    void loop() {
    sensors.requestTemperatures();
    float temperature = sensors.getTempCByIndex(0);
    Serial.println(temperature);
    delay(5000);
    }
    ```

### Envoyer la température au serveur ESP :
Ajout des bibliothèques `ESP8266WiFi` et `WifiClient` pour permettre la connexion de l'ESP client au Wifi.

1. Création d'un fichier de configuration pour y stocker les informations de connexion telle que le SSID ou le password. 
    ```
    // config.h

    #define SSID "Livebox-4810"
    #define PASSWORD "7SypWJtqvaLHgYwHKV"
    ```
2. Modification du code client pour ajouter la configuration WiFi.
    ```
    // Bibliothèques Wifi
    #include <ESP8266WiFi.h>
    #include <WiFiClient.h>

    // Information de connexion
    #include "config.h"
    const char* serverESPIP = "192.168.1.53"; // Adresse IP de l'ESP8266 serveur

    WiFiClient client;

    void setup() {

    Wifi.begin(SSID, PASSWORD);
    while (WiFi.status()!= WL_CONNECTED){
        delay(1000)
        Serial.println("Connexion en cours");
    }
    Serial.println("Connecté au WIFI !");
    
    }

    void loop() {

    if (client.connect(serverESPIP, 80)){
    String url = "/temperature?value=" + String(temperature);
    client.print(String("GET ") + url + " HTTP/1.1\r\n" +
                "Host: " + serverESPIP + "\r\n" +
                "Connection: close\r\n\r\n");
    }
    delay(5000);
    }
    ```
