<?php

namespace AirPressure\Services\Repository;

use AirPressure\Model\AirPressurePoint;
use DateTimeInterface;

interface RepositoryInterface {
    public function get(int $id): AirPressurePoint;

    public function getWithLastModifiedAndPressure(DateTimeInterface $lastModified, float $pressure): ?AirPressurePoint;

    public function save(AirPressurePoint $model): AirPressurePoint;

    public function delete(int $id): void;

    /**
     * @return AirPressurePoint[]
     */
    public function list(): array;

    public function listWithRange(DateTimeInterface $start, DateTimeInterface $end): array;
}