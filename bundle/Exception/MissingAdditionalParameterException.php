<?php

namespace Netgen\Bundle\InformationCollectionBundle\Exception;

use RuntimeException;

class MissingAdditionalParameterException extends RuntimeException
{
    /**
     * MissingAdditionalParameterException constructor.
     *
     * @param string $key
     */
    public function __construct($key)
    {
        $message = "There is no value for additional parameter {$key} specified.";

        parent::__construct($message);
    }
}
