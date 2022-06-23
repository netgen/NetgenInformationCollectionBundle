<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Exception;

use RuntimeException;

class ActionFailedException extends RuntimeException
{
    public function __construct(string $action, string $reason)
    {
        $message = "InformationCollection action {$action} failed with reason {$reason}";

        parent::__construct($message);
    }
}
