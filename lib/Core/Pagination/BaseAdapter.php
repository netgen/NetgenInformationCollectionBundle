<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Pagination;

use Netgen\InformationCollection\API\Service\InformationCollection;
use Netgen\InformationCollection\API\Value\Filter\Query;
use Pagerfanta\Adapter\AdapterInterface;

abstract class BaseAdapter implements AdapterInterface
{
    /**
     * @var \Netgen\InformationCollection\API\Service\InformationCollection
     */
    protected $informationCollectionService;

    /**
     * @var \Netgen\InformationCollection\API\Value\Filter\Query
     */
    protected $query;

    /**
     * InformationCollectionCollectionListAdapter constructor.
     *
     * @param \Netgen\InformationCollection\API\Service\InformationCollection $informationCollectionService
     * @param \Netgen\InformationCollection\API\Value\Filter\Query $query
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
     * @return \Netgen\InformationCollection\API\Value\Filter\Query
     */
    protected function getQuery($offset, $length)
    {
        $query = clone $this->query;
        $query->limit = $length;
        $query->offset = $offset;

        return $query;
    }

    /**
     * @return \Netgen\InformationCollection\API\Value\Filter\Query
     */
    protected function getCountQuery()
    {
        $query = clone $this->query;
        $query->limit = Query::COUNT_QUERY;

        return $query;
    }
}
