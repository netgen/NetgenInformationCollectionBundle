<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler\ActionsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ActionsPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ActionsPass());
    }

    public function testCompilerPassCollectsValidServices()
    {
        $actionsRegistry = new Definition();
        $this->setDefinition('netgen_information_collection.action.registry', $actionsRegistry);

        $action = new Definition();
        $action->addTag('netgen_information_collection.action', ['alias' => 'custom_action']);
        $this->setDefinition('my_action', $action);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_information_collection.action.registry',
            'addAction',
            [
                'custom_action',
                new Reference('my_action'),
            ]
        );
    }
    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\LogicException
     * @expectedExceptionMessage 'netgen_information_collection.action' service tag needs an 'alias' attribute to identify the action. None given.
     */
    public function testCompilerPassMustThrowExceptionIfActionServiceHasntGotAlias()
    {
        $actionsRegistry = new Definition();
        $this->setDefinition('netgen_information_collection.action.registry', $actionsRegistry);

        $action = new Definition();
        $action->addTag('netgen_information_collection.action');
        $this->setDefinition('my_action', $action);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_information_collection.action.registry',
            'addAction',
            [
                new Reference('my_action'),
            ]
        );
    }
}
