<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection;

use Netgen\Bundle\InformationCollectionBundle\API\Value\ValueObject;

class Attribute extends ValueObject
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute
     */
    public $entity;

    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition
     */
    public $field;
}
