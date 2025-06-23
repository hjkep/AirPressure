<?php

namespace AirPressure\Services\Cli;

use AirPressure\Loggable;
use AirPressure\Services\Interpreter;
use AirPressure\Services\Repository\MySQLRepository;

class InterpretAction implements CliAction {

    use Loggable;

    public function run(): void {
        $repository = new MySQLRepository();
        $results = $repository->list(limit: 2, sort: 'id:DESC');

        if (count($results) < 2) {
            $this->_log('Not enough air pressure points to interpret');
            return;
        }

        $interpreted = Interpreter::interpret($results[0], $results[1]);
        $json = json_encode($interpreted);
        if (!is_string($json)) {
            $this->_log('Error trying to encode interpreter');
            return;
        }
        $this->_log($json);
    }
}