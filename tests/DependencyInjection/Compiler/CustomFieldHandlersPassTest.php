<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler\CustomFieldHandlersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CustomFieldHandlersPassTest extends AbstractCompilerPassTestCase
{
    public function testCompilerPassCollectsValidServices()
    {
        $registry = new Definition();
        $this->setDefinition(CustomFieldHandlersPass::FIELD_HANDLER_REGISTRY, $registry);

        $fieldHandler = new Definition();
        $fieldHandler->addTag(CustomFieldHandlersPass::FIELD_HANDLER);
        $this->setDefinition('custom_handler', $fieldHandler);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            CustomFieldHandlersPass::FIELD_HANDLER_REGISTRY,
            'addHandler',
            array(
                new Reference('custom_handler'),
            )
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CustomFieldHandlersPass());
    }
}
