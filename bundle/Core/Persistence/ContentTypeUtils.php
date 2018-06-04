<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Persistence;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Content;

class ContentTypeUtils
{
    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    protected $contentTypeService;

    /**
     * FieldIdResolver constructor.
     *
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * Return field id for fiven field definition identifier
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param string $fieldDefIdentifier
     *
     * @return mixed
     */
    public function getId(Content $content, $fieldDefIdentifier)
    {
        $contentType = $this->contentTypeService
            ->loadContentType($content->contentInfo->contentTypeId);

        $field = $contentType->getFieldDefinition($fieldDefIdentifier);

        return $field->id;
    }


    /**
     * Returns fields that are marked as info collectors
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     *
     * @return array
     */
    public function getInfoCollectorFields(Content $content)
    {
        $fields = [];

        $contentType = $this->contentTypeService
            ->loadContentType($content->contentInfo->contentTypeId);

        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {

            if ($fieldDefinition->isInfoCollector) {
                $fields[$fieldDefinition->id] = $fieldDefinition->getName();
            }
        }

        return $fields;
    }
}
