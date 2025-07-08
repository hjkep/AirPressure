<?php

namespace AirPressure;

use \Noodlehaus\Config as NoodleConfig;

class Config {
    private static NoodleConfig $instance;

    public static function getEnvironment(): string {
        return getenv('APP_ENV') ?? 'development';
    }

    public static function get(string $key): mixed {
        if (!isset(self::$instance)) {
            self::$instance = NoodleConfig::load(
                values: __DIR__ . '/../config/config.json');
        }

        return self::$instance->get($key);
    }
}