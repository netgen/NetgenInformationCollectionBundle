<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form;

use OutOfBoundsException;
use RuntimeException;
use function gettype;
use function is_callable;

final class FieldTypeHandlerRegistry
{
    /**
     * Map of registered callable or FieldTypeHandlerInterface objects.
     */
    private array $map;

    /**
     * Creates a service registry.
     *
     * In $map an array consisting of a mapping of FieldType identifiers to object / callable is expected.
     * In case of callable factory FieldTypeHandlerInterface should be returned on execution.
     */
    public function __construct(array $map = [])
    {
        $this->map = $map;
    }

    /**
     * Register a $service for FieldType $identifier.
     */
    public function register(string $identifier, FieldTypeHandlerInterface $handler): void
    {
        $this->map[$identifier] = $handler;
    }

    /**
     * Returns a FieldTypeHandlerInterface for FieldType $identifier.
     *
     * @throws \OutOfBoundsException
     * @throws \RuntimeException When type is not a FieldTypeHandlerInterface instance nor a callable factory
     */
    public function get(string $identifier): FieldTypeHandlerInterface|\Netgen\Bundle\IbexaFormsBundle\Form\FieldTypeHandlerInterface
    {
        if (!isset($this->map[$identifier])) {
            throw new OutOfBoundsException("No handler registered for FieldType '{$identifier}'.");
        }
        if (!$this->map[$identifier] instanceof FieldTypeHandlerInterface && !$this->map[$identifier] instanceof \Netgen\Bundle\IbexaFormsBundle\Form\FieldTypeHandlerInterface) {

            if (!is_callable($this->map[$identifier])) {
                throw new RuntimeException("FieldTypeHandler '{$identifier}' is not callable nor instance");
            }

            $factory = $this->map[$identifier];
            $this->map[$identifier] = $factory();

            if (!$this->map[$identifier] instanceof FieldTypeHandlerInterface && !$this->map[$identifier] instanceof \Netgen\Bundle\IbexaFormsBundle\Form\FieldTypeHandlerInterface) {
                throw new RuntimeException(
                    "FieldTypeHandler '{$identifier}' callable did not return a FieldTypeHandlerInterface instance, " .
                    'instead: ' . gettype($this->map[$identifier])
                );
            }
        }

        return $this->map[$identifier];
    }
}
