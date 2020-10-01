<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller;

use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;
use Netgen\InformationCollection\API\InformationCollectionTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class CollectInformation implements ContainerAwareInterface
{
    use InformationCollectionTrait;
    use ContainerAwareTrait;

    /**
     * Displays and handles information collection.
     *
     * @param ContentValueView $view
     *
     * @return ContentValueView
     */
    public function __invoke(ContentValueView $view)
    {
        return $this->collectInformation($view, []);
    }
}
