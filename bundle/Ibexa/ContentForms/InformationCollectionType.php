<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Ibexa\ContentForms;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Netgen\Bundle\InformationCollectionBundle\Form\Type\CaptchaType;
use Netgen\InformationCollection\API\Service\CaptchaService;
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

    private CaptchaService $captchaService;

    private ConfigResolverInterface $configResolver;

    public function __construct(CaptchaService $captchaService, ConfigResolverInterface $configResolver)
    {
        $this->captchaService = $captchaService;
        $this->configResolver = $configResolver;
    }

    public function getBlockPrefix(): string
    {
        return self::FORM_BLOCK_PREFIX;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var InformationCollectionStruct $struct */
        $struct = $options['data'];

        foreach ($struct->getFieldsData() as $fieldsDatum) {
            $builder->add($fieldsDatum->fieldDefinition->identifier, InformationCollectionFieldType::class, [
                'languageCode' => $options['languageCode'],
                'mainLanguageCode' => $options['mainLanguageCode'],
                'error_bubbling' => false,
            ]);
        }

        $builder->add('content_id', HiddenType::class, ['data' => $struct->getContent()->id]);
        $builder->add('content_type_id', HiddenType::class, ['data' => $struct->getContentType()->id]);
        $builder->add('discriminator', HiddenType::class, ['data' => $options['discriminator']]);

        if ($this->captchaService->isEnabled($struct->getLocation())) {

            $config = $this->captchaService->getConfig($struct->getLocation());

            $builder->add('captcha', CaptchaType::class, [
                'type' => $config['options']['type'],
                'theme' => $config['options']['theme'],
                'size' => $config['options']['size'],
                'captcha_action' => $config['options']['action'] ?? null,
                'captcha_value' => $this->captchaService->getCaptcha($struct->getLocation()),
                'site_key' => $this->captchaService->getSiteKey($struct->getLocation()),
            ]);
        }

        $builder->setDataMapper($this);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['languageCode'] = $options['languageCode'];
        $view->vars['mainLanguageCode'] = $options['mainLanguageCode'];
        $view->vars['discriminator'] = $options['discriminator'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(['languageCode', 'mainLanguageCode', 'discriminator'])
            ->setDefault('translation_domain', 'ezplatform_content_forms_content')
            ->setDefault('csrf_protection', $this->configResolver->getParameter('form.use_csrf', 'netgen_information_collection'));
    }

    public function mapDataToForms($viewData, iterable $forms): void
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

    public function mapFormsToData(iterable $forms, &$viewData): void
    {

    }
}
