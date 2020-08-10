<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Integration\RepositoryForms;

use EzSystems\EzPlatformContentForms\FieldType\FieldTypeFormMapperDispatcherInterface;
use EzSystems\EzPlatformContentForms\Form\Type\Content\BaseContentType;
use EzSystems\EzPlatformContentForms\Form\Type\Content\ContentFieldType;
use Netgen\InformationCollection\API\Value\InformationCollectionStruct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformationCollectionType extends AbstractType
{
    /**
     * @var FieldTypeFormMapperDispatcherInterface
     */
    private $fieldTypeFormMapper;

    public function __construct(FieldTypeFormMapperDispatcherInterface $fieldTypeFormMapper)
    {
        $this->fieldTypeFormMapper = $fieldTypeFormMapper;
    }

    public function getName()
    {
        $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'information_collection';
    }

    public function getParent()
    {
        return BaseContentType::class;
    }

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
