<?php

declare(strict_types=1);
namespace Class;

use DataBase\MyPdo;
require_once __DIR__ . '/../DataBase/MyPdo.php';

class Temperature
{
    private ?int $id;
    private float $temperature;
    private ?string $time;

    public function __construct(float $temperature, ?string $time, ?int $id = null){
        $this->temperature = $temperature;
        $this->time = $time;
        $this -> id = $id;
    }

    public function getId(): ?int{
        return $this->id;
    }

    public function getTemperature(): float{
        return $this->temperature;
    }

    public function getTime(): ?string{
        return $this->time;
    }

    public function insertTemperature(float $temperature): Temperature{
        $stmt = MyPdo::getInstance()->prepare(
            <<<'SQL'
            INSERT INTO SensorData (temperature) VALUES (:temperature)
SQL
        );
        $stmt ->execute(["temperature" => $temperature]);
        return $this;
    }

    public static function getLastTemperature(): ?Temperature{
        $stmt = MyPdo::getInstance()->prepare(
            <<<'SQL'
            SELECT id, temperature, DATE_FORMAT(time, "%H:%i") FROM SensorData ORDER BY id DESC LIMIT 1
SQL
        );
        $result = $stmt->execute();

        if ($result) {
            $row = $stmt->fetch();
            if ($row) {
                return new Temperature((float) $row['temperature'], (string) $row['DATE_FORMAT(time, "%H:%i")']);
            }
        }

        return null;
    }

    public static function getAllTemperatures(): array{
        $stmt = MyPdo::getInstance()->prepare(
            <<<'SQL'
            SELECT id, temperature, DATE_FORMAT(time, "%H:%i") FROM SensorData ORDER BY id DESC
SQL
        );
        $stmt->execute();
        $rows =  $stmt->fetchAll();

        $temperaturesTab = [];
        foreach ($rows as $row) {
            $temperaturesTab[] = new Temperature((float) $row['temperature'], $row['DATE_FORMAT(time, "%H:%i")']);
        }

        return $temperaturesTab;
    }

    public static function maxTemperature(): float{
        $stmt = MyPdo::getInstance()->prepare(
            <<<'SQL'
            SELECT MAX(temperature) as max_temp FROM SensorData WHERE DATE(time) = CURDATE()
SQL
        );
        $stmt->execute();
        $row =  $stmt->fetch();

        return (float)$row['max_temp'];
    }

    public static function minTemperature(): float{
        $stmt = MyPdo::getInstance()->prepare(
            <<<'SQL'
            SELECT MIN(temperature) as min_temp FROM SensorData WHERE DATE(time) = CURDATE()
SQL
        );
        $stmt->execute();
        $row =  $stmt->fetch();

        return (float)$row['min_temp'];
    }

    public static function avgTemperature(): float{
        $stmt = MyPdo::getInstance()->prepare(
            <<<'SQL'
            SELECT ROUND(AVG(temperature), 2) as avg_temp FROM SensorData WHERE DATE(time) = CURDATE()
SQL
        );
        $stmt->execute();
        $row =  $stmt->fetch();

        return (float)$row['avg_temp'];
    }


    public static function getTemperatureStats(): string {
        $stmt = MyPdo::getInstance()->prepare(
            "SELECT 
                DAYNAME(time) as day, 
                MIN(temperature) as min_temp, 
                AVG(temperature) as avg_temp, 
                MAX(temperature) as max_temp
             FROM SensorData
             WHERE DATE(time) >= CURDATE() - INTERVAL 7 DAY
             GROUP BY DAYNAME(time)
             ORDER BY FIELD(DAYNAME(time), 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $englishToFrenchDays = [
            'Monday' => 'Lundi',
            'Tuesday' => 'Mardi',
            'Wednesday' => 'Mercredi',
            'Thursday' => 'Jeudi',
            'Friday' => 'Vendredi',
            'Saturday' => 'Samedi',
            'Sunday' => 'Dimanche'
        ];

        foreach ($data as &$row) {
            $row['day'] = $englishToFrenchDays[$row['day']];
        }

        return json_encode($data);
    }

    public function __toString(): string {
        return (string) $this->temperature;
    }

}