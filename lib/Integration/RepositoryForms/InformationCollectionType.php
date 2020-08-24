<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Integration\RepositoryForms;

use Netgen\InformationCollection\API\Value\InformationCollectionStruct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformationCollectionType extends AbstractType implements DataMapperInterface
{
    public const FORM_BLOCK_PREFIX = 'information_collection';

    public function getName()
    {
        $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return self::FORM_BLOCK_PREFIX;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var InformationCollectionStruct $struct */
        $struct = $options['data'];

        foreach ($struct->getFieldsData() as $fieldsDatum) {
            $builder->add($fieldsDatum->fieldDefinition->identifier, InformationCollectionFieldType::class, [
                'languageCode' => $options['languageCode'],
                'mainLanguageCode' => $options['mainLanguageCode'],
            ]);
        }

        $builder->add('content_id', HiddenType::class, ['data' => $struct->getContent()->id]);
        $builder->add('content_type_id', HiddenType::class, ['data' => $struct->getContentType()->id]);

        $builder->setDataMapper($this);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['languageCode'] = $options['languageCode'];
        $view->vars['mainLanguageCode'] = $options['mainLanguageCode'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['translation_domain' => 'ezplatform_content_forms_content'])
            ->setRequired(['languageCode', 'mainLanguageCode']);
    }

    public function mapDataToForms($viewData, iterable $forms)
    {
        if (null === $viewData) {
            return;
        }

        if (!$viewData instanceof InformationCollectionStruct) {
            throw new UnexpectedTypeException($viewData, InformationCollectionStruct::class);
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        foreach ($viewData->getFieldsData() as $fieldsDatum) {
            $forms[$fieldsDatum->fieldDefinition->identifier]->setData($fieldsDatum);
        }
    }

    public function mapFormsToData(iterable $forms, &$viewData)
    {

    }
}
