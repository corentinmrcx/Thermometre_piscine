<?php

declare(strict_types=1);
namespace Class;

use DataBase\MyPdo;

namespace Class;
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

    public function getLastTemperature(): Temperature{
        $stmt = MyPdo::getInstance()->prepare(
            <<<'SQL'
            SELECT id, temperature FROM sensorData ORDER BY id DESC LIMIT 1
SQL
        );
        return $this;
    }

    public static function getAllTemperatures(): Array{
        $stmt = MyPdo::getInstance()->prepare(
            <<<'SQL'
            SELECT id, temperature FROM sensorData ORDER BY id DESC
SQL
        );
        return $stmt->fetchAll();
    }

    public static function maxTemperature(): int{
        $stmt = MyPdo::getInstance()->prepare(
            <<<'SQL'
            SELECT MAX(temperature) FROM sensorData WHERE TO_DATE(time, "dd/mm/YYYY") = SYSDATE ORDER BY id DESC
SQL
        );
        return $stmt->fetchColumn();
    }

}