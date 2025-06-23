<?php

namespace AirPressure\Services\Cli;

use AirPressure\Loggable;
use AirPressure\Services\Repository\MySQLRepository;

class CurrentAction implements CliAction {

    use Loggable;

    public function run(): void {
        $repository = new MySQLRepository();

        $latest = $repository->list(
            limit: 1,
            sort: 'id:DESC'
        );

        if (empty($latest)) {
            $this->_log('No current airpressure present, stopping');
            return;
        }

        $this->_log(sprintf('Latest airpressure is %d, fetched at %s', $latest[0]->getPressure(), $latest[0]->getLastModified()->format('Y-m-d H:i:s')));
    }
} 