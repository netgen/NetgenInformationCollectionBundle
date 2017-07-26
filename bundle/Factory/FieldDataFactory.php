<?php

namespace Netgen\Bundle\InformationCollectionBundle\Factory;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomFieldHandlerInterface;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\FieldHandlerRegistry;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;

class FieldDataFactory
{
    /**
     * @var FieldHandlerRegistry
     */
    protected $registry;

    /**
     * FieldDataFactory constructor.
     *
     * @param FieldHandlerRegistry $registry
     */
    public function __construct(FieldHandlerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Returns value object that represents legacy value.
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

        if (null !== $handler) {
            $value = $handler->toString($value, $fieldDefinition);
        }

        return new LegacyData(
            array(
                'contentClassAttributeId' => $fieldDefinition->id,
                'dataInt' => 0,
                'dataFloat' => 0,
                'dataText' => (string) $value,
            )
        );
    }
}
