<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Country\Value as CountryValue;
use Ibexa\Core\FieldType\Value;
use \Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

/**
 * Overrides the original country handler to save country alpha2 code to collected info
 * attribute instead of the country name.
 */
final class CountryFieldHandler implements CustomLegacyFieldHandlerInterface
{
    public function supports(Value $value): bool
    {
        return $value instanceof CountryValue;
    }

    public function toString(Value $value, FieldDefinition $fieldDefinition): string
    {
        return (string)$value;
    }

    /**
     * @param \Ibexa\Core\FieldType\Country\Value $value
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition): FieldValue
    {
        return FieldValue::withStringValue($fieldDefinition->id, implode(', ', array_column($value->countries, 'Alpha2')));
    }
}
