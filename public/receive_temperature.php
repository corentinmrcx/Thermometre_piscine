<?php

declare(strict_types=1);
require_once "Class/Temperature.php";

$key = "a5vf7adsm";

if (!empty($_POST["value"])) {
    if ($_POST["key"] === $key){
        $temperature = (float) $_POST["value"];
        $newTemp = new Temperature($temperature);
        $newTemp ->insertTemperature($temperature);
    }
    else{
        throw new Exception("Invalid key");
    }
}
else{
    $temperature = "Aucune donnée de temperature disponible";
}