<?php

use AirPressure\Services\Interpreter;
use AirPressure\Services\Repository\MySQLRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write(file_get_contents(__DIR__.'/../src/index.html'));
    return $response;
});

$app->get('/data[/{params:.*}]', function (Request $request, Response $response, $args) {
    parse_str($request->getUri()->getQuery(), $parsed);

    $start = new DateTimeImmutable($parsed['start']);
    $end = new DateTimeImmutable($parsed['end']);

    $repository = new MySQLRepository();

    $list = $repository->listWithRange($start, $end);

    $results = $repository->list(limit: 2, sort: 'id:DESC');
    $interpreted = null;
    if (count($results) === 2) {
        $interpreted = Interpreter::interpret($results[0], $results[1]);
    }

    $response->getBody()->write(json_encode(value: (object)[
        'list' => $list,
        'interpreted' => $interpreted
    ], flags: JSON_PRETTY_PRINT));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();