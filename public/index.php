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

$temperatureStatsJson = Temperature::getTemperatureStats();

$webPage->appendToHead(
    <<<HTML
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
HTML
);

$webPage->appendContent(
    <<<HTML
    <div class="temperature">
        <div class="temp-value">{$lastTemperature}&#xB0;C</div>
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
    <div class="chart">
        <canvas id="temperatureChart" width="400" height="200"></canvas>
    </div>
</body>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const data = JSON.parse('{$temperatureStatsJson}');
            console.log(data);
            const days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
            const minTemps = [];
            const avgTemps = [];
            const maxTemps = [];

            days.forEach(day => {
                const stats = data.find(d => d.day === day);
                if (stats) {
                    minTemps.push(stats.min_temp);
                    avgTemps.push(stats.avg_temp);
                    maxTemps.push(stats.max_temp);
                } else {
                    minTemps.push(0);
                    avgTemps.push(0);
                    maxTemps.push(0);
                }
            });

            const ctx = document.getElementById('temperatureChart').getContext('2d');
            const temperatureChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: days,
                    datasets: [
                        {
                            label: 'Min',
                            data: minTemps,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1, 
                        },
                        {
                            label: 'Avg',
                            data: avgTemps,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                        },
                        {
                            label: 'Max',
                            data: maxTemps,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                        }
                    ]
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: 'Températures Min, Moy, Max - 7 derniers jours'
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: false
                        },
                        y: {
                            stacked: false,
                            beginAtZero: true,
                        }
                    }
                }
            });
        });
    </script>
</html>
HTML);

echo $webPage->toHTML();
