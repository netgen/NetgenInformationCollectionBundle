<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Reference;

final class FieldTypeHandlerRegistryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('netgen_information_collection.form.fieldtype_handler_registry')) {
            return;
        }

        $registry = $container->getDefinition('netgen_information_collection.form.fieldtype_handler_registry');

        foreach ($container->findTaggedServiceIds('netgen.ibexa_forms.form.fieldtype_handler') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['alias'])) {
                    throw new LogicException(
                        "'netgen.ibexa_forms.form.fieldtype_handler' service tag " .
                        "needs an 'alias' attribute to identify the field type. None given."
                    );
                }

                $registry->addMethodCall(
                    'register',
                    [
                        $attribute['alias'],
                        new Reference($id),
                    ]
                );
            }
        }
    }
}
