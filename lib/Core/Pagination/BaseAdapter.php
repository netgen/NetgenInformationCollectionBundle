<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Pagination;

use Netgen\InformationCollection\API\Service\InformationCollection;
use Pagerfanta\Adapter\AdapterInterface;

abstract class BaseAdapter implements AdapterInterface
{
    protected int $nbResults;

    protected InformationCollection $informationCollectionService;

    public function __construct(InformationCollection $informationCollectionService)
    {
        $this->informationCollectionService = $informationCollectionService;
    }
}
