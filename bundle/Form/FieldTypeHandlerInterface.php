<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Interface FieldTypeHandlerInterface.
 */
interface FieldTypeHandlerInterface
{
    /**
     * Converts the Ibexa Platform FieldType value to a format that can be accepted by the form.
     *
     * @see buildFieldCreateForm
     * @see buildFieldUpdateForm
     *
     */
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): mixed;

    /**
     * Converts the form data to a format that can be accepted by Ibexa Platform FieldType.
     *
     * @see buildFieldCreateForm
     * @see buildFieldUpdateForm
     *
     */
    public function convertFieldValueFromForm(mixed $data): mixed;

    /**
     * Builds the form the given $fieldDefinition and $languageCode for creating.
     */
    public function buildFieldCreateForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode
    ): void;

    /**
     * Builds the form the given $fieldDefinition and $languageCode for updating.
     */
    public function buildFieldUpdateForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        Content $content,
        string $languageCode
    ): void;
}
