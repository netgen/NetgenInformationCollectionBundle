<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;

use DateTime;
use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Time\Value as TimeValue;
use Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use function is_int;

final class Time extends FieldTypeHandler
{
    /**
     * @param \Ibexa\Core\FieldType\Time\Value $value
     */
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): DateTime
    {
        /** @var $value TimeValue */
        $time = $value->time;
        if (is_int($time)) {
            return new DateTime("@{$time}");
        }

        return new DateTime();
    }

    public function convertFieldValueFromForm($data): TimeValue
    {
        if ($data instanceof DateTime) {
            return TimeValue::fromDateTime($data);
        }

        if (is_int($data)) {
            return new TimeValue($data);
        }

        return new TimeValue(null);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $useSeconds = $fieldDefinition->getFieldSettings()['useSeconds'];
        $options['input'] = 'datetime';
        $options['widget'] = 'choice';
        $options['with_seconds'] = $useSeconds;
        $options['constraints'][] = new Assert\Time();

        $formBuilder->add($fieldDefinition->identifier, TimeType::class, $options);
    }
}
