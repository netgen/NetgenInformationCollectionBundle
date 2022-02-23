<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use OutOfBoundsException;

final class ContentTypeUtils
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var \Ibexa\Contracts\Core\Repository\ContentService
     */
    private $contentService;

    /**
     * FieldIdResolver constructor.
     *
     * @param \Ibexa\Contracts\Core\Repository\ContentTypeService $contentTypeService
     * @param \Ibexa\Contracts\Core\Repository\ContentService $contentService
     */
    public function __construct(ContentTypeService $contentTypeService, ContentService $contentService)
    {
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
    }

    /**
     * Return field id for fiven field definition identifier.
     *
     * @param int $contentId
     * @param string $fieldDefIdentifier
     *
     * @throws \OutOfBoundsException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     *
     * @return mixed
     */
    public function getFieldId($contentId, $fieldDefIdentifier)
    {
        $content = $this->contentService->loadContent($contentId);

        $contentType = $this->contentTypeService
            ->loadContentType($content->contentInfo->contentTypeId);

        $field = $contentType->getFieldDefinition($fieldDefIdentifier);

        if (!$field instanceof FieldDefinition) {
            throw new OutOfBoundsException(sprintf('ContentType does not contain field with identifier %s.', $fieldDefIdentifier));
        }

        return $field->id;
    }

    /**
     * Returns fields that are marked as info collectors.
     *
     * @param int $contentId
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     *
     * @return array
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
