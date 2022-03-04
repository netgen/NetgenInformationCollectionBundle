<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Doctrine\Entity;

class EzInfoCollectionAttribute
{
    private int $id;

    private int $contentClassAttributeId;

    private int $contentObjectAttributeId;

    private int $contentObjectId;

    private float $dataFloat;

    private int $dataInt;

    private string $dataText;

    private int $informationCollectionId;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setContentClassAttributeId(int $contentClassAttributeId): void
    {
        $this->contentClassAttributeId = $contentClassAttributeId;
    }

    public function setContentObjectAttributeId(int $contentObjectAttributeId): void
    {
        $this->contentObjectAttributeId = $contentObjectAttributeId;
    }

    public function setContentObjectId(int $contentObjectId): void
    {
        $this->contentObjectId = $contentObjectId;
    }

    public function setDataFloat(float $dataFloat): void
    {
        $this->dataFloat = $dataFloat;
    }

    public function setDataInt(int $dataInt): void
    {
        $this->dataInt = $dataInt;
    }

    public function setDataText(string $dataText): void
    {
        $this->dataText = $dataText;
    }

    public function setInformationCollectionId(int $informationCollectionId): void
    {
        $this->informationCollectionId = $informationCollectionId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getContentClassAttributeId(): int
    {
        return $this->contentClassAttributeId;
    }

    public function getContentObjectAttributeId(): int
    {
        return $this->contentObjectAttributeId;
    }

    public function getContentObjectId(): int
    {
        return $this->contentObjectId;
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

    public function getInformationCollectionId(): int
    {
        return $this->informationCollectionId;
    }
}
