<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\RepositoryForms;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use EzSystems\RepositoryForms\Data\Mapper\FormDataMapperInterface;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\RepositoryForms\Data\Content\ContentUpdateData;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformationCollectionMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject|\eZ\Publish\API\Repository\Values\Content\Content $contentDraft
     * @param array $params
     *
     * @return InformationCollectionData
     */
    public function mapToFormData(ValueObject $contentDraft, array $params = [])
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);

        $params = $optionsResolver->resolve($params);
        $languageCode = $params['languageCode'];

        $data = new InformationCollectionData(['contentDraft' => $contentDraft]);
        $data->initialLanguageCode = $languageCode;

        $fields = $contentDraft->getFieldsByLanguage($languageCode);
        /** @var FieldDefinition $fieldDef */
        foreach ($params['contentType']->fieldDefinitions as $fieldDef) {
            if ($fieldDef->isInfoCollector) {
                $field = $fields[$fieldDef->identifier];
                $data->addFieldData(new FieldData([
                    'fieldDefinition' => $fieldDef,
                    'field' => $field,
                    'value' => $field->value,
                ]));
            }
        }

        return $data;
    }

    private function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setRequired(['languageCode', 'contentType'])
            ->setAllowedTypes('contentType', ContentType::class);
    }
}