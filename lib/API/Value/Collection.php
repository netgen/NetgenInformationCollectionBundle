<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

use DateTimeInterface;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\User\User;

final class Collection extends ValueObject
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    protected $content;

    /**
     * @var \eZ\Publish\API\Repository\Values\User\User
     */
    protected $creator;

    /**
     * @var \DateTimeInterface
     */
    protected $created;

    /**
     * @var \DateTimeInterface
     */
    protected $modified;

    /**
     * @var \Netgen\InformationCollection\API\Value\Attribute[]
     */
    protected $attributes;

    public function __construct(int $id, Content $content, User $creator, DateTimeInterface $created, DateTimeInterface $modified, array $attributes)
    {
        $this->id = $id;
        $this->content = $content;
        $this->creator = $creator;
        $this->created = $created;
        $this->modified = $modified;
        $this->attributes = $attributes;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Content
     */
    public function getContent(): Content
    {
        return $this->content;
    }

    /**
     * @return User
     */
    public function getCreator(): User
    {
        return $this->creator;
    }

    /**
     * @return int
     */
    public function getCreated(): DateTimeInterface
    {
        return $this->created;
    }

    /**
     * @return int
     */
    public function getModified(): DateTimeInterface
    {
        return $this->modified;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
