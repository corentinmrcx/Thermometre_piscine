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
$min = Temperature::minTemperature();
$avg = Temperature::avgTemperature();


$webPage->appendContent(
    <<<HTML
    <div class="temperature">
        <div class="temp-value">{$lastTemperature}</div>
        <div class="time">{$lastTemperature->getTime()}</div>
    </div>

    <div class="stats">
        <div class="stat">
            <div class="stat-label">Min</div>
            <div class="stat-value">{$min}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Max</div>
            <div class="stat-value">{$max}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Moy</div>
            <div class="stat-value">{$avg}</div>
        </div>
        </div>
        
        <div class="table-container">
            <table>
                <tr>
                    <th>Heure</th>
                    <th>Température</th>
                </tr>
                <tr>
HTML
);

for ($i = 0; $i < count($allTemperature); $i++) {
    $webPage->appendContent(
        <<<HTML
                <tr>
                    <td>{$allTemperature[$i]->getTime()}</td>
                    <td>{$allTemperature[$i]->getTemperature()}</td>
                </tr>
HTML);
}

$webPage -> appendContent(
    <<<HTML
            </table>
    </div>
</body>
</html>
HTML);
echo $webPage->toHTML();
