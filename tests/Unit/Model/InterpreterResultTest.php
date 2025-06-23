<?php

use AirPressure\Model\InterpreterResult;

it('should return toArray', function() {
    $interpreted = new InterpreterResult(true, true);
    $toArray = $interpreted->toArray();

    expect($toArray)->toBe([
        'currentPressureChanceForHeadache' => true,
        'historicPressureChanceForHeadache' => true
    ]);
});