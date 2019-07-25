<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Mapper;

use DateTimeImmutable;
use DateTimeInterface;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\Content as APIContent;
use Netgen\InformationCollection\API\Value\Attribute;
use Netgen\InformationCollection\API\Value\AttributeValue;
use Netgen\InformationCollection\API\Value\Collection;
use Netgen\InformationCollection\API\Value\Content;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute;

final class DomainObjectMapper
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    protected $contentService;

    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    protected $contentTypeService;

    /**
     * @var \eZ\Publish\API\Repository\UserService
     */
    protected $userService;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
        $this->contentService = $repository->getContentService();
        $this->contentTypeService = $repository->getContentTypeService();
    }

    public function mapContent(array $data, EzInfoCollection $first, EzInfoCollection $last, int $childCount): Content
    {
        $content = $this->contentService->loadContent((int) $data['content_id']);
        $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);
        $hasLocation = empty($object['main_node_id']) ? false : true;

        return new Content(
            $content,
            $contentType,
            $this->mapCollection($first, []),
            $this->mapCollection($last, []),
            $childCount,
            $hasLocation
        );
    }

    public function mapCollection(EzInfoCollection $collection, array $attributes): Collection
    {
        $content = $this->contentService->loadContent($collection->getContentObjectId());
        $contentType = $this->contentTypeService
            ->loadContentType(
                $content->contentInfo->contentTypeId
            );

        $attributeValues = [];
        foreach ($attributes as $attr) {
            if (empty($contentType->fieldDefinitionsById[$attr->getContentClassAttributeId()])) {
                continue;
            }

            $attributeValues[] = $this->mapAttribute($attr, $content, $contentType->fieldDefinitionsById[$attr->getContentClassAttributeId()]);
        }

        $user = $this->getUser($collection->getCreatorId());

        return new Collection(
            $collection->getId(),
            $content,
            $user,
            $this->getDateTime($collection->getCreated()),
            $this->getDateTime($collection->getModified()),
            $attributeValues
        );
    }

    public function mapAttribute(EzInfoCollectionAttribute $attribute, APIContent $content, FieldDefinition $fieldDefinition): Attribute
    {
        $classField = null;
        foreach ($content->getFields() as $field) {
            if ($field->id == $attribute->getContentObjectAttributeId()) {
                $classField = $field;
                break;
            }
        }

        $value = new AttributeValue($attribute->getDataInt(), $attribute->getDataFloat(), $attribute->getDataText());
        dump($classField);
        return new Attribute(
            $attribute->getId(), $content, $classField, $fieldDefinition, $value
        );
    }

    protected function getUser($userId)
    {
        try {
            return $this->repository
                ->getUserService()
                ->loadUser($userId);

        } catch (NotFoundException $exception) {

        }
    }

    protected function getDateTime(int $timestamp): DateTimeInterface
    {
        $date = new DateTimeImmutable();
        $date->setTimestamp($timestamp);

        return $date;
    }
}
