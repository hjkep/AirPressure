<?php

namespace AirPressure\Services;

use AirPressure\Model\AirPressurePoint;
use AirPressure\Model\InterpreterResult;

class Interpreter {
    //Differences in this range could lead to migraines
    private const DIFFERENCE_RANGE_MIN = 6;
    private const DIFFERENCE_RANGE_MAX = 10;

    //Atmospheric pressure between these values could also lead to migraines
    private const HEADACHE_MIN = 1003;
    private const HEADACHE_MAX = 1007;

    public static function interpret(AirPressurePoint $currentAirPressure, AirPressurePoint $previousAirPressure): InterpreterResult   {
        $difference = abs($previousAirPressure->getPressure() - $currentAirPressure->getPressure());
        return new InterpreterResult(
            $currentAirPressure->getPressure() >= self::HEADACHE_MIN && $currentAirPressure->getPressure() <= self::HEADACHE_MAX, 
            $difference >= self::DIFFERENCE_RANGE_MIN && $difference <= self::DIFFERENCE_RANGE_MAX
        );
    }
}