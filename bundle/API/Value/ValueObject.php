<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Value;

abstract class ValueObject
{
    /**
     * Construct object optionally with a set of properties.
     *
     * @param array $properties
     */
    public function __construct(array $properties = array())
    {
        foreach ($properties as $property => $value) {
            $this->$property = $value;
        }
    }
}
