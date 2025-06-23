<?php

use AirPressure\Config;

describe('config', function(): void    {
    it('should get an existing value', function(): void {
        expect(Config::get('database.name'))->toBe('airpressure');
    });

    it('should not return a non-existing value', function(): void {
        expect(Config::get('some.nonexisting.value'))->toBeEmpty();
    });
});