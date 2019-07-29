<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use EzSystems\RepositoryForms\Data\Content\FieldData;

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

    public function __construct(Content $content, ContentType $contentType, array $fields, string $languageCode)
    {
        $this->content = $content;
        $this->contentType = $contentType;
        $this->languageCode = $languageCode;

        foreach ($fields as $field) {
            $this->addFieldData($field);
        }
    }

    /**
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function getContent(): Content
    {
        return $this->content;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    /**
     * @return \EzSystems\RepositoryForms\Data\Content\FieldData[]
     */
    public function getFieldsData(): array
    {
        return $this->fields;
    }

    /**
     * @return \EzSystems\RepositoryForms\Data\Content\FieldData[]
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
