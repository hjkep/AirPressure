<?php

use AirPressure\Services\Interpreter;
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
        <title>Air pressure</title>
        <link rel="icon" href="static/favicon.svg" sizes="any" type="image/svg+xml">
    </head>
    <body>
        <h1>Air pressure</h1>
        <p>
            The current and historic air pressure for Alkmaar, the Netherlands.<br>
            Also includes current chance for headache based on air pressure differences or on current air pressure.
        </p>

        <p>
            There is currently <span id="headeache-difference" style="font-weight: bold">no</span> chance for headache based on air pressure differences.<br>
            There is currently <span id="headeache-current" style="font-weight: bold">no</span> chance for a headeache based on current air pressure.
        </p>

        <div>
            <canvas id="chart"></canvas>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src=" https://cdn.jsdelivr.net/npm/luxon@3.6.1/build/global/luxon.min.js "></script>
        
        <script>
            const ctx = document.getElementById('chart');
            const labels = ['One', 'Two', 'Three'];

            let start = '2025-01-01';
            let end = '2025-06-23';

            async function getChartData() {
                const url = '/data?' + new URLSearchParams({
                    start: start,
                    end: end,
                }).toString();

                const result = await fetch(url).then(response => response.json());

                if (result?.interpreted) {
                    document.getElementById('headeache-difference').innerHtml = result.interpreted.historicPressureChanceForHeadache ? 'yes' : 'no';
                    document.getElementById('headeache-current').innerHtml = result.interpreted.currentPressureChanceForHeadache ? 'yes' : 'no';
                }

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: result.list.map(e => luxon.DateTime.fromISO(e.created).toISODate()),
                        datasets: [{
                            label: 'Air pressure for Alkmaar',
                            data: result.list.map(e => {
                                return {
                                    y: e.pressure, 
                                    x: luxon.DateTime.fromISO(e.created).toISODate(),
                                    original: e
                                };
                            }
                            ),
                            fill: true,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        y: {
                            min: 900,
                            max: 1100
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    title: function(context) {
                                        return luxon.DateTime.fromISO(context[0].raw.original.created).toLocaleString(luxon.DateTime.DATETIME_FULL);
                                    },
                                    label: function(context) {
                                        return context.raw.original.pressure;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            getChartData();
        </script>
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