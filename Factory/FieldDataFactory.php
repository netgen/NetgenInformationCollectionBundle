<?php

namespace Netgen\Bundle\InformationCollectionBundle\Factory;

use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomFieldHandlerInterface;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\FieldHandlerRegistry;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;

class FieldDataFactory
{
    /**
     * @var FieldHandlerRegistry
     */
    protected $registry;

    public function __construct(FieldHandlerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Returns value object that represents legacy value
     *
     * @param Value $value
     * @param FieldDefinition $fieldDefinition
     *
     * @return LegacyData
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition)
    {
        /** @var CustomFieldHandlerInterface $handler */
        $handler = $this->registry->handle($value);

        if (!is_null($handler)) {
            $value = $handler->toString($value, $fieldDefinition);
        }

        return new LegacyData(
            $fieldDefinition->id,
            0,
            0,
            (string)$value
        );
    }
}