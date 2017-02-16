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
        $action->addTag('netgen_information_collection.action', ['alias' => 'custom_action', 'priority' => 100]);
        $this->setDefinition('my_action', $action);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_information_collection.action.registry',
            'addAction',
            [
                'custom_action',
                new Reference('my_action'),
                100
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

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\LogicException
     * @expectedExceptionMessage Service my_action uses priority less than 1. Priority must be positive integer.
     */
    public function testCompilerWithServicePriorityLessThanOne()
    {
        $actionsRegistry = new Definition();
        $this->setDefinition('netgen_information_collection.action.registry', $actionsRegistry);

        $action = new Definition();
        $action->addTag('netgen_information_collection.action', ['alias' => 'custom_action', 'priority' => -1]);
        $this->setDefinition('my_action', $action);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_information_collection.action.registry',
            'addAction',
            [
                'custom_action',
                new Reference('my_action'),
                -1
            ]
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\LogicException
     * @expectedExceptionMessage Service my_action uses top priority. Only database can use priority 1, please lower down priority for given service.
     */
    public function testCompilerWithServicePriorityEqualsOne()
    {
        $actionsRegistry = new Definition();
        $this->setDefinition('netgen_information_collection.action.registry', $actionsRegistry);

        $action = new Definition();
        $action->addTag('netgen_information_collection.action', ['alias' => 'custom_action', 'priority' => 1]);
        $this->setDefinition('my_action', $action);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_information_collection.action.registry',
            'addAction',
            [
                'custom_action',
                new Reference('my_action'),
                1
            ]
        );
    }

    public function testCompilerWithServiceThatIsMissingPriority()
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
                100
            ]
        );
    }
}
