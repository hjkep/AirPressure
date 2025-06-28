<?php

namespace AirPressure;

use AirPressure\Model\ExecuteMode;
use AirPressure\Services\Cli\FetchAction;
use AirPressure\Services\Fetcher\WeatherApi;
use AirPressure\Services\Repository\MySQLRepository;
use Console_CommandLine;
use DateTimeImmutable;

error_reporting(E_ALL);
ini_set('display_errors', '1');

require __DIR__ . '/../vendor/autoload.php';

class Main {

    use Loggable;

    public static function run(): void {
        $parser = new Console_CommandLine();
        $parser->description = 'Airpressure application that gathers, interprets and returns data';
        $parser->version = '0.1';
        $parser->addOption('mode', [
            'short_name' => '-m',
            'long_name' => '--mode',
            'description' => 'What mode should be used? Choose from ' . join(array: array_map(
                callback: fn ($case) => $case->value,
                array: ExecuteMode::cases()
            ), separator: ', ')
        ]);
        
        $result = $parser->parse();

        $cliAction = $result->options['mode'] ?? ExecuteMode::Gather->value;
        if (!is_string($cliAction)) {
            self::_log('Mode not valid, stopping');
            return;
        }

        $executeMode = ExecuteMode::tryFrom($cliAction);

        switch($executeMode) {
            case ExecuteMode::Gather:
                $run = new FetchAction()
                    ->withFetcher((new WeatherApi())->configure(
                        Config::get('location.latitude'),
                        Config::get('location.longitude'),
                        Config::get('api.weatherapi.key')
                    ))
                    ->withRepository(new MySQLRepository()
                );
                $run->run();
                break;
        }
    }
}