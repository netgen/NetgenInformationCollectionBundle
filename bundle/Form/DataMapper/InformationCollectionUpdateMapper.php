<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\DataMapper;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\EzFormsBundle\Form\DataMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;
use RuntimeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * Class InformationCollectionMapper.
 */
class InformationCollectionUpdateMapper extends DataMapper
{
    /**
     * Maps data from eZ Publish structure to the form.
     *
     * @param \Symfony\Component\Form\FormInterface $form
     * @param \Netgen\Bundle\EzFormsBundle\Form\DataWrapper $data
     * @param \Symfony\Component\PropertyAccess\PropertyPathInterface $propertyPath
     */
    protected function mapToForm(
        FormInterface $form,
        DataWrapper $data,
        PropertyPathInterface $propertyPath
    ) {
        /** @var ContentType $contentType */
        $contentType = $data->definition;

        $fieldDefinitionIdentifier = (string) $propertyPath;
        $fieldDefinition = $contentType->getFieldDefinition($fieldDefinitionIdentifier);

        if (null === $fieldDefinition) {
            throw new RuntimeException(
                "Data definition does not contain expected FieldDefinition '{$fieldDefinitionIdentifier}'"
            );
        }

        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;

        $handler = $this->fieldTypeHandlerRegistry->get($fieldTypeIdentifier);

        /** @var \Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct $struct */
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

    /**
     * Maps data from form to the eZ Publish structure.
     *
     * @param \Symfony\Component\Form\FormInterface $form
     * @param \Netgen\Bundle\EzFormsBundle\Form\DataWrapper $data
     * @param \Symfony\Component\PropertyAccess\PropertyPathInterface $propertyPath
     */
    protected function mapFromForm(
        FormInterface $form,
        DataWrapper $data,
        PropertyPathInterface $propertyPath
    ) {
        /** @var InformationCollectionStruct $payload */
        $payload = $data->payload;
        /** @var ContentType $contentType */
        $contentType = $data->definition;

        $fieldDefinitionIdentifier = (string) $propertyPath;
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
