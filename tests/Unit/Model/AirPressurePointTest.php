<?php

use AirPressure\Model\AirPressurePoint;

it('should return a valid AirPressurePoint model without id', function() {
    $now = new DateTimeImmutable('2025-03-04 05:06:07');
    $pressure = 1005.0;

    $airPressureModel = new AirPressurePoint(
        pressure: $pressure,
        created: $now,
        last_modified: $now
    );

    expect($airPressureModel->getPressure())->toEqual($pressure);
    expect($airPressureModel->getCreated())->toEqual($now);
    expect($airPressureModel->getLastModified())->toEqual($now);
    expect($airPressureModel->getId())->toBeNull();

    $newDate = new DateTimeImmutable();
    $newPressure = 1006.0;

    $airPressureModel->setPressure($newPressure);
    $airPressureModel->setLastModified($newDate);

    expect($airPressureModel->getPressure())->toEqual($newPressure);
    expect($airPressureModel->getLastModified())->toEqual($newDate);
});

it('should return a valid AirPressurePoint with id', function() {
    $id = 34987;

    $airPressureModel = new AirPressurePoint(
        pressure: 1004.4,
        created: new DateTimeImmutable(),
        last_modified: new DateTimeImmutable(),
        id: $id
    );

    expect($airPressureModel->getId())->toBe($id);
});

it('should return json', function() {
    $now = new DateTimeImmutable('2025-03-04 05:06:07');
    $pressure = 1005.0;
    $id = 89732;

    $airPressureModel = new AirPressurePoint(
        pressure: $pressure,
        created: $now,
        last_modified: $now,
        id: $id
    );

    $json = json_encode($airPressureModel);
    expect($json)->toBe('{"id":' . $id . ',"pressure":' . $pressure . ',"last_modified":"' . $now->format(DateTimeInterface::ATOM) . '","created":"' . $now->format(DateTimeInterface::ATOM) . '"}');
});

it('should return json without id', function() {
    $now = new DateTimeImmutable('2025-03-04 05:06:07');
    $pressure = 1005.0;

    $airPressureModel = new AirPressurePoint(
        pressure: $pressure,
        created: $now,
        last_modified: $now,
    );

    $json = json_encode($airPressureModel);
    expect($json)->toBe('{"id":null,"pressure":' . $pressure . ',"last_modified":"' . $now->format(DateTimeInterface::ATOM) . '","created":"' . $now->format(DateTimeInterface::ATOM) . '"}');
});