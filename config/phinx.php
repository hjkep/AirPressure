<?php

use AirPressure\Config;

return
[
    'paths' => [
        'migrations' => Config::get('phinx.migrations'),
        'seeds' => Config::get('phinx.seeds')
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => 'INSERT_HOST_HERE',
            'name' => 'airpressure',
            'user' => 'airpressure',
            'pass' => 'INSERT_PASS_HERE',
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => Config::get('database.host'),
            'name' => Config::get('database.name'),
            'user' => Config::get('database.user'),
            'pass' => Config::get('database.pass'),
            'port' => Config::get('database.port'),
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
