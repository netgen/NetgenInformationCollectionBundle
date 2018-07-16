<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Pagination;

use Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection;
use Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Query;
use Pagerfanta\Adapter\AdapterInterface;

abstract class BaseAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection
     */
    protected $informationCollectionService;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Query
     */
    protected $query;

    /**
     * InformationCollectionCollectionListAdapter constructor.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection $informationCollectionService
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Query $query
     */
    public function __construct(InformationCollection $informationCollectionService, Query $query)
    {
        $this->informationCollectionService = $informationCollectionService;
        $this->query = $query;
    }

    /**
     * @param int $offset
     * @param int $length
     *
     * @return \Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Query
     */
    protected function getQuery($offset, $length)
    {
        $query = clone $this->query;
        $query->limit = $length;
        $query->offset = $offset;

        return $query;
    }

    /**
     * @return \Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Query
     */
    protected function getCountQuery()
    {
        $query = clone $this->query;
        $query->limit = Query::COUNT_QUERY;

        return $query;
    }
}
