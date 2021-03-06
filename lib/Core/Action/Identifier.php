<?php

namespace Netgen\InformationCollection\Core\Action;

use Netgen\InformationCollection\API\Action\ActionInterface;
use ReflectionClass;
use ReflectionProperty;

final class Identifier
{
    /**
     * @var string
     */
    private $classConstant;

    public function __construct(ActionInterface $action)
    {
        $this->classConstant = get_class($action);
    }

    public function getPrimary(): string
    {
        $class = new ReflectionClass($this->classConstant);
        $propertyValue = '';

        if ($class->hasProperty('defaultName')) {
            $defaultName = new ReflectionProperty($this->classConstant, 'defaultName');
            $propertyValue = $defaultName->getValue();
        }

        if (!empty($propertyValue)) {
            return $propertyValue;
        }

        return $this->getSecondary();
    }

    public function getSecondary(): string
    {
        return $this->classConstant;
    }
}
