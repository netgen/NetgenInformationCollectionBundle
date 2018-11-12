<?php

namespace Netgen\InformationCollection\API\Value;

use eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException;
use eZ\Publish\API\Repository\Exceptions\PropertyReadOnlyException;

abstract class ValueObject
{
    /**
     * Construct object optionally with a set of properties.
     *
     * Readonly properties values must be set using $properties as they are not writable anymore
     * after object has been created.
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
     * Magic set function handling writes to non public properties.
     *
     * @ignore This method is for internal use
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException When property does not exist
     * @throws \eZ\Publish\API\Repository\Exceptions\PropertyReadOnlyException When property is readonly (protected)
     *
     * @param string $property Name of the property
     * @param string $value
     */
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            throw new PropertyReadOnlyException($property, get_class($this));
        }
        throw new PropertyNotFoundException($property, get_class($this));
    }

    /**
     * Magic get function handling read to non public properties.
     *
     * Returns value for all readonly (protected) properties.
     *
     * @ignore This method is for internal use
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException exception on all reads to undefined properties so typos are not silently accepted.
     *
     * @param string $property Name of the property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        throw new PropertyNotFoundException($property, get_class($this));
    }

    /**
     * Magic isset function handling isset() to non public properties.
     *
     * Returns true for all (public/)protected/private properties.
     *
     * @ignore This method is for internal use
     *
     * @param string $property Name of the property
     *
     * @return bool
     */
    public function __isset($property)
    {
        return property_exists($this, $property);
    }

    /**
     * Magic unset function handling unset() to non public properties.
     *
     * @ignore This method is for internal use
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException exception on all writes to undefined properties so typos are not silently accepted and
     * @throws \eZ\Publish\API\Repository\Exceptions\PropertyReadOnlyException exception on readonly (protected) properties.
     *
     * @uses ::__set()
     *
     * @param string $property Name of the property
     *
     * @return bool
     */
    public function __unset($property)
    {
        $this->__set($property, null);
    }
}