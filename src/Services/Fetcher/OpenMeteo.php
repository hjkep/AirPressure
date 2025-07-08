<?php

namespace AirPressure\Services\Fetcher;

use AirPressure\Loggable;
use AirPressure\Model\AirPressurePoint;
use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use stdClass;

class OpenMeteo extends AbstractFetcher {

    use Loggable;

    private const BASE_URL = "https://api.open-meteo.com/v1/forecast";

    public function fetchCurrent(): ?AirPressurePoint {
        $currentWeather = $this->_getCurrentWeather();
        if (!$currentWeather) {
            $this->_log('No weather returned from OpenMeteo API, stopping');
            return null;
        }

        if (empty($currentWeather->daily->surface_pressure_mean[0])) {
            $this->_log('Unexpected return result from OpenMeteo API, stopping');
        }

        return new AirPressurePoint(
            pressure: $currentWeather->daily->surface_pressure_mean[0], 
            created: (new DateTimeImmutable()), 
            last_modified: (new DateTimeImmutable())
        );
    }

    public function configure(string $latitude, string $longitude): static {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        return $this;
    }

    private function _getCurrentWeather(): ?stdClass {
        $client = $this->_getClient();
        try {
            [$latitude, $longitude] = explode(',', $this->_parseLatitudeLongitude());
            $now = new DateTimeImmutable();

            $result = $client->request('GET', '', [
                RequestOptions::QUERY => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'daily' => 'surface_pressure_mean',
                    'timezone' => 'auto',
                    'start_date' => $now->format('Y-m-d'),
                    'end_date' => $now->format('Y-m-d')
                ]
            ]);

            if ($result->getStatusCode() !== 200) {
                $this->_log('Status not 200 when fetching with OpenMeteo: ' . $result->getStatusCode());
                return null;
            }

            $contents = $result->getBody()->getContents();
            
            return json_decode(json: $contents, flags: JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            $this->_log($e->getMessage());
            return null;
        }
    }

    protected function _getClient(): Client {
        return new Client([
            'base_uri' => $this::BASE_URL
        ]);
    }
}