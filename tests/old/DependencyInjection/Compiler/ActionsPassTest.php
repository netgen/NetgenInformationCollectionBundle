<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler\ActionsPass;
use Netgen\Bundle\InformationCollectionBundle\Priority;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ActionsPassTest extends AbstractCompilerPassTestCase
{
    public function testCompilerPassCollectsValidServices(): void
    {
        $actionsRegistry = new Definition();
        $this->setDefinition('netgen_information_collection.action.registry', $actionsRegistry);

        $action = new Definition();
        $action->addTag('netgen_information_collection.action', array('alias' => 'custom_action', 'priority' => 100));
        $this->setDefinition('my_action', $action);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_information_collection.action.registry',
            'addAction',
            array(
                'custom_action',
                new Reference('my_action'),
                100,
            )
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\LogicException
     * @expectedExceptionMessage 'netgen_information_collection.action' service tag needs an 'alias' attribute to identify the action. None given.
     */
    public function testCompilerPassMustThrowExceptionIfActionServiceHasntGotAlias(): void
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
            array(
                new Reference('my_action'),
            )
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\LogicException
     * @expectedExceptionMessage Service my_action uses priority less than allowed. Priority must be greater than or equal to -255.
     */
    public function testCompilerWithServicePriorityLessThanAllowed(): void
    {
        $actionsRegistry = new Definition();
        $this->setDefinition('netgen_information_collection.action.registry', $actionsRegistry);
        $priority = Priority::MIN_PRIORITY - 1;

        $action = new Definition();
        $action->addTag('netgen_information_collection.action', array('alias' => 'custom_action', 'priority' => $priority));
        $this->setDefinition('my_action', $action);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_information_collection.action.registry',
            'addAction',
            array(
                'custom_action',
                new Reference('my_action'),
                $priority,
            )
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\LogicException
     * @expectedExceptionMessage Service my_action uses priority greater than allowed. Priority must be lower than or equal to 255.
     */
    public function testCompilerWithServicePriorityGreaterThanAllowed(): void
    {
        $actionsRegistry = new Definition();
        $this->setDefinition('netgen_information_collection.action.registry', $actionsRegistry);
        $priority = Priority::MAX_PRIORITY + 1;

        $action = new Definition();
        $action->addTag('netgen_information_collection.action', array('alias' => 'custom_action', 'priority' => $priority));
        $this->setDefinition('my_action', $action);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_information_collection.action.registry',
            'addAction',
            array(
                'custom_action',
                new Reference('my_action'),
                $priority,
            )
        );
    }

    public function testCompilerWithServiceThatIsMissingPriority(): void
    {
        $actionsRegistry = new Definition();
        $this->setDefinition('netgen_information_collection.action.registry', $actionsRegistry);

        $action = new Definition();
        $action->addTag('netgen_information_collection.action', array('alias' => 'custom_action'));
        $this->setDefinition('my_action', $action);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_information_collection.action.registry',
            'addAction',
            array(
                'custom_action',
                new Reference('my_action'),
                Priority::DEFAULT_PRIORITY,
            )
        );
    }

    public function testCompilerWithDatabasePriority(): void
    {
        $actionsRegistry = new Definition();
        $this->setDefinition('netgen_information_collection.action.registry', $actionsRegistry);

        $action = new Definition();
        $action->addTag('netgen_information_collection.action', array('alias' => 'database', 'priority' => 300));
        $this->setDefinition('my_action', $action);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_information_collection.action.registry',
            'addAction',
            array(
                'database',
                new Reference('my_action'),
                300,
            )
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ActionsPass());
    }
}
