<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;
use Netgen\Bundle\InformationCollectionBundle\Form\Type\FieldType\UserCreateType;
use Netgen\Bundle\InformationCollectionBundle\Form\Type\FieldType\UserUpdateType;
use Symfony\Component\Form\FormBuilderInterface;

final class User extends FieldTypeHandler
{
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): void
    {
        // Returning nothing here because user data is mapped in mapper as an exceptional case
    }

    public function buildFieldCreateForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode);

        $formBuilder->add($fieldDefinition->identifier, UserCreateType::class, $options);
    }

    public function buildFieldUpdateForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        Content $content,
        string $languageCode
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $formBuilder->add($fieldDefinition->identifier, UserUpdateType::class, $options);
    }
}
