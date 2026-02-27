<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaptchaType extends AbstractType
{

    public function __construct()
    {
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['type'] = $options['type'];
        $view->vars['theme'] = $options['theme'];
        $view->vars['size'] = $options['size'];
        $view->vars['site_key'] = $options['site_key'];
        $view->vars['captcha_action'] = $options['captcha_action'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'type' => 'checkbox',
                'theme' => 'light',
                'size' => 'normal',
                'captcha_action' => null,
                'site_key' => null,
                'captcha_value' => null,
            ]
        );
        $resolver
//            ->setAllowedTypes('captcha_value', CaptchaValue::class)
            ->setDefault('type', 'invisible')
            ->setAllowedValues('type', ['checkbox', 'invisible'])
            ->setAllowedValues('theme', ['light', 'dark'])
            ->setAllowedValues('size', ['compact', 'normal'])
            ->setRequired(['captcha_value', 'site_key', 'theme', 'type', 'size']);
    }
}
