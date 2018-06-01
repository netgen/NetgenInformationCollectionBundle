<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Persistence\Anonymizer;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Content;

class FieldIdResolver
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
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function getId(Content $content, $fieldDefIdentifier)
    {
        $contentType = $this->contentTypeService
            ->loadContentType($content->contentInfo->contentTypeId);

        $field = $contentType->getFieldDefinition($fieldDefIdentifier);

        return $field->id;
    }
}
