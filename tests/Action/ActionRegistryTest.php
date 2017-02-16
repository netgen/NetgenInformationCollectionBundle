<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Action;

use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\InformationCollectionBundle\Action\ActionInterface;
use Netgen\Bundle\InformationCollectionBundle\Action\ActionRegistry;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ActionRegistryTest extends TestCase
{
    /**
     * @var ActionRegistry
     */
    protected $registry;

    /**
     * @var ActionRegistry
     */
    protected $registryWithEmptyConf;

    /**
     * @var ActionRegistry
     */
    protected $registryWithOnlyDefaultConf;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $onlyDefaultConfig;

    /**
     * @var array
     */
    protected $emptyConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $action1;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $action2;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    /**
     * @var InformationCollected
     */
    protected $event;

    /**
     * @var InformationCollected
     */
    protected $event2;

    public function setUp()
    {
        $this->config = [
            'default' => [
                'email',
            ],
            'content_type' => [
                'ng_feedback_form' => [
                    'database',
                ],
            ]
        ];

        $this->emptyConfig = [
            'default',
        ];

        $this->onlyDefaultConfig = [
            'default' => [
                'database',
                'email',
            ]
        ];

        $this->action1 = $this->getMockBuilder(ActionInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['act'])
            ->getMock();

        $this->action2 = $this->getMockBuilder(ActionInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['act'])
            ->getMock();

        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['error', 'emergency', 'alert', 'debug', 'critical', 'notice', 'info', 'warning', 'log'])
            ->getMock();

        $this->registry = new ActionRegistry($this->config, $this->logger);
        $this->registryWithEmptyConf = new ActionRegistry($this->emptyConfig, $this->logger);
        $this->registryWithOnlyDefaultConf = new ActionRegistry($this->onlyDefaultConfig, $this->logger);

        $contentType = new ContentType([
            'identifier' => 'ng_feedback_form',
            'fieldDefinitions' => [],
        ]);

        $contentType2 = new ContentType([
            'identifier' => 'ng_feedback_form2',
            'fieldDefinitions' => [],
        ]);

        $this->event = new InformationCollected(
            new DataWrapper('payload', $contentType, 'target')
        );

        $this->event2 = new InformationCollected(
            new DataWrapper('payload', $contentType2, 'target')
        );

        parent::setUp();
    }

    public function testAddingActions()
    {
        $this->registry->addAction('database', $this->action1, 1);
        $this->registry->addAction('email', $this->action2, 100);
    }

    public function testAct()
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

    public function testActWithContentTypeThatDoesNotHaveConfiguration()
    {
        $this->registry->addAction('database', $this->action1, 1);
        $this->registry->addAction('email', $this->action2, 2);

        $this->action1->expects($this->never())
            ->method('act');

        $this->action2->expects($this->once())
            ->method('act');

        $this->registry->act($this->event2);
    }

    public function testActWithDefaultConfigOnly()
    {
        $this->registryWithOnlyDefaultConf->addAction('database', $this->action1, 1);
        $this->registryWithOnlyDefaultConf->addAction('email', $this->action2, 2);

        $this->action1->expects($this->once())
            ->method('act');

        $this->action2->expects($this->once())
            ->method('act');

        $this->registryWithOnlyDefaultConf->act($this->event2);
    }

    public function testActWithEmptyConfig()
    {
        $this->registryWithEmptyConf->addAction('database', $this->action1, 1);
        $this->registryWithEmptyConf->addAction('email', $this->action2, 2);

        $this->action1->expects($this->never())
            ->method('act');

        $this->action2->expects($this->never())
            ->method('act');

        $this->registryWithEmptyConf->act($this->event2);
    }

    public function testActWithActionFailedException()
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
}
