<?php

declare(strict_types=1);
namespace Class;

use DataBase\MyPdo;
require_once __DIR__ . '/../DataBase/MyPdo.php';

class Temperature
{
    private ?int $id;
    private float $temperature;

    public function __construct(float $temperature, ?int $id = null){
        $this->temperature = $temperature;
    }

    public function getId(): ?int{
        return $this->id;
    }

    public function getTemperature(): float{
        return $this->temperature;
    }

    public function insertTemperature(float $temperature): Temperature{
        $stmt = MyPdo::getInstance()->prepare(
            <<<'SQL'
            INSERT INTO sensorData (temperature) VALUES (:temperature)
SQL
        );
        $stmt ->execute(["temperature" => $temperature]);
        return $this;
    }

    public static function getLastTemperature(): ?Temperature{
        $stmt = MyPdo::getInstance()->prepare(
            <<<'SQL'
            SELECT id, temperature FROM sensorData ORDER BY id DESC LIMIT 1
SQL
        );
        $result = $stmt->execute();

        if ($result) {
            $row = $stmt->fetch();
            if ($row) {
                return new Temperature((float) $row['temperature']);
            }
        }

        return null;
    }

    public static function getAllTemperatures(): array{
        $stmt = MyPdo::getInstance()->prepare(
            <<<'SQL'
            SELECT id, temperature FROM sensorData ORDER BY id DESC
SQL
        );
        $stmt->execute();
        $rows =  $stmt->fetchAll();

        $temperaturesTab = [];
        foreach ($rows as $row) {
            $temperaturesTab[] = new Temperature((float) $row['temperature']);
        }

        return $temperaturesTab;
    }

    public static function maxTemperature(): float{
        $stmt = MyPdo::getInstance()->prepare(
            <<<'SQL'
            SELECT MAX(temperature) as max_temp FROM sensorData WHERE DATE(time) = CURDATE()
SQL
        );
        $stmt->execute();
        $row =  $stmt->fetch();

        return (float)$row['max_temp'];
    }

    public function __toString(): string {
        return (string) $this->temperature;
    }

}