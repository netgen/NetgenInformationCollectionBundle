<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Legacy;

use Netgen\InformationCollection\API\Value\ValueObject;

class FieldValue extends ValueObject
{
    /**
     * @var int
     *
     * previous $contentClassAttributeId
     */
    protected $fieldDefinitionId;

    /**
     * @var float
     */
    protected $dataFloat = 0.0;

    /**
     * @var int
     */
    protected $dataInt = 0;

    /**
     * @var string
     */
    protected $dataText = '';

    public function __construct(int $fieldDefinitionId, string $dataText, int $dataInt = 0, float $dataFloat = 0.0)
    {
        $this->fieldDefinitionId = $fieldDefinitionId;
        $this->dataInt = $dataInt;
        $this->dataFloat = $dataFloat;
        $this->dataText = $dataText;
    }

    /**
     * @return int
     */
    public function getFieldDefinitionId(): int
    {
        return $this->fieldDefinitionId;
    }

    /**
     * @return float
     */
    public function getDataFloat(): float
    {
        return $this->dataFloat;
    }

    /**
     * @return int
     */
    public function getDataInt(): int
    {
        return $this->dataInt;
    }

    /**
     * @return string
     */
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
