<?php

namespace Netgen\Bundle\InformationCollectionBundle\Exception;

use Exception;

class PropertyNotFoundException extends Exception
{
    /**
     * Generates: Property '{$propertyName}' not found.
     *
     * @param string $propertyName
     * @param string $className
     * @param Exception|null $previous
     */
    public function __construct($propertyName, $className, Exception $previous = null)
    {
        parent::__construct("Property '{$propertyName}' not found on class '{$className}'", 0, $previous);
    }
}
