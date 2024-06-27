<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Ibexa\ContentForms;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Netgen\InformationCollection\API\Value\InformationCollectionStruct;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;

final class InformationCollectionMapper
{
    /**
     * Maps a ValueObject from Ibexa content repository to a data usable as underlying form data (e.g. create/update struct).
     */
    public function mapToFormData(Content $content, Location $location, ContentType $contentType): InformationCollectionStruct
    {
        $fieldsData = [];

        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition */
        foreach ($contentType->fieldDefinitions as $fieldDefinition) {
            if ($fieldDefinition->isInfoCollector) {
                $field = $content->getField($fieldDefinition->identifier);

                $fieldsData[] = new FieldData([
                    'fieldDefinition' => $fieldDefinition,
                    'field' => $field,
                ]);
            }
        }

        return new InformationCollectionStruct(
            $content,
            $location,
            $contentType,
            $fieldsData
        );
    }
}
