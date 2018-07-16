<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection;

use Netgen\Bundle\InformationCollectionBundle\API\Value\ValueObject;

class Collection extends ValueObject
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection
     */
    public $entity;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Attribute[]
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
