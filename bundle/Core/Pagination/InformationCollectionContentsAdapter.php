<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Pagination;

use Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection;
use Pagerfanta\Adapter\AdapterInterface;

class InformationCollectionContentsAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection
     */
    protected $informationCollectionService;

    /**
     * InformationCollectionContentsAdapter constructor.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection $informationCollectionService
     */
    public function __construct(InformationCollection $informationCollectionService)
    {
        $this->informationCollectionService = $informationCollectionService;
    }

    public function getNbResults()
    {
        if (!isset($this->nbResults)) {
            $this->nbResults = $this->informationCollectionService->overviewCount();
        }

        return $this->nbResults;
    }

    public function getSlice($offset, $length)
    {
        $objects = $this->informationCollectionService
            ->overview($length, $offset);

        if (!isset($this->nbResults)) {
            $this->nbResults = $this->informationCollectionService->overviewCount();
        }

        return $objects;
    }
}
