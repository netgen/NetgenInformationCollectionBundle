<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form;

use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

use function is_array;
use function is_object;

/**
 * A data mapper using property paths to read/write data.
 */
abstract class DataMapper implements DataMapperInterface
{
    protected FieldTypeHandlerRegistry $fieldTypeHandlerRegistry;

    protected PropertyAccessorInterface $propertyAccessor;

    /**
     * Creates a new property path mapper.
     */
    public function __construct(
        FieldTypeHandlerRegistry $fieldTypeHandlerRegistry,
        ?PropertyAccessorInterface $propertyAccessor = null
    ) {
        $this->fieldTypeHandlerRegistry = $fieldTypeHandlerRegistry;
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    public function mapDataToForms($data, \Traversable $forms): void
    {
        $empty = null === $data || [] === $data;

        if (!$empty && !is_array($data) && !is_object($data)) {
            throw new UnexpectedTypeException($data, 'object, array or empty');
        }

        foreach ($forms as $form) {
            $propertyPath = $form->getPropertyPath();
            $config = $form->getConfig();

            if ($data instanceof DataWrapper && null !== $propertyPath && $config->getMapped()) {
                /* @var $data \Netgen\Bundle\InformationCollectionBundle\Form\DataWrapper */
                $this->mapToForm($form, $data, $propertyPath);
            } elseif (!$empty && null !== $propertyPath && $config->getMapped()) {
                $form->setData($this->propertyAccessor->getValue($data, $propertyPath));
            } else {
                $form->setData($form->getConfig()->getData());
            }
        }
    }

    public function mapFormsToData(\Traversable $forms, &$data): void
    {
        if (null === $data) {
            return;
        }

        if (!is_array($data) && !is_object($data)) {
            throw new UnexpectedTypeException($data, 'object, array or empty');
        }

        foreach ($forms as $form) {
            $propertyPath = $form->getPropertyPath();
            $config = $form->getConfig();

            // Write-back is disabled if the form is not synchronized (transformation failed),
            // if the form was not submitted and if the form is disabled (modification not allowed)
            if (
                null === $propertyPath
                || !$config->getMapped()
                || !$form->isSubmitted()
                || !$form->isSynchronized()
                || $form->isDisabled()
            ) {
                continue;
            }

            // If $data is out ContentCreateStruct, we need to map it to the corresponding field
            // in the struct
            if ($data instanceof DataWrapper) {
                $this->mapFromForm($form, $data, $propertyPath);

                continue;
            }

            // If the field is of type DateTime and the data is the same skip the update to
            // keep the original object hash
            if (
                $form->getData() instanceof \DateTimeImmutable
                && $form->getData() === $this->propertyAccessor->getValue($data, $propertyPath)
            ) {
                continue;
            }

            // If the data is identical to the value in $data, we are
            // dealing with a reference
            if (
                is_object($data)
                && $config->getByReference()
                && $form->getData() === $this->propertyAccessor->getValue($data, $propertyPath)
            ) {
                continue;
            }

            $this->propertyAccessor->setValue($data, $propertyPath, $form->getData());
        }
    }

    /**
     * Maps data from Ibexa Platform structure to the form.
     */
    abstract protected function mapToForm(
        FormInterface $form,
        DataWrapper $data,
        PropertyPathInterface $propertyPath
    ): void;

    /**
     * Maps data from form to the Ibexa Platform structure.
     */
    abstract protected function mapFromForm(
        FormInterface $form,
        DataWrapper $data,
        PropertyPathInterface $propertyPath
    ): void;
}
