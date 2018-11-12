<?php

namespace Netgen\Bundle\InformationCollectionBundle\FieldHandler;

use eZ\Publish\Core\FieldType\Value;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomFieldHandlerInterface;

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
    public function __construct(array $handlers = array())
    {
        $this->handlers = $handlers;
    }

    /**
     * Adds new handler.
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
     * @return CustomFieldHandlerInterface|null
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
