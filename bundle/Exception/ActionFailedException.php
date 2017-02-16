<?php

namespace Netgen\Bundle\InformationCollectionBundle\Exception;

use RuntimeException;

class ActionFailedException extends RuntimeException
{
    /**
     * ActionFailedException constructor.
     *
     * @param string $action
     * @param string $reason
     */
    public function __construct($action, $reason)
    {
        $message = "InformationCollection action {$action} failed with reason {$reason}";

        parent::__construct($message);
    }
}
