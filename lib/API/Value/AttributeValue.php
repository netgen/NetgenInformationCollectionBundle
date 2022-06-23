<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

class AttributeValue extends ValueObject
{
    protected int $dataInt;

    protected float $dataFloat;

    protected string $dataText;

    public function __construct(int $dataInt, float $dataFloat, string $dataText)
    {
        $this->dataInt = $dataInt;
        $this->dataFloat = $dataFloat;
        $this->dataText = $dataText;
    }

    public function __toString(): string
    {
        if (!empty($this->dataText)) {
            return $this->dataText;
        }

        if (!empty($this->dataInt)) {
            return (string) $this->dataInt;
        }

        if (!empty($this->dataFloat)) {
            return (string) $this->dataFloat;
        }

        return '';
    }

    public function getDataInt(): int
    {
        return $this->dataInt;
    }

    public function getDataFloat(): float
    {
        return $this->dataFloat;
    }

    public function getDataText(): string
    {
        return $this->dataText;
    }
}
