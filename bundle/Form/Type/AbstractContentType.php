<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\Type;

use Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandlerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;

abstract class AbstractContentType extends AbstractType
{
    protected FieldTypeHandlerRegistry $fieldTypeHandlerRegistry;

    protected DataMapperInterface $dataMapper;

    public function __construct(FieldTypeHandlerRegistry $fieldTypeHandlerRegistry, DataMapperInterface $dataMapper)
    {
        $this->fieldTypeHandlerRegistry = $fieldTypeHandlerRegistry;
        $this->dataMapper = $dataMapper;
    }
}
