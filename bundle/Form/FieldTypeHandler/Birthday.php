<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;

use DateTimeInterface;
use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;
use Netgen\InformationCollection\Form\Type\FieldType\Birthday\Value as BirthdayValue;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Birthday extends FieldTypeHandler
{
    /**
     * @param \Netgen\InformationCollection\Form\Type\FieldType\Birthday\Value $value
     */
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): ?DateTimeInterface
    {
        return $value->date;
    }

    public function convertFieldValueFromForm($data): BirthdayValue
    {
        if (empty($data)) {
            $data = null;
        }

        return new BirthdayValue($data);
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

        $formBuilder->add(
            $fieldDefinition->identifier,
            BirthdayType::class,
            $options
        );
    }
}
