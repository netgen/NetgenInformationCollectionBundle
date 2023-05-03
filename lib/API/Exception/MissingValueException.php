<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Exception;

use RuntimeException;

class MissingValueException extends RuntimeException
{
    public function __construct(string $field)
    {
        $message = "There is no value for field {$field} specified.";

        parent::__construct($message);
    }
}
