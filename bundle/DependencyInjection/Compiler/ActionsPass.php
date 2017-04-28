<?php

namespace Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler;

use Netgen\Bundle\InformationCollectionBundle\Priority;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Reference;

class ActionsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('netgen_information_collection.action.registry')) {
            return;
        }

        $actionAggregate = $container->getDefinition('netgen_information_collection.action.registry');

        foreach ($container->findTaggedServiceIds('netgen_information_collection.action') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['alias'])) {
                    throw new LogicException(
                        "'netgen_information_collection.action' service tag " .
                        "needs an 'alias' attribute to identify the action. None given."
                    );
                }

                $priority = isset($attribute['priority']) ? $attribute['priority'] : Priority::DEFAULT_PRIORITY;

                if ($priority > Priority::MAX_PRIORITY && $attribute['alias'] !== 'database') {
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
                    array(
                        $attribute['alias'],
                        new Reference($id),
                        $priority,
                    )
                );
            }
        }
    }
}
