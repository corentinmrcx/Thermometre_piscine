<?php
declare(strict_types=1);

use Class\Temperature;
use Html\WebPage;

$webPage = new WebPage();
$webPage->setTitle('Temperature Piscine');
$webPage -> appendCssUrl("style.css");

$lastTemperature = Temperature::getLastTemperature();

$webPage->appendContent(
    <<<HTML
        <div class="container">
            <p>{$lastTemperature}</p>
        </div>
HTML
);

echo $webPage->toHTML();