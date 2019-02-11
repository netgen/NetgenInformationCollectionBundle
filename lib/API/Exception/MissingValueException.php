<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Exception;

use RuntimeException;

class MissingValueException extends RuntimeException
{
    /**
     * MissingEmailValueException constructor.
     *
     * @param string $field
     */
    public function __construct(string $field)
    {
        $message = "There is no value for field {$field} specified.";

        parent::__construct($message);
    }
}
