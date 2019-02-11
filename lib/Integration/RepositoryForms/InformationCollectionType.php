<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Integration\RepositoryForms;

use eZ\Publish\API\Repository\Values\Content\ContentStruct;
use EzSystems\RepositoryForms\Form\Type\Content\BaseContentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
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
                'data_class' => InformationCollectionData::class,
                'translation_domain' => 'ezrepoforms_content',
            ]);
    }
}
