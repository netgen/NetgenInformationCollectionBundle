<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Legacy;

use Netgen\InformationCollection\API\Value\ValueObject;

class FieldValue extends ValueObject
{
    protected int $fieldDefinitionId;

    protected float $dataFloat;

    protected int $dataInt;

    protected string $dataText;

    public function __construct(int $fieldDefinitionId, string $dataText, int $dataInt = 0, float $dataFloat = 0.0)
    {
        $this->fieldDefinitionId = $fieldDefinitionId;
        $this->dataInt = $dataInt;
        $this->dataFloat = $dataFloat;
        $this->dataText = $dataText;
    }

    public function getFieldDefinitionId(): int
    {
        return $this->fieldDefinitionId;
    }

    public function getDataFloat(): float
    {
        return $this->dataFloat;
    }

    public function getDataInt(): int
    {
        return $this->dataInt;
    }

    public function getDataText(): string
    {
        return $this->dataText;
    }

    public static function withStringValue(int $fieldDefinitionId, string $dataText): self
    {
        return new self(
            $fieldDefinitionId,
            $dataText
        );
    }

    public static function withIntValue(int $fieldDefinitionId, int $dataInt): self
    {
        return new self(
            $fieldDefinitionId,
            '',
            $dataInt
        );
    }

    public static function withFloatValue(int $fieldDefinitionId, float $dataFloat): self
    {
        return new self(
            $fieldDefinitionId,
            '',
            0,
            $dataFloat
        );
    }
}
