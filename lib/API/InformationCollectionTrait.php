<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API;

use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;
use eZ\Publish\Core\MVC\Symfony\View\LocationValueView;
use Netgen\InformationCollection\API\Form\DynamicFormBuilderInterface;
use Netgen\InformationCollection\API\Value\DataTransfer\AdditionalContent;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Symfony\Component\HttpFoundation\Request;

trait InformationCollectionTrait
{
    /**
     * Builds Form, checks if Form is valid and dispatches InformationCollected event.
     *
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentValueView $view
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Netgen\InformationCollection\API\Value\DataTransfer\AdditionalContent $additionalContent
     *
     * @return array
     */
    protected function collectInformation(ContentValueView $view, Request $request, AdditionalContent $additionalContent): array
    {
        $isValid = false;

        if (!$view instanceof LocationValueView) {
            throw new \BadMethodCallException('eZ view needs to implement LocationValueView interface');
        }

        /** @var DynamicFormBuilderInterface $formBuilder */
        $formBuilder = $this->container
            ->get('netgen_information_collection.form.builder');

        $form = $formBuilder->createForm($view->getContent());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isValid = true;

            $event = new InformationCollected(
                $form->getData(),
                $view->getLocation(),
                $additionalContent
            );

            /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher */
            $dispatcher = $this->container
                ->get('event_dispatcher');

            $dispatcher->dispatch(Events::INFORMATION_COLLECTED, $event);
        }

        return [
            'is_valid' => $isValid,
            'form' => $form->createView(),
            'collected_fields' => $form->getData()->getFieldsData(),
        ];
    }
}
