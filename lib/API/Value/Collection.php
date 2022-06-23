<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\User\User;

final class Collection extends ValueObject
{
    protected int $id;

    protected Content $content;

    protected User $creator;

    protected DateTimeInterface $created;

    protected DateTimeInterface $modified;

    /**
     * @var \Netgen\InformationCollection\API\Value\Attribute[]
     */
    protected array $attributes;

    public function __construct(int $id, Content $content, User $creator, DateTimeInterface $created, DateTimeInterface $modified, array $attributes)
    {
        $this->id = $id;
        $this->content = $content;
        $this->creator = $creator;
        $this->created = $created;
        $this->modified = $modified;
        $this->attributes = $attributes;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getCreator(): User
    {
        return $this->creator;
    }

    public function getCreated(): DateTimeInterface
    {
        return $this->created;
    }

    public function getModified(): DateTimeInterface
    {
        return $this->modified;
    }

    /**
     * @return \Netgen\InformationCollection\API\Value\Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
