<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler;

use Ibexa\Core\FieldType\Value;
use Netgen\InformationCollection\API\FieldHandler\CustomFieldHandlerInterface;

final class FieldHandlerRegistry
{
    private iterable $handlers;

    public function __construct(iterable $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * Adds new handler.
     */
    public function addHandler(CustomFieldHandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
    }

    public function handle(Value $value): ?CustomFieldHandlerInterface
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
