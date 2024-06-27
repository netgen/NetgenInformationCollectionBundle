<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

final class InformationCollectionStruct extends ValueObject
{
    private Content $content;

    private ContentType $contentType;

    /**
     * @var \Ibexa\Contracts\ContentForms\Data\Content\FieldData[]
     */
    private array $fieldsData;

    private Location $location;

    /**
     * @param \Ibexa\Contracts\ContentForms\Data\Content\FieldData[] $fieldsData
     */
    public function __construct(
        Content $content,
        Location $location,
        ContentType $contentType,
        array $fieldsData
    ) {
        $this->content = $content;
        $this->location = $location;
        $this->contentType = $contentType;

        foreach ($fieldsData as $fieldData) {
            $this->addFieldData($fieldData);
        }
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    /**
     * @return \Ibexa\Contracts\ContentForms\Data\Content\FieldData[]
     */
    public function getFieldsData(): array
    {
        return $this->fieldsData;
    }

    /**
     * @return \Ibexa\Contracts\ContentForms\Data\Content\FieldData[]
     */
    public function getCollectedFields(): array
    {
        return $this->fieldsData;
    }

    private function addFieldData(FieldData $fieldData): void
    {
        $this->fieldsData[$fieldData->fieldDefinition->identifier] = $fieldData;
    }
}
