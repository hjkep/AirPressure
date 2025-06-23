<?php

namespace AirPressure\Model;

class InterpreterResult {
    public function __construct(
        public readonly bool $currentPressureChanceForHeadache,
        public readonly bool $historicPressureChanceForHeadache
    ) {

    }

    /**
     * Summary of toArray
     * @return array{currentPressureChanceForHeadache: bool, historicPressureChanceForHeadache: bool}
     */
    public function toArray(): array {
        return [
            "currentPressureChanceForHeadache" => $this->currentPressureChanceForHeadache,
            "historicPressureChanceForHeadache" => $this->historicPressureChanceForHeadache
        ];
    }
}