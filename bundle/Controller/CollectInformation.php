<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller;

use eZ\Publish\Core\MVC\Symfony\View\BaseView;
use eZ\Publish\Core\MVC\Symfony\View\CachableView;
use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;
use Netgen\InformationCollection\API\InformationCollectionTrait;
use Netgen\InformationCollection\API\Value\DataTransfer\AdditionalContent;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class CollectInformation implements ContainerAwareInterface
{
    use InformationCollectionTrait;
    use ContainerAwareTrait;

    /**
     * Displays and handles information collection.
     *
     * @param ContentValueView $view
     * @param Request $request
     *
     * @return ContentValueView
     */
    public function __invoke(ContentValueView $view, Request $request)
    {
        $parameters = $this->collectInformation($view, $request, new AdditionalContent());

        if ($view instanceof BaseView) {
            $view->addParameters($parameters);
        }

        if ($view instanceof CachableView) {
            $view->setCacheEnabled(false);
        }

        return $view;
    }
}
