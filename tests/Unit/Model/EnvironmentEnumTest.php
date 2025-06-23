<?php

use AirPressure\Model\EnvironmentEnum;

it('should return the configured environment', function() {
    $configured = EnvironmentEnum::getConfigured();
    expect($configured)->toBe(EnvironmentEnum::Development);
});