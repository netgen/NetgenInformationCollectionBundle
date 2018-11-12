<?php

namespace Netgen\Bundle\InformationCollectionBundle\Value;

class LegacyData
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

    /**
     * LegacyData constructor.
     *
     * @param int $contentClassAttributeId
     * @param float $dataFloat
     * @param int $dataInt
     * @param string $dataText
     */
    public function __construct($contentClassAttributeId, $dataFloat, $dataInt, $dataText)
    {
        $this->contentClassAttributeId = $contentClassAttributeId;
        $this->dataFloat = $dataFloat;
        $this->dataInt = $dataInt;
        $this->dataText = $dataText;
    }

    /**
     * @return int
     */
    public function getContentClassAttributeId()
    {
        return $this->contentClassAttributeId;
    }

    /**
     * @return float
     */
    public function getDataFloat()
    {
        return $this->dataFloat;
    }

    /**
     * @return int
     */
    public function getDataInt()
    {
        return $this->dataInt;
    }

    /**
     * @return string
     */
    public function getDataText()
    {
        return $this->dataText;
    }
}
