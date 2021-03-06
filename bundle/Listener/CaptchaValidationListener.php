<?php


namespace Netgen\Bundle\InformationCollectionBundle\Listener;

use Netgen\InformationCollection\API\Value\InformationCollectionStruct;
use Netgen\InformationCollection\Core\Service\CaptchaService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class CaptchaValidationListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Netgen\InformationCollection\Core\Service\CaptchaService
     */
    private $captchaService;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    private $translator;

    public function __construct(RequestStack $requestStack, CaptchaService $captchaService, TranslatorInterface $translator)
    {
        $this->requestStack = $requestStack;
        $this->captchaService = $captchaService;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    public function onPostSubmit(FormEvent $event)
    {
        $captchaValue = $event->getForm()
            ->getConfig()
            ->getOption('captcha_value');

//        $request = $this->requestStack->getCurrentRequest();
        $request = Request::createFromGlobals();

        $text = 'The captcha is invalid. Please try again.';

        if (!$captchaValue->isValid($request)) {
            $error = new FormError(
                $this->translator->trans('netgen_information_collection.captcha')
            );

            $event->getForm()->addError($error);
        }
    }
}
