<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\DataMapper;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\IbexaFormsBundle\Form\DataMapper;
use Netgen\Bundle\IbexaFormsBundle\Form\DataWrapper;
use Netgen\Bundle\IbexaFormsBundle\Form\Payload\InformationCollectionStruct;
use RuntimeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class InformationCollectionUpdateMapper extends DataMapper
{

    protected function mapToForm(
        FormInterface         $form,
        DataWrapper           $data,
        PropertyPathInterface $propertyPath
    ): void
    {
        $contentType = $data->definition;

        $fieldDefinitionIdentifier = (string)$propertyPath;
        $fieldDefinition = $contentType->getFieldDefinition($fieldDefinitionIdentifier);

        if (null === $fieldDefinition) {
            throw new RuntimeException(
                "Data definition does not contain expected FieldDefinition '{$fieldDefinitionIdentifier}'"
            );
        }

        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;

        $handler = $this->fieldTypeHandlerRegistry->get($fieldTypeIdentifier);

        $struct = $data->payload;

        $collectedFieldValue = $struct->getCollectedFieldValue($fieldDefinitionIdentifier);
        if ($collectedFieldValue === null) {
            return;
        }

        $form->setData(
            $handler->convertFieldValueToForm(
                $struct->getCollectedFieldValue($fieldDefinitionIdentifier),
                $fieldDefinition
            )
        );
    }

    protected function mapFromForm(
        FormInterface         $form,
        DataWrapper           $data,
        PropertyPathInterface $propertyPath
    ): void
    {
        $payload = $data->payload;
        $contentType = $data->definition;

        $fieldDefinitionIdentifier = (string)$propertyPath;
        $fieldDefinition = $contentType->getFieldDefinition($fieldDefinitionIdentifier);

        if (null === $fieldDefinition) {
            throw new RuntimeException(
                "Data definition does not contain expected FieldDefinition '{$fieldDefinitionIdentifier}'"
            );
        }

        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;
        $handler = $this->fieldTypeHandlerRegistry->get($fieldTypeIdentifier);

        $payload->setCollectedFieldValue(
            $fieldDefinitionIdentifier,
            $handler->convertFieldValueFromForm($form->getData())
        );
    }
}
