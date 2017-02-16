<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller;

use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;
use Netgen\Bundle\InformationCollectionBundle\InformationCollectionTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\Request;

class InformationCollectionController implements ContainerAwareInterface
{
    use InformationCollectionTrait;

    /**
     * Displays and handles information collection
     *
     * @param ContentValueView $view
     * @param Request $request
     *
     * @return ContentValueView
     */
    public function displayAndHandle(ContentValueView $view, Request $request)
    {
        $parameters = $this->collectInformation($view, $request);

        $view->addParameters($parameters);
        return $view;
    }
}
