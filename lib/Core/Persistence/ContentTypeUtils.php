<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use OutOfBoundsException;
use function sprintf;

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

    public function __construct(ContentTypeService $contentTypeService, ContentService $contentService)
    {
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
    }

    /**
     * Return field id for given field definition identifier.
     *
     * @throws \OutOfBoundsException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getFieldId(int $contentId, string $fieldDefIdentifier): int
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
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getInfoCollectorFields(int $contentId): array
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
