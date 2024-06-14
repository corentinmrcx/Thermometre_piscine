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