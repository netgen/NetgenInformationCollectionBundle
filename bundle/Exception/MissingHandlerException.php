<?php

namespace Netgen\Bundle\InformationCollectionBundle\Exception;

use RuntimeException;

class MissingHandlerException extends RuntimeException
{
    /**
     * MissingHandlerException constructor.
     *
     * @param string $fieldType
     */
    public function __construct($fieldType)
    {
        $message = "There is no handler for {$fieldType} field type specified.";

        parent::__construct($message);
    }
}
