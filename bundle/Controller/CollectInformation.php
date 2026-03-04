<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Controller;

use Ibexa\Core\MVC\Symfony\View\ContentValueView;
use Netgen\InformationCollection\API\InformationCollectionTrait;
use Netgen\InformationCollection\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class CollectInformation extends AbstractController
{
    use InformationCollectionTrait;

    /**
     * Displays and handles information collection.
     */
    public function __invoke(ContentValueView $view): ContentValueView
    {
        return $this->collectInformation($view, []);
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                'netgen_information_collection.handler' => Handler::class,
                'request_stack' => RequestStack::class,
            ]
        );
    }
}
