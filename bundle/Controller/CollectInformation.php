<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller;

use Ibexa\Core\MVC\Symfony\View\ContentValueView;
use Netgen\InformationCollection\API\InformationCollectionTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class CollectInformation implements ContainerAwareInterface
{
    use InformationCollectionTrait;
    use ContainerAwareTrait;

    /**
     * Displays and handles information collection.
     */
    public function __invoke(ContentValueView $view): ContentValueView
    {
        return $this->collectInformation($view, []);
    }
}
