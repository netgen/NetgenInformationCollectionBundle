<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\TextLine\Value as TextLineValue;
use Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

final class TextLine extends FieldTypeHandler
{
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): string
    {
        /** @var $value TextLineValue */
        return $value->text;
    }

    public function convertFieldValueFromForm($data): TextLineValue
    {
        if (empty($data)) {
            $data = '';
        }

        return new TextLineValue($data);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        if (!empty($fieldDefinition->validatorConfiguration['StringLengthValidator'])) {
            $lengthConstraints = [];

            $minStringLength = $fieldDefinition->validatorConfiguration['StringLengthValidator']['minStringLength'];
            $maxStringLength = $fieldDefinition->validatorConfiguration['StringLengthValidator']['maxStringLength'];

            if (!empty($minStringLength)) {
                $lengthConstraints['min'] = $minStringLength;
            }

            if (!empty($maxStringLength)) {
                $lengthConstraints['max'] = $maxStringLength;
            }

            if (!empty($lengthConstraints)) {
                $options['constraints'][] = new Constraints\Length($lengthConstraints);
            }
        }

        $formBuilder->add($fieldDefinition->identifier, TextType::class, $options);
    }
}
