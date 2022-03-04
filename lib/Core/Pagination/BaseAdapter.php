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

    public function __construct(InformationCollection $informationCollectionService)
    {
        $this->informationCollectionService = $informationCollectionService;
    }
}
