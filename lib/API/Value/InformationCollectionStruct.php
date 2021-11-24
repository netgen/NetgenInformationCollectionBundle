<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;

final class InformationCollectionStruct extends ValueObject
{
    /**
     * The language code of the version.
     *
     * @var string
     */
    protected $languageCode;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    protected $content;

    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    protected $contentType;

    /**
     * @var \EzSystems\RepositoryForms\Data\Content\FieldData[]
     */
    protected $fields;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Location
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

    /**
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->content->contentInfo->mainLanguageCode;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function getContent(): Content
    {
        return $this->content;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    /**
     * @return FieldData[]
     */
    public function getFieldsData(): array
    {
        return $this->fields;
    }

    /**
     * @return FieldData[]
     */
    public function getCollectedFields(): array
    {
       return $this->fields;
    }

    protected function addFieldData(FieldData $fieldData)
    {
        $this->fields[$fieldData->fieldDefinition->identifier] = $fieldData;
    }
}
