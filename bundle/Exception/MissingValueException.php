<?php

namespace Netgen\Bundle\InformationCollectionBundle\Exception;

use RuntimeException;

class MissingValueException extends RuntimeException
{
    /**
     * MissingEmailValueException constructor.
     *
     * @param string $field
     */
    public function __construct($field)
    {
        $message = "There is no value for field {$field} specified.";

        parent::__construct($message);
    }
}
