<?php

namespace AirPressure\Services\Cli;

use AirPressure\Loggable;
use AirPressure\Services\Repository\MySQLRepository;

class ListAction implements CliAction {

    use Loggable;

    public function run(): void {
        $repository = new MySQLRepository();
        $json = json_encode(value: $repository->list(), flags: JSON_PRETTY_PRINT);
        if (!is_string($json)) {
            $this->_log('Error trying to encode interpreter');
            return;
        }
        $this->_log($json);
    }
}