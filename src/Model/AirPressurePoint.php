<?php

namespace AirPressure\Model;

use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;

class AirPressurePoint implements JsonSerializable {
    public function __construct(
        private float $pressure,
        private DateTimeImmutable $created,
        private DateTimeImmutable $last_modified,
        private ?int $id = null,
    ) {
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getPressure(): float {
        return $this->pressure;
    }

    public function getCreated(): DateTimeImmutable {
        return $this->created;
    }

    public function getLastModified(): DateTimeImmutable {
        return $this->last_modified;
    }

    public function setPressure(float $pressure): self {
        $this->pressure = $pressure;
        return $this;
    }

    public function setLastModified(DateTimeImmutable $last_modified): self {
        $this->last_modified = $last_modified;
        return $this;
    }

    public function jsonSerialize(): mixed {
        return [
            'id' => $this->id,
            'pressure' => $this->pressure,
            'last_modified' => $this->last_modified->format(DateTimeInterface::ATOM),
            'created' => $this->created->format(DateTimeInterface::ATOM)
        ];
    }
}