<?php

namespace Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Exception\LogicException;

class ActionsPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
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

                $actionAggregate->addMethodCall(
                    'addAction',
                    [
                        $attribute['alias'],
                        new Reference($id),
                    ]
                );
            }
        }
    }
}
