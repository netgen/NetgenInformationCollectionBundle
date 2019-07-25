<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute;

final class Attribute extends ValueObject
{
    /**
     * @var \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute
     */
    protected $attribute;

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
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    protected $content;

    /**
     * @var \Netgen\InformationCollection\API\Value\AttributeValue
     */
    protected $value;

    public function __construct(
        int $id,
        Content $content,
        Field $field,
        FieldDefinition $fieldDefinition,
        AttributeValue $value
    )
    {
        $this->id = $id;
        $this->content = $content;
        $this->field = $field;
        $this->fieldDefinition = $fieldDefinition;
        $this->value = $value;
    }

    /**
     * @return EzInfoCollectionAttribute
     */
    public function getAttribute(): EzInfoCollectionAttribute
    {
        return $this->attribute;
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
     * @return Content
     */
    public function getContent(): Content
    {
        return $this->content;
    }

    /**
     * @return AttributeValue
     */
    public function getValue(): AttributeValue
    {
        return $this->value;
    }
}
