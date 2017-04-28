<?php

namespace Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CustomFieldHandlersPass implements CompilerPassInterface
{
    const FIELD_HANDLER_REGISTRY = 'netgen_information_collection.field_handler.registry';
    const FIELD_HANDLER = 'netgen_information_collection.field_handler.custom';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::FIELD_HANDLER_REGISTRY)) {
            return;
        }

        $actionAggregate = $container->getDefinition(self::FIELD_HANDLER_REGISTRY);

        foreach ($container->findTaggedServiceIds(self::FIELD_HANDLER) as $id => $attributes) {
            $actionAggregate->addMethodCall(
                'addHandler',
                array(
                    new Reference($id),
                )
            );
        }
    }
}
