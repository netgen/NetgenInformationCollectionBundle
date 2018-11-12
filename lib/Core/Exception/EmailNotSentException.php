<?php

namespace Netgen\Bundle\InformationCollectionBundle\Exception;

use RuntimeException;

class EmailNotSentException extends RuntimeException
{
    /**
     * EmailNotSentException constructor.
     *
     * @param string $what
     * @param string $why
     */
    public function __construct($what, $why)
    {
        $message = "Error occurred while trying to send email: {$what} failed with error {$why}";

        parent::__construct($message);
    }
}
