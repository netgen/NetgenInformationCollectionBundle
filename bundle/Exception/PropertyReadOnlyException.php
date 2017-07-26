<?php

namespace Netgen\Bundle\InformationCollectionBundle\Exception;

use Exception;

class PropertyReadOnlyException extends Exception
{
    /**
     * Generates: Property '{$propertyName}' is readonly[ on class '{$className}'].
     *
     * @param string $propertyName
     * @param string $className
     * @param Exception|null $previous
     */
    public function __construct($propertyName, $className = null, Exception $previous = null)
    {
        parent::__construct("Property '{$propertyName}' is readonly on class '{$className}'", 0, $previous);
    }
}
