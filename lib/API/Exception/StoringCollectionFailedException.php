<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Exception;

use RuntimeException;

class StoringCollectionFailedException extends RuntimeException
{
    public function __construct(string $action, string $reason)
    {
        $message = "Persisting of {$action} failed with reason {$reason}";

        parent::__construct($message);
    }
}
