<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller;

use Netgen\Bundle\InformationCollectionBundle\InformationCollectionLegacyTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class InformationCollectionLegacyController implements ContainerAwareInterface
{
    use InformationCollectionLegacyTrait;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Displays and handles information collection
     *
     * @param Request $request
     * @param int $locationId
     * @param string $viewType
     * @param bool $layout
     * @param array $params
     *
     * @return mixed
     */
    public function displayAndHandle(Request $request, $locationId, $viewType, $layout = false, array $params = [])
    {
        $parameters = $this->collectInformation($request, $locationId);

        $params += $parameters;

        return $this->container
            ->get('ez_content')
            ->viewLocation(
                $locationId,
                $viewType,
                $layout,
                $params
            );
    }
}
