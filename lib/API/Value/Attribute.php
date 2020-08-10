<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute;

final class Attribute extends ValueObject
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Field
     */
    protected $field;

    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition
     */
    protected $fieldDefinition;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var \Netgen\InformationCollection\API\Value\AttributeValue
     */
    protected $value;

    public function __construct(
        int $id,
        Field $field,
        FieldDefinition $fieldDefinition,
        AttributeValue $value
    ) {
        $this->id = $id;
        $this->field = $field;
        $this->fieldDefinition = $fieldDefinition;
        $this->value = $value;
    }

    public static function createFromAttributeAndValue(Attribute $attribute, AttributeValue $attributeValue)
    {
        return new self($attribute->getId(), $attribute->getField(), $attribute->getFieldDefinition(), $attributeValue);
    }

    /**
     * @return Field
     */
    public function getField(): Field
    {
        return $this->field;
    }

    /**
     * @return FieldDefinition
     */
    public function getFieldDefinition(): FieldDefinition
    {
        return $this->fieldDefinition;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return AttributeValue
     */
    public function getValue(): AttributeValue
    {
        return $this->value;
    }
}
