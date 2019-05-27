<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Doctrine\Entity;

class EzInfoCollectionAttribute
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $contentClassAttributeId;

    /**
     * @var int
     */
    private $contentObjectAttributeId;

    /**
     * @var int
     */
    private $contentObjectId;

    /**
     * @var float
     */
    private $dataFloat;

    /**
     * @var int
     */
    private $dataInt;

    /**
     * @var string
     */
    private $dataText;

    /**
     * @var int
     */
    private $informationCollectionId;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param int $contentClassAttributeId
     */
    public function setContentClassAttributeId($contentClassAttributeId)
    {
        $this->contentClassAttributeId = $contentClassAttributeId;
    }

    /**
     * @param int $contentObjectAttributeId
     */
    public function setContentObjectAttributeId($contentObjectAttributeId)
    {
        $this->contentObjectAttributeId = $contentObjectAttributeId;
    }

    /**
     * @param int $contentObjectId
     */
    public function setContentObjectId($contentObjectId)
    {
        $this->contentObjectId = $contentObjectId;
    }

    /**
     * @param float $dataFloat
     */
    public function setDataFloat($dataFloat)
    {
        $this->dataFloat = $dataFloat;
    }

    /**
     * @param int $dataInt
     */
    public function setDataInt($dataInt)
    {
        $this->dataInt = $dataInt;
    }

    /**
     * @param string $dataText
     */
    public function setDataText($dataText)
    {
        $this->dataText = $dataText;
    }

    /**
     * @param int $informationCollectionId
     */
    public function setInformationCollectionId($informationCollectionId)
    {
        $this->informationCollectionId = $informationCollectionId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getContentClassAttributeId()
    {
        return $this->contentClassAttributeId;
    }

    /**
     * @return int
     */
    public function getContentObjectAttributeId()
    {
        return $this->contentObjectAttributeId;
    }

    /**
     * @return int
     */
    public function getContentObjectId()
    {
        return $this->contentObjectId;
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

    /**
     * @return int
     */
    public function getInformationCollectionId()
    {
        return $this->informationCollectionId;
    }

    public function getValue()
    {
        if (!empty($this->dataText)) {
            return $this->dataText;
        }

        if (!empty($this->dataInt)) {
            return $this->dataInt;
        }

        if (!empty($this->dataFloat)) {
            return $this->dataFloat;
        }

        return '';
    }
}
