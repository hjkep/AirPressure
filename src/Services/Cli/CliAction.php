<?php

namespace AirPressure\Services\Cli;

interface CliAction {
    public function run(): void;
}