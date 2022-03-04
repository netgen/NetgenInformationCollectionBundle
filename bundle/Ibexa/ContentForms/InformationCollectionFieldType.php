<?php


namespace Netgen\Bundle\InformationCollectionBundle\Ibexa\ContentForms;

use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\ContentForms\FieldType\FieldTypeFormMapperDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformationCollectionFieldType extends AbstractType
{
    /**
     * @var \Ibexa\ContentForms\FieldType\FieldTypeFormMapperDispatcherInterface
     */
    private $fieldTypeFormMapper;

    public function __construct(FieldTypeFormMapperDispatcherInterface $fieldTypeFormMapper)
    {
        $this->fieldTypeFormMapper = $fieldTypeFormMapper;
    }

    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix(): string
    {
        return 'ezplatform_content_forms_content_field';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'content' => null,
                'contentCreateStruct' => null,
                'contentUpdateStruct' => null,
                'data_class' => FieldData::class,
                'translation_domain' => 'ezplatform_content_forms_content',
            ])
            ->setRequired(['languageCode', 'mainLanguageCode']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['languageCode'] = $options['languageCode'];
        $view->vars['mainLanguageCode'] = $options['mainLanguageCode'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $this->fieldTypeFormMapper->map($event->getForm(), $event->getData());
        });
    }
}
