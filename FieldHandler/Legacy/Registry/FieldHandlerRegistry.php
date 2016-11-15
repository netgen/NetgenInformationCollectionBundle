<?php

namespace Netgen\Bundle\InformationCollectionBundle\FieldHandler\Legacy\Registry;

use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Legacy\LegacyFieldHandlerInterface;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\Value;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyHandledFieldValue;

class FieldHandlerRegistry
{
    /**
     * @var array
     */
    protected $fieldHandlers;

    /**
     * FieldHandlerRegistry constructor.
     *
     * @param array $fieldHandlers
     */
    public function __construct(array $fieldHandlers = [])
    {
        $this->fieldHandlers = $fieldHandlers;
    }

    /**
     * Adds new handler
     *
     * @param LegacyFieldHandlerInterface $handler
     */
    public function addHandler(LegacyFieldHandlerInterface $handler)
    {
        $this->fieldHandlers[] = $handler;
    }

    /**
     * @param Value $value
     * @param FieldDefinition $fieldDefinition
     *
     * @return LegacyHandledFieldValue
     *
     * @throws \RuntimeException
     */
    public function handleField(Value $value, FieldDefinition $fieldDefinition)
    {
        foreach ($this->fieldHandlers as $fieldHandler) {

            if ($fieldHandler->supports($value)) {
                return $fieldHandler->getValue($value, $fieldDefinition);
            }
        }

        throw new \RuntimeException('LegacyFieldHandler for field not found in FieldHandlerRegistry');
    }

    /**
     * @param Value $value
     * @param FieldDefinition $fieldDefinition
     *
     * @return LegacyHandledFieldValue
     *
     * @throws \RuntimeException
     */
    public function toStringHandle(Value $value, FieldDefinition $fieldDefinition)
    {
        foreach ($this->fieldHandlers as $fieldHandler) {

            if ($fieldHandler->supports($value)) {
                return $fieldHandler->toString($value, $fieldDefinition);
            }
        }

        throw new \RuntimeException('LegacyFieldHandler for field not found in FieldHandlerRegistry');
    }
}