<?php

namespace AirPressure\Services\Cli;

use AirPressure\Loggable;
use AirPressure\Model\EnvironmentEnum;
use AirPressure\Services\Cli\CliAction;
use AirPressure\Services\Fetcher\AbstractFetcher;
use AirPressure\Services\Repository\RepositoryInterface;

class FetchAction implements CliAction {

    use Loggable;

    private AbstractFetcher $fetcher;
    private RepositoryInterface $repository;

    public function __construct() {
        EnvironmentEnum::getConfigured();
    }

    public function withFetcher(AbstractFetcher $fetcher): self {
        $this->fetcher = $fetcher;
        return $this;
    }

    public function withRepository(RepositoryInterface $repositoryInterface): self {
        $this->repository = $repositoryInterface;
        return $this;
    }


    public function run(): void {
        $this->_log('Starting Air Pressure class');

        //Fetch
        $current = $this->fetcher->fetchCurrent();

        if (!$current) {
            $this->_log('No current air pressure point retrieved, stopping');
            return;
        }

        $isAlreadyPresent = $this->repository->getWithLastModifiedAndPressure($current->getLastModified(), $current->getPressure());

        if ($isAlreadyPresent) {
            $this->_log('Air pressure point is already present, stopping for now.');
            return;
        }

        $this->repository->save($current);

        $this->_log('Saved new airpressure point, done with fetching.');
    }
}