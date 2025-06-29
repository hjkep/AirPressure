<?php

namespace AirPressure;

trait Loggable {
    private static function _log(string $message): void {
        echo __CLASS__ . ': ' . $message . PHP_EOL;
    }
}