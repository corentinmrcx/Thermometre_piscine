<?php

declare(strict_types=1);

use Class\Temperature;
require_once "config.php";
require_once "Class/Temperature.php";

if (isset($_POST["value"])) {
    if ($_POST["key"] == SECURITY_KEY){
        $temperature = (float) $_POST["value"];
        $newTemp = new Temperature($temperature);
        $newTemp -> insertTemperature($temperature);
    }
    else{
        throw new Exception("Invalid key");
    }
}
else{
    echo "Aucune donnée de temperature disponible";
}