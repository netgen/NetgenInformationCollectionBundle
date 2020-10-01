<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API;

use eZ\Publish\Core\MVC\Symfony\View\BaseView;
use eZ\Publish\Core\MVC\Symfony\View\CachableView;
use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;
use eZ\Publish\Core\MVC\Symfony\View\LocationValueView;
use Netgen\InformationCollection\Handler;
use Symfony\Component\HttpFoundation\RequestStack;

trait InformationCollectionTrait
{
    /**
     * Builds Form, checks if Form is valid and dispatches InformationCollected event.
     *
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentValueView $view
     * @param array $options
     *
     * @return array
     */
    protected function collectInformation(ContentValueView $view, array $options): ContentValueView
    {
        $isValid = false;

        if (!$view instanceof LocationValueView) {
            throw new \BadMethodCallException('eZ view needs to implement LocationValueView interface');
        }

        /** @var Handler $handler */
        $handler = $this->container->get('netgen_information_collection.handler');
        /** @var RequestStack $requestStack */
        $requestStack = $this->container->get('request_stack');

        $form = $handler->getForm($view->getContent(), $view->getLocation());
        $request = $requestStack->getCurrentRequest();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isValid = true;

            $handler->handle($form->getData(), $options);
        }

        if ($view instanceof BaseView) {
            $view->addParameters([
                'is_valid' => $isValid,
                'form' => $form->createView(),
                'collected_fields' => $form->getData()->getFieldsData(),
            ]);
        }

        if ($view instanceof CachableView) {
            $view->setCacheEnabled(false);
        }

        return $view;
    }
}
