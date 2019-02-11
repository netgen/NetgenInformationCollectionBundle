<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

class Attribute extends ValueObject
{
    /**
     * @var \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute
     */
    public $entity;

    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition
     */
    public $field;
}
