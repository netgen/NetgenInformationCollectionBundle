<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\Anonymizer\Visitor\Field;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Netgen\InformationCollection\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor;
use Netgen\InformationCollection\API\Value\Attribute;
use Netgen\InformationCollection\API\Value\AttributeValue;
use function implode;
use function in_array;
use function mb_str_split;

class Email extends FieldAnonymizerVisitor
{
    protected $allowedCharacters = ['.', '@', '-', '+'];

    public function accept(Attribute $attribute, ContentType $contentType): bool
    {
        return 'ezemail' === $attribute->getFieldDefinition()->fieldTypeIdentifier;
    }

    public function visit(Attribute $attribute, ContentType $contentType): AttributeValue
    {
        $email = $attribute->getValue()->getDataText();

        $split = mb_str_split($email);

        $email = [];
        foreach ($split as $character) {
            if (!in_array($character, $this->allowedCharacters, true)) {
                $email[] = 'X';
            } else {
                $email[] = $character;
            }
        }

        return new AttributeValue(0, 0, implode('', $email));
    }
}
