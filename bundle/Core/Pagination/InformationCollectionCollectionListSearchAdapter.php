<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Pagination;

use Pagerfanta\Adapter\AdapterInterface;
use Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection;

class InformationCollectionCollectionListSearchAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection
     */
    protected $informationCollectionService;

    /**
     * @var int
     */
    protected $contentId;

    /**
     * @var string
     */
    protected $searchText;

    /**
     * InformationCollectionCollectionListSearchAdapter constructor.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection $informationCollectionService
     * @param int $contentId
     * @param string $searchText
     */
    public function __construct(InformationCollection $informationCollectionService, $contentId, $searchText)
    {
        $this->informationCollectionService = $informationCollectionService;
        $this->contentId = $contentId;
        $this->searchText = $searchText;
    }

    public function getNbResults()
    {
        if (!isset($this->nbResults)) {
            $this->nbResults = $this->informationCollectionService->searchCount($this->contentId, $this->searchText);
        }

        return $this->nbResults;
    }

    public function getSlice($offset, $length)
    {
        $objects = $this->informationCollectionService
            ->search($this->contentId, $this->searchText, $length, $offset);

        if (!isset($this->nbResults)) {
            $this->nbResults = $this->informationCollectionService->searchCount($this->contentId, $this->searchText);
        }

        return $objects;
    }
}
