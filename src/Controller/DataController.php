<?php

namespace AirPressure\Controller;

use AirPressure\Config;
use AirPressure\Services\Interpreter;
use AirPressure\Services\Repository\MySQLRepository;
use DateTimeImmutable;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class DataController {
    public function serve(Request $request, Response $response, $args): Response {
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
            'interpreted' => $interpreted,
            'location' => Config::get('location')
        ], flags: JSON_PRETTY_PRINT));

        $response->withHeader('Content-Type', 'application/json');
        return $response;
    }
}