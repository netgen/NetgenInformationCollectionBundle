<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Pagination;

use Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollectionService;
use Pagerfanta\Adapter\AdapterInterface;

class InformationCollectionContentsAdapter implements AdapterInterface
{
    /**
     * @var InformationCollectionService
     */
    protected $informationCollectionService;

    public function __construct(InformationCollectionService $informationCollectionService)
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
