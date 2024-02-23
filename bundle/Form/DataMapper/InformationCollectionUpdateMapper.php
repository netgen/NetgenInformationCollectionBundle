<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\DataMapper;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\IbexaFormsBundle\Form\DataMapper;
use Netgen\Bundle\IbexaFormsBundle\Form\DataWrapper;
use Netgen\Bundle\IbexaFormsBundle\Form\Payload\InformationCollectionStruct;
use RuntimeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

use function sprintf;

class InformationCollectionUpdateMapper extends DataMapper
{
    protected function mapToForm(
        FormInterface $form,
        DataWrapper $data,
        PropertyPathInterface $propertyPath
    ): void {
        /** @var ContentType $contentType */
        $contentType = $data->definition;

        $fieldDefinitionIdentifier = (string) $propertyPath;
        $fieldDefinition = $contentType->getFieldDefinition($fieldDefinitionIdentifier);

        if ($fieldDefinition === null) {
            throw new RuntimeException(sprintf('Data definition does not contain expected FieldDefinition %s', $fieldDefinitionIdentifier));
        }

        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;

        $handler = $this->fieldTypeHandlerRegistry->get($fieldTypeIdentifier);

        /** @var InformationCollectionStruct $struct */
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
        FormInterface $form,
        DataWrapper $data,
        PropertyPathInterface $propertyPath
    ): void {
        $payload = $data->payload;
        $contentType = $data->definition;

        $fieldDefinitionIdentifier = (string) $propertyPath;
        $fieldDefinition = $contentType->getFieldDefinition($fieldDefinitionIdentifier);

        if ($fieldDefinition === null) {
            throw new RuntimeException(sprintf('Data definition does not contain expected FieldDefinition %s', $fieldDefinitionIdentifier));
        }

        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;
        $handler = $this->fieldTypeHandlerRegistry->get($fieldTypeIdentifier);

        $payload->setCollectedFieldValue(
            $fieldDefinitionIdentifier,
            $handler->convertFieldValueFromForm($form->getData())
        );
    }
}
