<?php

namespace AirPressure\Services\Fetcher;

use AirPressure\Model\AirPressurePoint;

abstract class AbstractFetcher {
    abstract public function fetchCurrent(): ?AirPressurePoint;
    protected string $latitude;
    protected string $longitude;

    protected function _parseLatitudeLongitude(): string {
        if (!preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/', $this->latitude)) {
            throw new \Exception("Latitude [$this->latitude] is not valid");
        }

        if (!preg_match('/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $this->longitude)) {
            throw new \Exception("Longitude [$this->longitude] is not valid");
        }

        return $this->latitude . ',' . $this->longitude;
    }
}