<?php

namespace Netgen\Bundle\InformationCollectionBundle\FieldHandler;

use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomFieldHandlerInterface;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\Value;

class FieldHandlerRegistry
{
    /**
     * @var array
     */
    protected $handlers;

    /**
     * FieldHandlerRegistry constructor.
     *
     * @param array $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
     * Adds new handler
     *
     * @param CustomFieldHandlerInterface $handler
     */
    public function addHandler(CustomFieldHandlerInterface $handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * @param Value $value
     *
     * @return string
     */
    public function handle(Value $value)
    {
        /** @var CustomFieldHandlerInterface $handler */
        foreach ($this->handlers as $handler) {
            if ($handler->supports($value)) {
                return $handler;
            }
        }

        return null;
    }
}