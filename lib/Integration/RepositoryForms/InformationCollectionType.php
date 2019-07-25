<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Integration\RepositoryForms;

use EzSystems\RepositoryForms\Form\Type\Content\BaseContentType;
use Netgen\InformationCollection\API\Value\InformationCollectionStruct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformationCollectionType extends AbstractType
{
    public function getName()
    {
        $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_information_collection';
    }

    public function getParent()
    {
        return BaseContentType::class;
    }

//    public function buildForm(FormBuilderInterface $builder, array $options)
//    {
//        $builder
//            ->add('publish', SubmitType::class, ['label' => 'content.publish_button']);
//    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'drafts_enabled' => false,
                'data_class' => InformationCollectionStruct::class,
                'translation_domain' => 'ezrepoforms_content',
            ]);
    }
}
