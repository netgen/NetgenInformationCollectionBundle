<?php

namespace Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ActionsPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('netgen_information_collection.action.aggregate')) {
            return;
        }

        $actionAggregate = $container->getDefinition('netgen_information_collection.action.aggregate');

        foreach ($container->findTaggedServiceIds('netgen_information_collection.action') as $id => $attributes) {
            $actionAggregate->addMethodCall('addAction', [new Reference($id)]);
        }
    }
}