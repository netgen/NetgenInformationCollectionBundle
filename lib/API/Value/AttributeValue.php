<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

class AttributeValue extends ValueObject
{
    /**
     * @var int
     */
    protected $dataInt;

    /**
     * @var float
     */
    protected $dataFloat;

    /**
     * @var string
     */
    protected $dataText;

    public function __construct(int $dataInt, float $dataFloat, string $dataText)
    {
        $this->dataInt = $dataInt;
        $this->dataFloat = $dataFloat;
        $this->dataText = $dataText;
    }

    public function __toString()
    {
        if (!empty($this->dataText)) {
            return $this->dataText;
        }

        if (!empty($this->dataInt)) {
            return (string)$this->dataInt;
        }

        if (!empty($this->dataFloat)) {
            return (string)$this->dataFloat;
        }

        return '';
    }

    /**
     * @return int
     */
    public function getDataInt(): int
    {
        return $this->dataInt;
    }

    /**
     * @return float
     */
    public function getDataFloat(): float
    {
        return $this->dataFloat;
    }

    /**
     * @return string
     */
    public function getDataText(): string
    {
        return $this->dataText;
    }
}
