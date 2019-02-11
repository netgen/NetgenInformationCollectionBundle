<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Exception;

use RuntimeException;

class EmailNotSentException extends RuntimeException
{
    /**
     * EmailNotSentException constructor.
     *
     * @param string $what
     * @param string $why
     */
    public function __construct(string $what, string $why)
    {
        $message = "Error occurred while trying to send email: {$what} failed with error {$why}";

        parent::__construct($message);
    }
}
