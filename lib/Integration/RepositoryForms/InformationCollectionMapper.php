<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Integration\RepositoryForms;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\Mapper\FormDataMapperInterface;
use Netgen\InformationCollection\API\Value\InformationCollectionStruct;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformationCollectionMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Content $contentDraft
     * @param array $params
     *
     * @return InformationCollectionStruct
     */
    public function mapToFormData(ValueObject $contentDraft, array $params = [])
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);

        $params = $optionsResolver->resolve($params);
        $languageCode = $params['languageCode'];

        $fields = [];
        if ($contentDraft instanceof Content) {
            $fields = $contentDraft->getFieldsByLanguage($languageCode);
        }

        $informationCollectionFields = [];

        /** @var FieldDefinition $fieldDef */
        foreach ($params['contentType']->fieldDefinitions as $fieldDef) {
            if ($fieldDef->isInfoCollector) {
                $field = $fields[$fieldDef->identifier];

                $fieldData = new FieldData(
                    [
                        'fieldDefinition' => $fieldDef,
                        'field' => $field,
                    ]
                );

                $informationCollectionFields[] = $fieldData;
            }
        }

        return new InformationCollectionStruct(
            $contentDraft,
            $params['contentType'],
            $informationCollectionFields,
            $languageCode
        );
    }

    private function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setRequired(['languageCode', 'contentType'])
            ->setAllowedTypes('contentType', ContentType::class);
    }
}
