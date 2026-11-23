<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\ISBN\Value as IsbnValue;
use Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

final class Isbn extends FieldTypeHandler
{
    /**
     * @param \Ibexa\Core\FieldType\ISBN\Value $value
     */
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): string
    {
        return $value->isbn;
    }

    public function convertFieldValueFromForm(mixed $data): IsbnValue
    {
        if (empty($data)) {
            $data = '';
        }

        return new IsbnValue($data);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        if ($fieldDefinition->fieldSettings['isISBN13'] ?? true) {
            $options['constraints'][] = new Constraints\Isbn(
                [
                    'type' => 'isbn13',
                ]
            );
        } else {
            $options['constraints'][] = new Constraints\Isbn();
        }

        $formBuilder->add($fieldDefinition->identifier, TextType::class, $options);
    }
}
