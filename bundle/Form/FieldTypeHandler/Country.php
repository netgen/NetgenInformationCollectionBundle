<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Country\Value as CountryValue;
use Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use function array_flip;
use function array_key_exists;
use function array_keys;
use function is_array;
use function reset;

final class Country extends FieldTypeHandler
{
    /**
     * Country codes.
     */
    protected array $countryData;

    /**
     * Removed redundant data from array.
     */
    protected array $filteredCountryData;

    public function __construct(array $countryData)
    {
        $this->countryData = $countryData;

        foreach ($countryData as $countryCode => $country) {
            $this->filteredCountryData[$countryCode] = $country['Name'];
        }
    }

    /**
     * @param \Ibexa\Core\FieldType\Country\Value $value
     */
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): mixed
    {
        $isMultiple = true;
        if ($fieldDefinition !== null) {
            $fieldSettings = $fieldDefinition->getFieldSettings();
            $isMultiple = $fieldSettings['isMultiple'];
        }

        if (!$isMultiple) {
            if (empty($value->countries)) {
                return '';
            }

            $keys = array_keys($value->countries);

            return reset($keys);
        }

        return array_keys($value->countries);
    }

    public function convertFieldValueFromForm(mixed $data): CountryValue
    {
        $country = [];

        // case if multiple is true
        if (is_array($data)) {
            foreach ($data as $countryCode) {
                if (array_key_exists($countryCode, $this->countryData)) {
                    $country[$countryCode] = $this->countryData[$countryCode];
                }
            }
        } elseif (array_key_exists($data, $this->countryData)) {
            $country[$data] = $this->countryData[$data];
        }

        return new CountryValue($country);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $options['expanded'] = false;
        $options['multiple'] = $fieldDefinition->getFieldSettings()['isMultiple'] ?? false;

        $options['choices'] = array_flip($this->filteredCountryData);

        $formBuilder->add($fieldDefinition->identifier, ChoiceType::class, $options);
    }
}
