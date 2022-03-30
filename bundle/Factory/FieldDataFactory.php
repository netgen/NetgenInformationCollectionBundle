<?php

namespace Netgen\Bundle\InformationCollectionBundle\Factory;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomFieldHandlerInterface;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomLegacyFieldHandlerInterface;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\FieldHandlerRegistry;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;

class FieldDataFactory implements LegacyDataFactoryInterface
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

        if (null === $handler) {

            return new LegacyData(
                $fieldDefinition->id,
                0,
                0,
                (string)$value
            );
        }

        if ($handler instanceof CustomLegacyFieldHandlerInterface) {
            return $handler->getLegacyValue($value, $fieldDefinition);
        }

        return new LegacyData(
            $fieldDefinition->id,
            0,
            0,
            (string) $handler->toString($value, $fieldDefinition)
        );
    }

    /**
     * Returns the field value constructed from value object that represents legacy value.
     *
     * @param LegacyData $legacyData
     * @param FieldDefinition $fieldDefinition
     *
     * @return Value
     */
    public function fromLegacyValue(LegacyData $legacyData, FieldDefinition $fieldDefinition)
    {
        /** @var CustomFieldHandlerInterface $handler */
        $handler = $this->registry->handle($fieldDefinition->defaultValue);

        if (!$handler instanceof CustomLegacyFieldHandlerInterface) {
            return null;
        }

        return $handler->fromLegacyValue($legacyData, $fieldDefinition);
    }
}
