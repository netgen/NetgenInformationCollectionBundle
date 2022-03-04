<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

final class InformationCollectionStruct extends ValueObject
{
    /**
     * The language code of the version.
     *
     * @var string
     */
    protected $languageCode;

    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    protected $content;

    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
     */
    protected $contentType;

    /**
     * @var \Ibexa\Contracts\ContentForms\Data\Content\FieldData[]
     */
    protected $fields;

    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Location
     */
    private $location;

    public function __construct(Content $content, Location $location, ContentType $contentType, array $fields)
    {
        $this->content = $content;
        $this->contentType = $contentType;

        foreach ($fields as $field) {
            $this->addFieldData($field);
        }
        $this->location = $location;
    }

    public function getLanguageCode(): string
    {
        return $this->content->contentInfo->mainLanguageCode;
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
        return $this->fields;
    }

    /**
     * @return \Ibexa\Contracts\ContentForms\Data\Content\FieldData[]
     */
    public function getCollectedFields(): array
    {
        return $this->fields;
    }

    protected function addFieldData(FieldData $fieldData): void
    {
        $this->fields[$fieldData->fieldDefinition->identifier] = $fieldData;
    }
}
