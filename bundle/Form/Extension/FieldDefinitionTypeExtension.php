<?php
namespace Netgen\Bundle\InformationCollectionBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use EzSystems\RepositoryForms\Form\Type\FieldDefinition\FieldDefinitionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class FieldDefinitionTypeExtension extends AbstractTypeExtension
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'isInfoCollector',
            CheckboxType::class,
            [
                'required' => false,
                'label' => 'field_definition.is_infocollector'
            ]
        );
        
        return $this;
    }

    public function getExtendedType()
    {
        return FieldDefinitionType::class;
    }
}