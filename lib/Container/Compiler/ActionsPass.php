<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Container\Compiler;

use Netgen\InformationCollection\API\Priority;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Reference;

class ActionsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('netgen_information_collection.action.registry')) {
            return;
        }

        $actionAggregate = $container->getDefinition('netgen_information_collection.action.registry');

        foreach ($container->findTaggedServiceIds('netgen_information_collection.action') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['action'])) {
                    throw new LogicException(
                        "'netgen_information_collection.action' service tag " .
                        "needs an 'action' attribute to identify the action. None given."
                    );
                }

                $priority = $attribute['priority'] ?? Priority::DEFAULT_PRIORITY;

                if ($priority > Priority::MAX_PRIORITY && $attribute['action'] !== 'database') {
                    throw new LogicException(
                        "Service {$id} uses priority greater than allowed. " .
                        'Priority must be lower than or equal to ' . Priority::MAX_PRIORITY . '.'
                    );
                }

                if ($priority < Priority::MIN_PRIORITY) {
                    throw new LogicException(
                        "Service {$id} uses priority less than allowed. " .
                        'Priority must be greater than or equal to ' . Priority::MIN_PRIORITY . '.'
                    );
                }

                $actionAggregate->addMethodCall(
                    'addAction',
                    [
                        $attribute['action'],
                        new Reference($id),
                        $priority,
                    ]
                );
            }
        }
    }
}
