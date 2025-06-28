<?php

use AirPressure\Controller\DataController;
use AirPressure\Controller\IndexController;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

define('PUBLIC_DIR', __DIR__);
define('APP_DIR', realpath(__DIR__ . '/../'));

$app = AppFactory::create();

$app->get('/', [IndexController::class, 'serve']);
$app->get('/data[/{params:.*}]', [DataController::class, 'serve']);

$app->run();