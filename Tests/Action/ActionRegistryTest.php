<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Action;

use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\InformationCollectionBundle\Action\ActionInterface;
use Netgen\Bundle\InformationCollectionBundle\Action\ActionRegistry;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class ActionRegistryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ActionRegistry
     */
    protected $registry;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $action1;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $action2;

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
                'database',
            ],
            'content_type' => [
                'ng_feedback_form' => [
                    'database',
                ],
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

        $this->registry = new ActionRegistry($this->config);

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
        $this->registry->addAction('database', $this->action1);
        $this->registry->addAction('email', $this->action2);
    }

    public function testAct()
    {
        $this->registry->addAction('database', $this->action1);
        $this->registry->addAction('email', $this->action2);

        $this->action1->expects($this->once())
            ->method('act')
            ->with($this->event);

        $this->action2->expects($this->never())
            ->method('act');

        $this->registry->act($this->event);
    }

    public function testActWithContentTypeThatDoesNotHaveConfiguration()
    {
        $this->registry->addAction('database', $this->action1);
        $this->registry->addAction('email', $this->action2);

        $this->action1->expects($this->once())
            ->method('act');

        $this->action2->expects($this->never())
            ->method('act');

        $this->registry->act($this->event2);
    }
}
