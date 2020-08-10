<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Mapper;

use DateTimeImmutable;
use DateTimeInterface;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content as APIContent;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCollection;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType as CoreContentType;
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
    private $repository;

    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var \eZ\Publish\API\Repository\UserService
     */
    private $userService;

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
        /** @var CoreContentType $contentType */
        $contentType = $this->contentTypeService
            ->loadContentType(
                $content->contentInfo->contentTypeId
            );

        $fieldDefinitions = $contentType->getFieldDefinitions();
        $attributeValues = [];

        foreach ($attributes as $attr) {

            $fieldDefinition = $this->getFieldDefinition($fieldDefinitions, $attr);

            if (!$fieldDefinition instanceof FieldDefinition) {
                continue;
            }

            $attributeValues[] = $this->mapAttribute($attr, $content, $fieldDefinition);
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
        $classField = new Field();
        foreach ($content->getFields() as $field) {
            if ($field->id === $attribute->getContentObjectAttributeId()) {
                $classField = $field;

                break;
            }
        }

        $value = new AttributeValue($attribute->getDataInt(), $attribute->getDataFloat(), $attribute->getDataText());
        return new Attribute(
            $attribute->getId(),
            $classField,
            $fieldDefinition,
            $value
        );
    }

    private function getUser($userId): ?User
    {
        try {
            return $this->repository
                ->getUserService()
                ->loadUser($userId);
        } catch (NotFoundException $exception) {
        }
    }

    private function getDateTime(int $timestamp): DateTimeInterface
    {
        return DateTimeImmutable::createFromFormat('U', (string) $timestamp);
    }

    private function getFieldDefinition(FieldDefinitionCollection $fieldDefinitionCollection, EzInfoCollectionAttribute $attribute): ?FieldDefinition
    {
        /** @var FieldDefinitionCollection $collection */
        $collection = $fieldDefinitionCollection->filter(function(FieldDefinition $definition) use ($attribute) {
            return $definition->id === $attribute->getContentClassAttributeId();
        });

        if ($collection->isEmpty()) {
            return null;
        }

        return $collection->first();
    }
}
