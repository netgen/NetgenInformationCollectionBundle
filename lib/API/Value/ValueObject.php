<?php

declare(strict_types=1);

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
    public function __construct(array $properties = [])
    {
        foreach ($properties as $property => $value) {
            $this->{$property} = $value;
        }
    }

    /**
     * Magic set function handling writes to non public properties.
     *
     * @ignore This method is for internal use
     *
     * @param string $property Name of the property
     * @param string $value
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException When property does not exist
     * @throws \eZ\Publish\API\Repository\Exceptions\PropertyReadOnlyException When property is readonly (protected)
     */
    public function __set(string $property, string $value): void
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
     * @param string $property Name of the property
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException exception on all reads to undefined properties so typos are not silently accepted
     *
     * @return mixed
     */
    public function __get(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->{$property};
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
    public function __isset(string $property): bool
    {
        return property_exists($this, $property);
    }

    /**
     * Magic unset function handling unset() to non public properties.
     *
     * @ignore This method is for internal use
     *
     * @uses ::__set()
     *
     * @param string $property Name of the property
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException exception on all writes to undefined properties so typos are not silently accepted and
     * @throws \eZ\Publish\API\Repository\Exceptions\PropertyReadOnlyException exception on readonly (protected) properties
     *
     * @return bool
     */
    public function __unset(string $property): void
    {
        $this->__set($property, null);
    }
}
