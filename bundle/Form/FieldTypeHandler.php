<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use RuntimeException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

abstract class FieldTypeHandler implements FieldTypeHandlerInterface
{
    abstract public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null);

    public function convertFieldValueFromForm($data)
    {
        return $data;
    }

    /**
     * In most cases this will be the same as {@link self::buildUpdateFieldForm()}.
     * For this reason default implementation falls back to the internal method
     * {@link self::buildFieldForm()}, which should be implemented as needed.
     */
    public function buildFieldCreateForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode
    ): void {
        $this->buildFieldForm($formBuilder, $fieldDefinition, $languageCode);
    }

    /**
     * In most cases this will be the same as {@link self::buildCreateFieldForm()}.
     * For this reason default implementation falls back to the internal method
     * {@link self::buildFieldForm()}, which should be implemented as needed.
     */
    public function buildFieldUpdateForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        Content $content,
        string $languageCode
    ): void {
        $this->buildFieldForm($formBuilder, $fieldDefinition, $languageCode, $content);
    }

    /**
     * In most cases implementations of methods {@link self::buildCreateFieldForm()}
     * and {@link self::buildUpdateFieldForm()} will be the same, therefore default
     * handler implementation of those falls back to this method.
     *
     * Implement as needed.
     */
    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        throw new RuntimeException('Not implemented.');
    }

    /**
     * Returns default field options, created from given $fieldDefinition and $languageCode.
     */
    protected function getDefaultFieldOptions(
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): array {
        $options = [];

        $options['label'] = $fieldDefinition->getName($languageCode);
        $options['required'] = $fieldDefinition->isRequired;
        $options['ibexa_forms']['description'] = $fieldDefinition->getDescription($languageCode);
        $options['ibexa_forms']['language_code'] = $languageCode;
        $options['ibexa_forms']['fielddefinition'] = $fieldDefinition;

        if ($content !== null) {
            $options['ibexa_forms']['content'] = $content;
        }

        $options['constraints'] = [];
        if ($fieldDefinition->isRequired) {
            $options['constraints'][] = new Constraints\NotBlank();
        }

        return $options;
    }

    /**
     * Adds a hidden field to the from, indicating that empty value passed
     * for update should be ignored.
     */
    protected function skipEmptyUpdate(FormBuilderInterface $formBuilder, string $fieldDefinitionIdentifier): void
    {
        $options = [
            'mapped' => false,
            'data' => 'yes',
        ];

        $formBuilder->add(
            "ibexa_forms_skip_empty_update_{$fieldDefinitionIdentifier}",
            HiddenType::class,
            $options
        );
    }
}
