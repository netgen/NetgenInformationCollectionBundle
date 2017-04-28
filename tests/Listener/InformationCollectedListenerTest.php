<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Listener;

use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\InformationCollectionBundle\Action\ActionRegistry;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Events;
use Netgen\Bundle\InformationCollectionBundle\Listener\InformationCollectedListener;
use PHPUnit\Framework\TestCase;

class InformationCollectedListenerTest extends TestCase
{
    /**
     * @var InformationCollectedListener
     */
    protected $listener;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = $this->getMockBuilder(ActionRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array('act'))
            ->getMock();

        $this->listener = new InformationCollectedListener($this->registry);

        parent::setUp();
    }

    public function testListenerConfiguration()
    {
        $this->assertEquals(
            array(Events::INFORMATION_COLLECTED => 'onInformationCollected'),
            InformationCollectedListener::getSubscribedEvents()
        );
    }

    public function testItRunsActions()
    {
        $event = new InformationCollected(new DataWrapper('payload'));

        $this->registry->expects($this->once())
            ->method('act')
            ->with($event);

        $this->listener->onInformationCollected($event);
    }
}
