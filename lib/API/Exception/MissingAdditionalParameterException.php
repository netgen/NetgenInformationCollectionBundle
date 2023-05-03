<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Exception;

use RuntimeException;

class MissingAdditionalParameterException extends RuntimeException
{
    public function __construct(string $key)
    {
        $message = "There is no value for parameter {$key} specified.";

        parent::__construct($message);
    }
}
