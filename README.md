# Thermomètre de Piscine
Projet Arduino d'un thermomètre connecté pour surveiller la température de ma piscine à distance depuis un site internet.

## 1. Configuration du matériel :
### Branchement ESP --> thermomètre DS18B20 :
- Connecter le capteur DS18B20 à l'ESP8266 selon le schéma de câblage.
![Cablage DS18B20](img/cablage_ds18b20.png)
![Shema ESP8266](img/esp8266_shema.png)

## 2. Programmation de l’ESP8266
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

### Récupérer la température sur l'ESP serveur :
Ajout des bibliothèques `ESP8266WiFi` et `ESP8266WebServer` pour pouvoir utilisé l'ESP8266 comme serveur.

1. Ajout du fichier de configuration les informations de connexion telle que le SSID ou le password comme pour l'ESP client.

2. Code de l'ESP serveur : 
    ```
    #include <ESP8266WiFi.h>
    #include <ESP8266WebServer.h>
    #include <ESP8266HTTPClient.h>
    
    // Information de connexion
    #include "config.h"
    
    ESP8266WebServer server(80);
    WiFiClientSecure wifiClient;
    
    float temperature = 0.0;
    
    void handleRoot(){
    String html = "<html><body><h1>Température actuelle : " + String(temperature) + " °C</h1></body></html>";
    server.send(200, "text/html", html);
    }
    
    void handleTemperature(){
    if (server.arg("value") != ""){
    float temperature = server.arg("value").toFloat();
    sendTemperatureToWebServer(temperature, KeyValue);
    }
    handleRoot();
    }
    
    void sendTemperatureToWebServer(float temp, String key){
    HTTPClient http;
    wifiClient.setInsecure();
    http.begin(wifiClient, webServerURL);
    
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    String data = "key=" + key + "&value=" + String(temp);
    int httpResponseCode = http.POST(data);
    
    Serial.print("Envoie des données : ");
    Serial.println(data);
    Serial.print("Code de réponse HTTP : ");
    Serial.println(httpResponseCode);
        
        if (httpResponseCode > 0) {
            String response = http.getString();
            Serial.print("Réponse du serveur : ");
            Serial.println(response);
        } else {
            Serial.print("Erreur lors de l'envoi des données : ");
            Serial.println(http.errorToString(httpResponseCode).c_str());
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
    ```
   
### Envoyer la temperature au serveur WEB 
1. Ajout de la bibliothèque `ESP8266HTTPClient` pour envoyer une requête HTTP.
2. Modification du code de l'ESP : 
    ```
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
    ```

## 3. Développement de la Page WEB
### 1. Recevoir les données de température sur le serveur WEB 
**receive_temperature.php** :  Ce script PHP reçoit les données de température via une requête POST, vérifie une clé de sécurité, et insère la nouvelle température dans la base de données si la clé est correcte.

### 2. Traiter les données de température
**Temperature.php** : Cette classe représente une température avec son identifiant, sa valeur et le moment de l'enregistrement. Elle inclut des méthodes pour insérer une nouvelle température, récupérer les dernières températures enregistrées, ainsi que les statistiques de températures (max, min, moyenne).

- ``insertTemperature`` : Insère une nouvelle température dans la base de données.
- ``getLastTemperature`` : Récupère la dernière température enregistrée.
- ``getAllTemperatures`` : Récupère toutes les températures enregistrées.
- ``maxTemperature`` : Récupère la température maximale du jour.
- ``minTemperature`` : Récupère la température minimale du jour.
- ``avgTemperature`` : Récupère la température moyenne du jour.
- ``getTemperatureStats`` : Récupère les statistiques des températures des 7 derniers jours.
- ``__toString`` : Convertit l'objet Temperature en chaîne de caractères.

### 3. Afficher ces données
**index.php** : Cette page web affiche la dernière température enregistrée, ainsi que des statistiques (min, max, moyenne) et un tableau de toutes les températures.
Elle utilise la bibliothèque Chart.js pour afficher un graphique des températures minimales, moyennes et maximales des 7 derniers jours.

Pour concevoir cette page, on utilise la classe ``WebPage.php`` qui permet de construire une page HTML :

    <?php
    
    declare(strict_types=1);
    
    namespace Html;
    
    require_once "StringEscaper.php";
    class WebPage
    {
    use StringEscaper;
    
        private string $head;
        private string $title;
        private string $body;
    
        public function __construct(string $title = "")
        {
            $this -> title = $title;
            $this -> head = "";
            $this -> body = "";
        }
    
        /**
         * Retourne le contenue de HEAD.
         * @return string
         */
        public function getHead(): string
        {
            return $this->head;
        }
    
        /**
         * Retourne le titre de la page.
         * @return string
         */
        public function getTitle(): string
        {
            return $this->title;
        }
    
        /**
         * Modificateur de la valeur du titre.
         * @param string $title
         */
        public function setTitle(string $title): void
        {
            $this->title = $title;
        }
    
        /**
         * Retourne le contenue du body.
         * @return string
         */
        public function getBody(): string
        {
            return $this->body;
        }
    
        /**
         * Fonction qui ajoute du contenu a la balise HEAD.
         * @param string $content
         * @return void
         */
        public function appendToHead(string $content): void
        {
            $this -> head .= $content;
        }
    
        /**
         * Fonction qui ajoute du CSS.
         * @param string $css
         * @return void
         */
        public function appendCss(string $css): void
        {
            $this -> head .= <<<HTML
            <style>{$css}</style>
            HTML;
        }
    
        /**
         * Fonction qui ajoute la balise link vers le fichier css.
         * @param string $url
         * @return void
         */
        public function appendCssUrl(string $url): void
        {
            $this -> head .= <<<HTML
            <link rel="stylesheet" href={$url}>
        HTML;
        }
    
        /**
         * Fonction qui ajoute un script JS.
         * @param string $js
         * @return void
         */
        public function appendJS(string $js): void
        {
            $this -> head .= <<<HTML
            <script>{$js}</script>
            HTML;
        }
    
        /**
         * Fonction qui ajoute un lien vers un fichier JS.
         * @param string $url
         * @return void
         */
        public function appendJsUrl(string $url): void
        {
            $this -> head .= <<<HTML
            <script src="{$url}"></script>
        HTML;
        }
    
        /**
         * Fonction qui ajoute du contenue a la balise body.
         * @param string $content
         * @return void
         */
        public function appendContent(string $content): void
        {
            $this -> body .= $content;
        }
    
        /**
         * Fonction qui convertit en HTML nos différentes instances.
         * @return string
         */
        public function toHTML(): string
        {
            return <<<HTML
            <!doctype html>
            <html lang="fr">
            <head>
            <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="ie=edge">
                <title>{$this->title}</title>
                {$this -> head}
            </head>
            <body>
              {$this->body}
            </body>
            </html>
    HTML;
    }
    
        /**
         * Fonction qui donne la date et l'heure de la dernière modification du script principale.
         * @return string
         */
        public function getLastModification(): string
        {
            return date("d F Y  H:i:s.", getlastmod());
        }
    }