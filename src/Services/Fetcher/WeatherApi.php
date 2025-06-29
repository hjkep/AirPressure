<?php

namespace AirPressure\Services\Fetcher;

use AirPressure\Loggable;
use AirPressure\Model\AirPressurePoint;
use AirPressure\Model\CurrentWeather\CurrentWeatherResponse;
use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use stdClass;

class WeatherApi extends AbstractFetcher {
    use Loggable;

    private const BASE_URL = "https://api.weatherapi.com/v1/current.json";
    private string $apiKey;

    public function configure(string $latitude, string $longitude, string $apiKey): static {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->apiKey = $apiKey;
        return $this;
    }

    public function fetchCurrent (): ?AirPressurePoint {
        $currentWeather = $this->_getCurrentWeather();
        if (!$currentWeather) {
            return null;
        }

        $lastModified = (new DateTimeImmutable())->createFromFormat('U', $currentWeather->current->last_updated_epoch);
        if (is_bool($lastModified)) {
            return null;
        }

        return new AirPressurePoint(
            pressure: $currentWeather->current->pressure_mb, 
            created: (new DateTimeImmutable()), 
            last_modified: $lastModified
        );
    }

    private function _getCurrentWeather(): ?stdClass {
        $client = $this->_getClient();
        try {
            $result = $client->request('GET', '', [
                RequestOptions::QUERY => [
                    'key' => $this->apiKey,
                    'q' => $this->_parseLatitudeLongitude()
                ]
            ]);

            if ($result->getStatusCode() !== 200) {
                $this->_log('Status not 200 when fetching with WeatherApi: ' . $result->getStatusCode());
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