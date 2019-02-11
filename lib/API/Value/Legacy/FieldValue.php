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
    public $fieldDefinitionId;

    /**
     * @var float
     */
    public $dataFloat = 0;

    /**
     * @var int
     */
    public $dataInt = 0;

    /**
     * @var string
     */
    public $dataText = '';
}
