<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Factory;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use Netgen\InformationCollection\API\FieldHandler\CustomFieldHandlerInterface;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;
use Netgen\InformationCollection\Core\Persistence\FieldHandler\FieldHandlerRegistry;

class FieldDataFactory
{
    /**
     * @var \Netgen\InformationCollection\Core\Persistence\FieldHandler\FieldHandlerRegistry
     */
    protected $registry;

    /**
     * FieldDataFactory constructor.
     *
     * @param \Netgen\InformationCollection\Core\Persistence\FieldHandler\FieldHandlerRegistry $registry
     */
    public function __construct(FieldHandlerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Returns value object that represents legacy value.
     *
     * @param \eZ\Publish\Core\FieldType\Value $value
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
     *
     * @return \Netgen\InformationCollection\API\Value\Legacy\FieldValue
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition): FieldValue
    {
        /** @var CustomFieldHandlerInterface $handler */
        $handler = $this->registry->handle($value);

        if (!$handler instanceof CustomFieldHandlerInterface) {
            return new FieldValue($fieldDefinition->id, (string) $value);
        }

        if ($handler instanceof CustomLegacyFieldHandlerInterface) {
            return $handler->getLegacyValue($value, $fieldDefinition);
        }

        return new FieldValue($fieldDefinition->id, $handler->toString($value, $fieldDefinition));
    }
}
