<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\Payload;

class InformationCollectionStruct
{
    /**
     * @var mixed[] An array of field values like[$fieldDefIdentifier]
     */
    protected $collectedData;

    /**
     * Returns value for $fieldDefIdentifier.
     *
     * @param $fieldDefIdentifier
     *
     * @return mixed
     */
    public function getCollectedFieldValue($fieldDefIdentifier)
    {
        if (isset($this->collectedData[$fieldDefIdentifier])) {
            return $this->collectedData[$fieldDefIdentifier];
        }

        return null;
    }

    /**
     * This method returns the complete fields collection.
     *
     * @return array
     */
    public function getCollectedFields()
    {
        return $this->collectedData;
    }

    /**
     * Sets value for $fieldDefIdentifier.
     *
     * @param string $fieldDefIdentifier
     * @param mixed $value
     *
     * @return mixed
     */
    public function setCollectedFieldValue($fieldDefIdentifier, $value)
    {
        $this->collectedData[$fieldDefIdentifier] = $value;
    }
}
