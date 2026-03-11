<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Country\Type as CountryType;
use Ibexa\Core\FieldType\Country\Value;
use Ibexa\Core\FieldType\Country\Value as CountryValue;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

use function array_column;
use function array_map;
use function explode;
use function implode;
use function trim;

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

    public function supports(ValueInterface $value): bool
    {
        return $value instanceof CountryValue;
    }

    public function toString(ValueInterface $value, FieldDefinition $fieldDefinition): string
    {
        return (string) $value;
    }

    /**
     * @param Value $value
     */
    public function getLegacyValue(ValueInterface $value, FieldDefinition $fieldDefinition): FieldValue
    {
        return FieldValue::withStringValue($fieldDefinition->id, implode(', ', array_column($value->countries, 'Alpha2')));
    }

    public function fromLegacyValue(FieldValue $legacyData): ?ValueInterface
    {
        $countryCodes = explode(',', $legacyData->getDataText());

        return $this->countryType->fromHash(array_map(static fn ($code) => trim($code), $countryCodes));
    }
}
