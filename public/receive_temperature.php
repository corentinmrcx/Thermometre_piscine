<?php

declare(strict_types=1);

if (!empty($_GET["value"])) {
    $temperature = htmlspecialchars($_GET["value"]);
}
else{
    $temperature = "Aucune donnée de temperature disponible";
}

$html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Température de la Piscine</title>
</head>
<body>
    <div class="container">
        {$temperature}
    </div>    
HTML;

echo $html;