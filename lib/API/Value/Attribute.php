<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;

final class Attribute extends ValueObject
{
    protected Field $field;

    protected FieldDefinition $fieldDefinition;

    protected int $id;

    protected AttributeValue $value;

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

    public static function createFromAttributeAndValue(self $attribute, AttributeValue $attributeValue): self
    {
        return new self($attribute->getId(), $attribute->getField(), $attribute->getFieldDefinition(), $attributeValue);
    }

    public function getField(): Field
    {
        return $this->field;
    }

    public function getFieldDefinition(): FieldDefinition
    {
        return $this->fieldDefinition;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getValue(): AttributeValue
    {
        return $this->value;
    }
}
