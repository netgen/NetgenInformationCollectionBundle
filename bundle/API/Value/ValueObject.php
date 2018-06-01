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

    /**
     * Returns a new instance of this class with the data specified by $array.
     *
     * $array contains all the data members of this class in the form:
     * array('member_name'=>value).
     *
     * __set_state makes this class exportable with var_export.
     * var_export() generates code, that calls this method when it
     * is parsed with PHP.
     *
     * @ignore This method is for internal use
     *
     * @param mixed[] $array
     *
     * @return ValueObject
     */
    public static function __set_state(array $array)
    {
        return new static($array);
    }
}
