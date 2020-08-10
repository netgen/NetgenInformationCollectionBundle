<?php

namespace Netgen\InformationCollection\Core\Persistence\Anonymizer\Visitor\Field;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Netgen\InformationCollection\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor;
use Netgen\InformationCollection\API\Value\Attribute;
use Netgen\InformationCollection\API\Value\AttributeValue;

class Email extends FieldAnonymizerVisitor
{
    protected $allowedCharacters = ['.', '@', '-', '+'];

    /**
     * {@inheritdoc}
     */
    public function accept(Attribute $attribute, ContentType $contentType): bool
    {
        return 'ezemail' === $attribute->getFieldDefinition()->fieldTypeIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function visit(Attribute $attribute, ContentType $contentType): AttributeValue
    {
        $email = $attribute->getValue()->getDataText();

        $split = str_split($email);

        $email = [];
        foreach ($split as $character) {
            if (!in_array($character, $this->allowedCharacters)) {
                $email[] = 'X';
            } else {
                $email[] = $character;
            }
        }

        return new AttributeValue(0, 0, implode($email, ''));
    }
}
