<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Selection\Value as SelectionValue;
use Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use function array_flip;

final class Selection extends FieldTypeHandler
{
    public function convertFieldValueFromForm(mixed $value): SelectionValue
    {
        return new SelectionValue((array) $value);
    }

    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): mixed
    {
        $isMultiple = true;
        if ($fieldDefinition !== null) {
            $fieldSettings = $fieldDefinition->getFieldSettings();
            $isMultiple = $fieldSettings['isMultiple'];
        }

        if (!$isMultiple) {
            if (empty($value->selection)) {
                return '';
            }

            return $value->selection[0];
        }

        /** @var $value \Ibexa\Core\FieldType\Selection\Value */
        return $value->selection;
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $values = $fieldDefinition->getFieldSettings()['options'];

        $options['expanded'] = false;
        $options['multiple'] = $fieldDefinition->getFieldSettings()['isMultiple'];

        $options['choices'] = array_flip($values);

        $formBuilder->add($fieldDefinition->identifier, ChoiceType::class, $options);
    }
}
