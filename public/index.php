<?php

use AirPressure\Services\Repository\MySQLRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write(<<<HTML
<!doctype html>
<html>
    <head>
        <title>Airpressure</title>
    </head>
    <body>
        Test
    </body>
</html>
HTML);
    return $response;
});

$app->get('/data[/{params:.*}]', function (Request $request, Response $response, $args) {
    parse_str($request->getUri()->getQuery(), $parsed);

    $start = new DateTimeImmutable($parsed['start']);
    $end = new DateTimeImmutable($parsed['end']);

    $repository = new MySQLRepository();
    $response->getBody()->write(json_encode(value: $repository->listWithRange($start, $end), flags: JSON_PRETTY_PRINT));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();