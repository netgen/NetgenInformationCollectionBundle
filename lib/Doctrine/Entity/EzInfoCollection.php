<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Doctrine\Entity;

class EzInfoCollection
{
    private int $id;

    private int $contentObjectId;

    private int $created;

    private int $creatorId;

    private int $modified;

    private string $userIdentifier;

    public function getId(): int
    {
        return $this->id;
    }

    public function getContentObjectId(): int
    {
        return $this->contentObjectId;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setContentObjectId(int $contentObjectId): void
    {
        $this->contentObjectId = $contentObjectId;
    }

    public function setCreated(int $created): void
    {
        $this->created = $created;
    }

    public function setCreatorId(int $creatorId): void
    {
        $this->creatorId = $creatorId;
    }

    public function setModified(int $modified): void
    {
        $this->modified = $modified;
    }

    public function setUserIdentifier(string $userIdentifier): void
    {
        $this->userIdentifier = $userIdentifier;
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function getCreatorId(): int
    {
        return $this->creatorId;
    }

    public function getModified(): int
    {
        return $this->modified;
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }
}
