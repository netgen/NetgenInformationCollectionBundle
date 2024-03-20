<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\MapLocation\Value as MapLocationValue;
use Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;
use Netgen\Bundle\InformationCollectionBundle\Form\Type\MapType;
use Symfony\Component\Form\FormBuilderInterface;
use function is_array;

final class MapLocation extends FieldTypeHandler
{
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): array
    {
        return [
            'latitude' => empty($value->latitude) ? null : $value->latitude,
            'longitude' => empty($value->longitude) ? null : $value->longitude,
            'address' => empty($value->address) ? null : $value->address,
        ];
    }

    public function convertFieldValueFromForm($data): ?MapLocationValue
    {
        if (!is_array($data)) {
            return null;
        }

        return new MapLocationValue(
            [
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'address' => $data['address'],
            ]
        );
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $options['block_name'] = 'ibexa_forms_map';

        $formBuilder->add($fieldDefinition->identifier, MapType::class, $options);
    }
}
