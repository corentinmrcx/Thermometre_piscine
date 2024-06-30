<?php
declare(strict_types=1);

use Class\Temperature;
use DataBase\MyPdo;
use Html\WebPage;
require_once "Class/Html/WebPage.php";
require_once "Class/Temperature.php";

$webPage = new WebPage();
$webPage->setTitle('Temperature Piscine');
$webPage -> appendCssUrl("styles.css");

$lastTemperature = Temperature::getLastTemperature();
$allTemperature = Temperature::getAllTemperatures();
$max = Temperature::maxTemperature();
$webPage->appendContent(
    <<<HTML
        <div class="container">
            <p>Dernière temperature : {$lastTemperature}</p>
            <p>Temperature maximal : {$max}</p>
            <p>Les dernières temperatures :</p>
HTML
);

for ($i = 0; $i < count($allTemperature); $i++) {
    $webPage->appendContent("$allTemperature[$i]<br>");
}

$webPage -> appendContent("</div>");
echo $webPage->toHTML();
