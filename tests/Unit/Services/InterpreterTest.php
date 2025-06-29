<?php

use AirPressure\Model\AirPressurePoint;
use AirPressure\Services\Interpreter;

it('should return no current headeache too low', function() {
    $created = new DateTimeImmutable();
    $lastModified = new DateTimeImmutable();
    
    $createdPrevious = new DateTimeImmutable();
    $lastModifiedPrevious = new DateTimeImmutable();

    $current = new AirPressurePoint(1002, $created, $lastModified);
    $previous = new AirpressurePoint(1001, $createdPrevious, $lastModifiedPrevious);

    $interpreted = Interpreter::interpret($current, $previous);

    expect($interpreted->currentPressureChanceForHeadache)->toBe(false);
});

it('should return no current headeache too high', function() {
    $created = new DateTimeImmutable();
    $lastModified = new DateTimeImmutable();
    
    $createdPrevious = new DateTimeImmutable();
    $lastModifiedPrevious = new DateTimeImmutable();

    $current = new AirPressurePoint(1008, $created, $lastModified);
    $previous = new AirpressurePoint(1001, $createdPrevious, $lastModifiedPrevious);

    $interpreted = Interpreter::interpret($current, $previous);

    expect($interpreted->currentPressureChanceForHeadache)->toBe(false);
});

it('should return current headache with 1005', function() {
    $created = new DateTimeImmutable();
    $lastModified = new DateTimeImmutable();
    
    $createdPrevious = new DateTimeImmutable();
    $lastModifiedPrevious = new DateTimeImmutable();

    $current = new AirPressurePoint(1005, $created, $lastModified);
    $previous = new AirpressurePoint(1001, $createdPrevious, $lastModifiedPrevious);

    $interpreted = Interpreter::interpret($current, $previous);

    expect($interpreted->currentPressureChanceForHeadache)->toBe(true);
});

it('should return no historic headache with range 5', function() {
    $created = new DateTimeImmutable();
    $lastModified = new DateTimeImmutable();
    
    $createdPrevious = new DateTimeImmutable();
    $lastModifiedPrevious = new DateTimeImmutable();

    $current = new AirPressurePoint(1002, $created, $lastModified);
    $previous = new AirpressurePoint(1007, $createdPrevious, $lastModifiedPrevious);

    $interpreted = Interpreter::interpret($current, $previous);

    expect($interpreted->historicPressureChanceForHeadache)->toBe(false);
});
it('should return no historic headache with range 11', function() {
    $created = new DateTimeImmutable();
    $lastModified = new DateTimeImmutable();
    
    $createdPrevious = new DateTimeImmutable();
    $lastModifiedPrevious = new DateTimeImmutable();

    $current = new AirPressurePoint(1002, $created, $lastModified);
    $previous = new AirpressurePoint(1013, $createdPrevious, $lastModifiedPrevious);

    $interpreted = Interpreter::interpret($current, $previous);

    expect($interpreted->historicPressureChanceForHeadache)->toBe(false);
});
it('should return historic headache with range 6', function() {
    $created = new DateTimeImmutable();
    $lastModified = new DateTimeImmutable();
    
    $createdPrevious = new DateTimeImmutable();
    $lastModifiedPrevious = new DateTimeImmutable();

    $current = new AirPressurePoint(1008, $created, $lastModified);
    $previous = new AirpressurePoint(1002, $createdPrevious, $lastModifiedPrevious);

    $interpreted = Interpreter::interpret($current, $previous);

    expect($interpreted->historicPressureChanceForHeadache)->toBe(true);
});
it('should return historic headache with range 10', function() {
    $created = new DateTimeImmutable();
    $lastModified = new DateTimeImmutable();
    
    $createdPrevious = new DateTimeImmutable();
    $lastModifiedPrevious = new DateTimeImmutable();

    $current = new AirPressurePoint(1002, $created, $lastModified);
    $previous = new AirpressurePoint(1012, $createdPrevious, $lastModifiedPrevious);

    $interpreted = Interpreter::interpret($current, $previous);

    expect($interpreted->historicPressureChanceForHeadache)->toBe(true);
});