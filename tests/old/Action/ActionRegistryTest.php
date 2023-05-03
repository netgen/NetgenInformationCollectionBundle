<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Action;

use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\IbexaFormsBundle\Form\DataWrapper;
use Netgen\Bundle\InformationCollectionBundle\Action\ActionInterface;
use Netgen\Bundle\InformationCollectionBundle\Action\ActionRegistry;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionObject;

class ActionRegistryTest extends TestCase
{
    protected ActionRegistry $registry;

    protected ActionRegistry $registryForPriority;

    protected ActionRegistry $registryWithEmptyConf;

    protected ActionRegistry $registryWithOnlyDefaultConf;

    protected array $config;

    protected array $config2;

    protected array $onlyDefaultConfig;

    protected array $emptyConfig;

    protected MockObject $action1;

    protected MockObject $action2;

    protected MockObject $action3;

    protected MockObject $action4;

    protected MockObject $logger;

    protected InformationCollected $event;

    protected InformationCollected $event2;

    public function setUp(): void
    {
        $this->config = array(
            'default' => array(
                'email',
            ),
            'content_types' => array(
                'ng_feedback_form' => array(
                    'database',
                ),
            ),
        );

        $this->config2 = array(
            'default' => array(
                'email',
                'database',
                'email2',
                'database2',
            ),
        );

        $this->emptyConfig = array(
            'default',
        );

        $this->onlyDefaultConfig = array(
            'default' => array(
                'database',
                'email',
            ),
        );

        $this->action1 = $this->getMockBuilder(ActionInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('act'))
            ->getMock();

        $this->action2 = $this->getMockBuilder(ActionInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('act'))
            ->getMock();

        $this->action3 = $this->getMockBuilder(ActionInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('act'))
            ->getMock();

        $this->action4 = $this->getMockBuilder(ActionInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('act'))
            ->getMock();

        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('error', 'emergency', 'alert', 'debug', 'critical', 'notice', 'info', 'warning', 'log'))
            ->getMock();

        $this->registry = new ActionRegistry($this->config, $this->logger);
        $this->registryForPriority = new ActionRegistry($this->config2, $this->logger);
        $this->registryWithEmptyConf = new ActionRegistry($this->emptyConfig, $this->logger);
        $this->registryWithOnlyDefaultConf = new ActionRegistry($this->onlyDefaultConfig, $this->logger);

        $contentType = new ContentType(array(
            'identifier' => 'ng_feedback_form',
            'fieldDefinitions' => array(),
        ));

        $contentType2 = new ContentType(array(
            'identifier' => 'ng_feedback_form2',
            'fieldDefinitions' => array(),
        ));

        $this->event = new InformationCollected(
            new DataWrapper('payload', $contentType, 'target')
        );

        $this->event2 = new InformationCollected(
            new DataWrapper('payload', $contentType2, 'target')
        );

        parent::setUp();
    }

    public function testAddingActions(): void
    {
        $this->registry->addAction('database', $this->action1, 1);
        $this->registry->addAction('email', $this->action2, 100);
    }

    public function testAct(): void
    {
        $this->registry->addAction('database', $this->action1, 1);
        $this->registry->addAction('email', $this->action2, 2);

        $this->action1->expects($this->once())
            ->method('act')
            ->with($this->event);

        $this->action2->expects($this->never())
            ->method('act');

        $this->registry->act($this->event);
    }

    public function testActWithContentTypeThatDoesNotHaveConfiguration(): void
    {
        $this->registry->addAction('database', $this->action1, 1);
        $this->registry->addAction('email', $this->action2, 2);

        $this->action1->expects($this->never())
            ->method('act');

        $this->action2->expects($this->once())
            ->method('act');

        $this->registry->act($this->event2);
    }

    public function testActWithDefaultConfigOnly(): void
    {
        $this->registryWithOnlyDefaultConf->addAction('database', $this->action1, 1);
        $this->registryWithOnlyDefaultConf->addAction('email', $this->action2, 2);

        $this->action1->expects($this->once())
            ->method('act');

        $this->action2->expects($this->once())
            ->method('act');

        $this->registryWithOnlyDefaultConf->act($this->event2);
    }

    public function testActWithEmptyConfig(): void
    {
        $this->registryWithEmptyConf->addAction('database', $this->action1, 1);
        $this->registryWithEmptyConf->addAction('email', $this->action2, 2);

        $this->action1->expects($this->never())
            ->method('act');

        $this->action2->expects($this->never())
            ->method('act');

        $this->registryWithEmptyConf->act($this->event2);
    }

    public function testActWithActionFailedException(): void
    {
        $this->registry->addAction('database', $this->action1, 1);
        $this->registry->addAction('email', $this->action2, 2);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('InformationCollection action database failed with reason cannot write to database');

        $exception = new ActionFailedException('database', 'cannot write to database');

        $this->action1->expects($this->once())
            ->method('act')
            ->willThrowException($exception);

        $this->action2->expects($this->never())
            ->method('act');

        $this->registry->act($this->event);
    }

    public function testActionsAreExecutedByPriority(): void
    {
        $prioritizedActions = array(
            array(
                'name' => 'email2',
                'action' => $this->action4,
                'priority' => 100,
            ),
            array(
                'name' => 'database',
                'action' => $this->action1,
                'priority' => 44,
            ),
            array(
                'name' => 'email',
                'action' => $this->action2,
                'priority' => 22,
            ),
            array(
                'name' => 'database2',
                'action' => $this->action3,
                'priority' => 11,
            ),
        );

        $this->registryForPriority->addAction('database', $this->action1, 44);
        $this->registryForPriority->addAction('database2', $this->action3, 11);
        $this->registryForPriority->addAction('email', $this->action2, 22);
        $this->registryForPriority->addAction('email2', $this->action4, 100);

        $this->action4->expects($this->once())
            ->method('act');

        $this->action1->expects($this->once())
            ->method('act');

        $this->action2->expects($this->once())
            ->method('act');

        $this->action3->expects($this->once())
            ->method('act');

        $this->registryForPriority->act($this->event);

        $registryReflection = new ReflectionObject($this->registryForPriority);
        $actions = $registryReflection->getProperty('actions');
        $actions->setAccessible(true);

        $this->assertEquals($prioritizedActions, $actions->getValue($this->registryForPriority));
    }

    public function testActionsAreExecutedByPriorityWithSamePriorities(): void
    {
        $prioritizedActions = array(
            array(
                'name' => 'email2',
                'action' => $this->action4,
                'priority' => 100,
            ),
            array(
                'name' => 'database',
                'action' => $this->action1,
                'priority' => 44,
            ),
            array(
                'name' => 'database2',
                'action' => $this->action3,
                'priority' => 11,
            ),
            array(
                'name' => 'email',
                'action' => $this->action2,
                'priority' => 11,
            ),
        );

        $this->registryForPriority->addAction('database', $this->action1, 44);
        $this->registryForPriority->addAction('database2', $this->action3, 11);
        $this->registryForPriority->addAction('email', $this->action2, 11);
        $this->registryForPriority->addAction('email2', $this->action4, 100);

        $this->action4->expects($this->once())
            ->method('act');

        $this->action1->expects($this->once())
            ->method('act');

        $this->action2->expects($this->once())
            ->method('act');

        $this->action3->expects($this->once())
            ->method('act');

        $this->registryForPriority->act($this->event);

        $registryReflection = new ReflectionObject($this->registryForPriority);
        $actions = $registryReflection->getProperty('actions');
        $actions->setAccessible(true);

        $this->assertEquals($prioritizedActions, $actions->getValue($this->registryForPriority));
    }

    public function testSetDebugMethod(): void
    {
        $this->registryForPriority->addAction('database', $this->action1, 44);

        $this->action1->expects($this->never())
            ->method('act');

        $this->registryForPriority->setDebug(true);

        $registryReflection = new ReflectionObject($this->registryForPriority);
        $debug = $registryReflection->getProperty('debug');
        $debug->setAccessible(true);

        $this->assertTrue($debug->getValue($this->registryForPriority));
    }

    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException
     * @expectedExceptionMessage InformationCollection action database failed with reason cannot write to database

     */
    public function testThrowExceptionWhenDebugIsTrue(): void
    {
        $this->registry->addAction('database', $this->action1, 1);
        $this->registry->addAction('email', $this->action2, 2);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('InformationCollection action database failed with reason cannot write to database');

        $exception = new ActionFailedException('database', 'cannot write to database');

        $this->action1->expects($this->once())
            ->method('act')
            ->willThrowException($exception);

        $this->action2->expects($this->never())
            ->method('act');

        $this->registry->setDebug(true);
        $this->registry->act($this->event);
    }
}
