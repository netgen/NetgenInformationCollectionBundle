<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use EzSystems\RepositoryForms\Form\Type\FieldDefinition\FieldDefinitionType;
use EzSystems\RepositoryForms\Form\Type\ContentType\ContentTypeUpdateType;

class ContentTypeUpdateTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'fieldDefinitionsData',
            CollectionType::class,
            [
                'entry_type' => FieldDefinitionType::class,
                'entry_options' => [
                    'languageCode' => $options['languageCode']
                ],
                'label' => 'content_type.field_definitions_data',
            ]
        );
    }
    
    public function getExtendedType()
    {
        return ContentTypeUpdateType::class;
    }
}