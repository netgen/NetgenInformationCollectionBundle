<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

class Collection extends ValueObject
{
    /**
     * @var \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection
     */
    public $entity;

    /**
     * @var \Netgen\InformationCollection\API\Value\Attribute[]
     */
    public $attributes;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    public $content;

    /**
     * @var \eZ\Publish\API\Repository\Values\User\User
     */
    public $user;
}
