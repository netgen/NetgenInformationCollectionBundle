<?php

namespace Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LegacyFieldHandlersPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('netgen_information_collection.field_handler.legacy.registry')) {
            return;
        }

        $actionAggregate = $container->getDefinition('netgen_information_collection.field_handler.legacy.registry');

        foreach ($container->findTaggedServiceIds('netgen_information_collection.field_handler.legacy') as $id => $attributes) {
            $actionAggregate->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}