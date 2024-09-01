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
$lastTemperatureTime = date('Y-m-d\TH:i:s', strtotime(Temperature::getLastTemperatureTime()));

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
            <div class="stat-label">Moy</div>
            <div class="stat-value">{$avg}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Max</div>
            <div class="stat-value">{$max}</div>
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
                    <td>{$allTemperature[$i]->getTemperature()}&#xB0;C</td>
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

            const dates = data.map(d => d.days); 
            const minTemps = data.map(d => d.min_temp);
            const avgTemps = data.map(d => d.avg_temp);
            const maxTemps = data.map(d => d.max_temp);

            const ctx = document.getElementById('temperatureChart').getContext('2d');
            const temperatureChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Min',
                            data: minTemps,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1, 
                        },
                        {
                            label: 'Moy',
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
                            text: 'Températures - 7 derniers jours'
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
        
        document.addEventListener("DOMContentLoaded", function() {
            const lastTemperatureTimeString = '$lastTemperatureTime';
            console.log("Date formatée envoyée à JS:", lastTemperatureTimeString);
        
            const lastTemperatureTime = new Date(lastTemperatureTimeString);
            const currentTime = new Date();
        
            console.log("Dernière température enregistrée:", lastTemperatureTime);
            console.log("Heure actuelle:", currentTime);
        
            const timeDifference = currentTime - lastTemperatureTime; // Différence en millisecondes
        
            console.log("Différence de temps en millisecondes:", timeDifference);
        
            if (timeDifference > 86400000 ) {
                alert("Aucune température reçue depuis plus de 24 heures. Le thermomètre est peut-être hors tension car la piscine est probablement démontée pour la période hivernale.");
            }
        });
    </script>
</html>
HTML);

echo $webPage->toHTML();
