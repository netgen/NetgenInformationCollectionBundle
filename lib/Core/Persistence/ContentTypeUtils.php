<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Persistence;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use OutOfBoundsException;

final class ContentTypeUtils
{
    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    protected $contentTypeService;

    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    protected $contentService;

    /**
     * FieldIdResolver constructor.
     *
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
    public function __construct(ContentTypeService $contentTypeService, ContentService $contentService)
    {
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
    }

    /**
     * Return field id for fiven field definition identifier
     *
     * @param int $contentId
     * @param string $fieldDefIdentifier
     *
     * @return mixed
     *
     * @throws \OutOfBoundsException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function getFieldId($contentId, $fieldDefIdentifier)
    {
        $content = $this->contentService->loadContent($contentId);

        $contentType = $this->contentTypeService
            ->loadContentType($content->contentInfo->contentTypeId);

        $field = $contentType->getFieldDefinition($fieldDefIdentifier);

        if (!$field instanceof FieldDefinition) {
            throw new OutOfBoundsException(sprintf("ContentType does not contain field with identifier %s.", $fieldDefIdentifier));
        }

        return $field->id;
    }


    /**
     * Returns fields that are marked as info collectors
     *
     * @param int $contentId
     *
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function getInfoCollectorFields($contentId)
    {
        $fields = [];

        $content = $this->contentService->loadContent($contentId);

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
