<?php

namespace Netgen\Bundle\InformationCollectionBundle\Exception;

use Exception;

class PropertyNotFoundException extends Exception
{
    /**
     * Generates: Property '{$propertyName}' not found.
     *
     * @param string $propertyName
     * @param string|null $className Optionally to specify class in abstract/parent classes
     * @param \Exception|null $previous
     */
    public function __construct($propertyName, $className = null, Exception $previous = null)
    {
        if ($className === null) {
            parent::__construct("Property '{$propertyName}' not found", 0, $previous);
        } else {
            parent::__construct("Property '{$propertyName}' not found on class '{$className}'", 0, $previous);
        }
    }
}
