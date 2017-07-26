<?php

namespace Netgen\Bundle\InformationCollectionBundle\Value;

/**
 * @property int $contentClassAttributeId
 * @property float $dataFloat
 * @property int $dataInt
 * @property string $dataText
 */
class LegacyData extends ValueObject
{
    /**
     * @var int
     */
    protected $contentClassAttributeId;

    /**
     * @var float
     */
    protected $dataFloat;

    /**
     * @var int
     */
    protected $dataInt;

    /**
     * @var string
     */
    protected $dataText;
}
