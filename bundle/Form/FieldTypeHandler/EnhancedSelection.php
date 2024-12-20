<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\InformationCollection\Form\Type\FieldType\EnhancedSelection\Value as EnhancedSelectionValue;
use Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

use function is_array;

final class EnhancedSelection extends FieldTypeHandler
{
    /**
     * @param \Netgen\InformationCollection\Form\Type\FieldType\EnhancedSelection\Value $value
     */
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): mixed
    {
        $isMultiple = true;
        if ($fieldDefinition !== null) {
            $fieldSettings = $fieldDefinition->getFieldSettings();
            $isMultiple = $fieldSettings['isMultiple'];
        }

        if (!$isMultiple) {
            if (empty($value->identifiers)) {
                return '';
            }

            return $value->identifiers[0];
        }

        return $value->identifiers;
    }

    public function convertFieldValueFromForm(mixed $data): EnhancedSelectionValue
    {
        if ($data === null) {
            return new EnhancedSelectionValue();
        }

        return new EnhancedSelectionValue(is_array($data) ? $data : [$data]);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $fieldSettings = $fieldDefinition->getFieldSettings();
        $optionsValues = $fieldSettings['options'];

        $options['multiple'] = $fieldSettings['isMultiple'];
        $options['expanded'] = $fieldSettings['isExpanded'];
        $options['choices'] = $this->getValues($optionsValues);

        $formBuilder->add(
            $fieldDefinition->identifier,
            ChoiceType::class,
            $options
        );
    }

    private function getValues(array $options): array
    {
        $values = [];

        foreach ($options as $option) {
            if (!empty($option['identifier']) && !empty($option['name'])) {
                $values[$option['name']] = $option['identifier'];
            }
        }

        return $values;
    }
}

