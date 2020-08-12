<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Form\Type;

use Netgen\InformationCollection\API\Value\Export\ExportCriteria;
use Netgen\InformationCollection\API\Value\Filter\ContentId;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Netgen\InformationCollection\Core\Export\ExportResponseFormatterRegistry;

class ExportType extends AbstractType implements DataMapperInterface
{
    /**
     * @var \Netgen\InformationCollection\Core\Export\ExportResponseFormatterRegistry
     */
    protected $exportResponseFormatterRegistry;

    /**
     * ExportType constructor.
     *
     * @param \Netgen\InformationCollection\Core\Export\ExportResponseFormatterRegistry $exportResponseFormatterRegistry
     */
    public function __construct(ExportResponseFormatterRegistry $exportResponseFormatterRegistry)
    {
        $this->exportResponseFormatterRegistry = $exportResponseFormatterRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('dateFrom', DateType::class, [
            'required' => true,
            'widget' => 'single_text',
            'input'  => 'datetime_immutable',
            'label' => 'netgen_information_collection_admin_export_from',
            'translation_domain' => 'netgen_information_collection_admin',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Type(\DateTimeInterface::class),
            ],
        ]);

        $builder->add('dateTo', DateType::class, [
            'required' => true,
            'widget' => 'single_text',
            'input'  => 'datetime_immutable',
            'label' => 'netgen_information_collection_admin_export_to',
            'translation_domain' => 'netgen_information_collection_admin',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Type(\DateTimeInterface::class),
            ],
        ]);

        $availableFormatters = [];
        foreach ($this->exportResponseFormatterRegistry->getExportResponseFormatters() as $formatter) {
            $availableFormatters['netgen_information_collection_admin_export_type_' . $formatter->getIdentifier()] = $formatter->getIdentifier();
        }

        $builder->add('exportType', ChoiceType::class, [
            'required' => true,
            'choices' => $availableFormatters,
            'translation_domain' => 'netgen_information_collection_admin',
            'label' => 'netgen_information_collection_admin_export_type',
            'disabled' => empty($availableFormatters) ? true : false,
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ]);

        $builder->add('offset', IntegerType::class, [
            'required' => true,
            'data' => 0,
            'label' => 'netgen_information_collection_admin_offset',
            'translation_domain' => 'netgen_information_collection_admin',
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ]);

        $builder->add('limit', IntegerType::class, [
            'required' => true,
            'data' => 100,
            'label' => 'netgen_information_collection_admin_limit',
            'translation_domain' => 'netgen_information_collection_admin',
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ]);

        $builder->add('contentId', HiddenType::class, [
            'required' => true,
            'data' => $options['contentId'],
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ]);

        $builder->add('export', SubmitType::class, [
            'label' => 'netgen_information_collection_admin_export_export',
            'translation_domain' => 'netgen_information_collection_admin',
            'disabled' => empty($availableFormatters) ? true : false,
        ]);

        $builder->add('cancel', SubmitType::class, [
            'label' => 'netgen_information_collection_admin_export_cancel',
            'translation_domain' => 'netgen_information_collection_admin',
        ]);

        $builder->setDataMapper($this);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($availableFormatters) {
            if (empty($availableFormatters)) {
                $formError = new FormError('netgen_information_collection_admin_export_no_formatters');
                $event->getForm()->addError($formError);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExportCriteria::class,
            'empty_data' => null,
        ]);

        $resolver->setRequired('contentId');
        $resolver->setAllowedTypes('contentId', 'int');
    }

    public function mapDataToForms($viewData, iterable $forms)
    {

    }

    public function mapFormsToData(iterable $forms, &$viewData)
    {
        $forms = iterator_to_array($forms);

        $contentId = new ContentId((int)$forms['contentId']->getData(), $forms['offset']->getData(), $forms['limit']->getData());

        $viewData = new ExportCriteria(
            $contentId,
            $forms['dateFrom']->getData(),
            $forms['dateTo']->getData(),
            $forms['exportType']->getData()
        );
    }


}
