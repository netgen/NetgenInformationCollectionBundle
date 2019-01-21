<?php

namespace Netgen\Bundle\InformationCollectionBundle;

use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Form\Captcha\CaptchaValueInterface;
use Symfony\Component\HttpFoundation\Request;

trait InformationCollectionLegacyTrait
{
    /**
     * Builds Form, checks if Form is valid and dispatches InformationCollected event.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $locationId
     *
     * @return array
     */
    protected function collectInformation(Request $request, $locationId)
    {
        $isValid = false;

        $location = $this->container
            ->get('ezpublish.api.service.location')
            ->loadLocation($locationId);

        /** @var \Netgen\Bundle\InformationCollectionBundle\Form\Builder\FormBuilder $formBuilder */
        $formBuilder = $this->container
            ->get('netgen_information_collection.form.builder');

        $form = $formBuilder->createFormForLocation($location)
            ->getForm();

        /** @var CaptchaValueInterface $captcha */
        $captcha = $this->container
            ->get('netgen_information_collection.factory.captcha')
            ->getCaptcha($location);

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $isValid = true;

            $event = new InformationCollected($form->getData());

            /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher */
            $dispatcher = $this->container
                ->get('event_dispatcher');

            $dispatcher->dispatch(Events::INFORMATION_COLLECTED, $event);
        }

        return array(
            'is_valid' => $isValid,
            'form' => $form->createView(),
            'collected_fields' => $form->getData()->payload->getCollectedFields(),
        );
    }
}
