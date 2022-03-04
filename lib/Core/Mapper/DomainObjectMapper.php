<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Mapper;

use DateTimeImmutable;
use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content as APIContent;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCollection;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Core\Repository\Values\ContentType\ContentType as CoreContentType;
use Netgen\InformationCollection\API\Value\Attribute;
use Netgen\InformationCollection\API\Value\AttributeValue;
use Netgen\InformationCollection\API\Value\Collection;
use Netgen\InformationCollection\API\Value\Content;
use Netgen\InformationCollection\API\Value\NullUser;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute;

final class DomainObjectMapper
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\Repository
     */
    private $repository;

    /**
     * @var \Ibexa\Contracts\Core\Repository\ContentService
     */
    private $contentService;

    /**
     * @var \Ibexa\Contracts\Core\Repository\ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var \Ibexa\Contracts\Core\Repository\UserService
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

    private function getUser($userId): User
    {
        try {
            return $this->repository
                ->getUserService()
                ->loadUser($userId);
        } catch (NotFoundException $exception) {
        }

        return new NullUser();
    }

    private function getDateTime(int $timestamp): DateTimeInterface
    {
        return DateTimeImmutable::createFromFormat('U', (string) $timestamp);
    }

    private function getFieldDefinition(FieldDefinitionCollection $fieldDefinitionCollection, EzInfoCollectionAttribute $attribute): ?FieldDefinition
    {
        /** @var FieldDefinitionCollection $collection */
        $collection = $fieldDefinitionCollection->filter(static function (FieldDefinition $definition) use ($attribute) {
            return $definition->id === $attribute->getContentClassAttributeId();
        });

        if ($collection->isEmpty()) {
            return null;
        }

        return $collection->first();
    }
}
