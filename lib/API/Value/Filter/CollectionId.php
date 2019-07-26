<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Filter;

final class CollectionId
{
    /**
     * @var int
     */
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getCollectionId(): int
    {
        return $this->id;
    }
}
