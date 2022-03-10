<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Factory;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Value;
use Netgen\InformationCollection\API\Factory\FieldValueFactoryInterface;
use Netgen\InformationCollection\API\FieldHandler\CustomFieldHandlerInterface;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;
use Netgen\InformationCollection\Core\Persistence\FieldHandler\FieldHandlerRegistry;

class FieldDataFactory implements FieldValueFactoryInterface
{
    protected FieldHandlerRegistry $registry;

    public function __construct(FieldHandlerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Returns value object that represents legacy value.
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
