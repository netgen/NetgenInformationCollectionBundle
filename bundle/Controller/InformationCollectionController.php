<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Netgen\Bundle\InformationCollectionBundle\InformationCollectionTrait;
use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;

class InformationCollectionController
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
