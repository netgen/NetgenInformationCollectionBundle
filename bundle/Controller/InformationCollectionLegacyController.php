<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller;

use Netgen\Bundle\InformationCollectionBundle\InformationCollectionLegacyTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class InformationCollectionLegacyController implements ContainerAwareInterface
{
    use InformationCollectionLegacyTrait;
    use ContainerAwareTrait;

    /**
     * Displays and handles information collection.
     *
     * @param Request $request
     * @param int $locationId
     * @param string $viewType
     * @param bool $layout
     * @param array $params
     *
     * @return mixed
     */
    public function displayAndHandle(Request $request, $locationId, $viewType, $layout = false, array $params = array())
    {
        $parameters = $this->collectInformation($request, $locationId);

        $params += $parameters;

        $response = $this->container
            ->get('ez_content')
            ->viewLocation(
                $locationId,
                $viewType,
                $layout,
                $params
            );

        $response->setPrivate();

        return $response;
    }
}
