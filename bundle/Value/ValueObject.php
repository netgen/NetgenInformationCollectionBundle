<?php

namespace Netgen\Bundle\InformationCollectionBundle\Value;

use Netgen\Bundle\InformationCollectionBundle\Exception\PropertyNotFoundException;
use Netgen\Bundle\InformationCollectionBundle\Exception\PropertyReadOnlyException;

abstract class ValueObject
{
    /**
     * ValueObject constructor.
     *
     * @param array $properties
     */
    public function __construct(array $properties = array())
    {
        foreach ($properties as $property => $value) {
            $this->$property = $value;
        }
    }

    /**
     * Disables magic set
     *
     * @param string $property
     * @param mixed $value
     *
     * @throws PropertyNotFoundException
     * @throws PropertyReadOnlyException
     */
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            throw new PropertyReadOnlyException($property, get_class($this));
        }
        throw new PropertyNotFoundException($property, get_class($this));
    }

    /**
     * Magic get
     *
     * @param string $property
     *
     * @return mixed
     *
     * @throws PropertyNotFoundException
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        throw new PropertyNotFoundException($property, get_class($this));
    }
}
