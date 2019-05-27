<?php

namespace Netgen\InformationCollection\Integration\RepositoryForms;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class FieldDefinitionTypeExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return \EzSystems\RepositoryForms\Form\Type\FieldDefinition\FieldDefinitionType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isTranslation = $options['languageCode'] !== $options['mainLanguageCode'];

        $builder->add('isInfoCollector', CheckboxType::class, [
            'required' => false,
            'label' => 'field_definition.is_infocollector',
            'disabled' => $isTranslation,
        ]);
    }
}
