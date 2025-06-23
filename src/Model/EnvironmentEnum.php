<?php

namespace AirPressure\Model;

use AirPressure\Config;
use Exception;

enum EnvironmentEnum: string {
    case Development = 'development';
    case Production = 'production';

    public static function getConfigured(): EnvironmentEnum {
        return self::from(Config::get('environment'));
    }
}