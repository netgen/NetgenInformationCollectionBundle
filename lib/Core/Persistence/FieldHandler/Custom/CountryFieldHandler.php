<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Country\Value as CountryValue;
use Ibexa\Core\FieldType\Country\Type as CountryType;
use Ibexa\Core\FieldType\Value;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

use function array_column;
use function implode;

/**
 * Overrides the original country handler to save country alpha2 code to collected info
 * attribute instead of the country name.
 */
final class CountryFieldHandler implements CustomLegacyFieldHandlerInterface
{

    private CountryType $countryType;

    public function __construct(CountryType $countryType)
    {
        $this->countryType = $countryType;
    }

    public function supports(Value $value): bool
    {
        return $value instanceof CountryValue;
    }

    public function toString(Value $value, FieldDefinition $fieldDefinition): string
    {
        return (string) $value;
    }

    /**
     * @param \Ibexa\Core\FieldType\Country\Value $value
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition): FieldValue
    {
        return FieldValue::withStringValue($fieldDefinition->id, implode(', ', array_column($value->countries, 'Alpha2')));
    }

    public function fromLegacyValue(FieldValue $legacyData)
    {
        $countryCodes = explode(',', $legacyData->getDataText());
        return $this->countryType->fromHash(array_map(fn($code) => trim($code), $countryCodes));
    }
}
