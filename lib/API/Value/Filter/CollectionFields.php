<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Filter;

final class CollectionFields
{
    /**
     * @var int
     */
    private $contentId;

    /**
     * @var int
     */
    private $collectionId;

    /**
     * @var array
     */
    private $fields;

    public function __construct(int $contentId, int $collectionId, array $fields)
    {
        $this->contentId = $contentId;
        $this->collectionId = $collectionId;
        $this->fields = $fields;
    }

    public function getContentId(): int
    {
        return $this->contentId;
    }

    public function getCollectionId(): int
    {
        return $this->collectionId;
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}
