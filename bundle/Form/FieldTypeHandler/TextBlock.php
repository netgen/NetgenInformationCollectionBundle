<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\TextBlock\Value as TextBlockValue;
use Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

final class TextBlock extends FieldTypeHandler
{
    /**
     * @param \Ibexa\Core\FieldType\TextBlock\Value $value
     */
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): string
    {
        return $value->text;
    }

    public function convertFieldValueFromForm($data): TextBlockValue
    {
        if (empty($data)) {
            $data = '';
        }

        return new TextBlockValue($data);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $options['attr']['rows'] = $fieldDefinition->fieldSettings['textRows'];

        $formBuilder->add($fieldDefinition->identifier, TextareaType::class, $options);
    }
}
