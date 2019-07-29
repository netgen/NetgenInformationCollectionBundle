<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Pagination;

use Netgen\InformationCollection\API\Service\InformationCollection;
use Pagerfanta\Adapter\AdapterInterface;

abstract class BaseAdapter implements AdapterInterface
{
    /**
     * @var int
     */
    protected $nbResults;

    /**
     * @var \Netgen\InformationCollection\API\Service\InformationCollection
     */
    protected $informationCollectionService;

    /**
     * InformationCollectionCollectionListAdapter constructor.
     *
     * @param \Netgen\InformationCollection\API\Service\InformationCollection $informationCollectionService
     */
    public function __construct(InformationCollection $informationCollectionService)
    {
        $this->informationCollectionService = $informationCollectionService;
    }
}
