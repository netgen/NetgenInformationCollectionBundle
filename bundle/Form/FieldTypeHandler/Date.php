<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;

use DateTime;
use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Date\Value as DateValue;
use Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class Date extends FieldTypeHandler
{
    /**
     * @param \Ibexa\Core\FieldType\Date\Value $value
     */
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): DateTime
    {
        return $value->date;
    }

    public function convertFieldValueFromForm($data): DateValue
    {
        return new DateValue($data);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $options['input'] = 'datetime';
        $options['widget'] = 'choice';
        $options['constraints'][] = new Assert\Date();

        $formBuilder->add($fieldDefinition->identifier, DateType::class, $options);
    }
}
