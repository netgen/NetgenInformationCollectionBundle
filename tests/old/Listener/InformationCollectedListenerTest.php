<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Listener;

use Netgen\Bundle\IbexaFormsBundle\Form\DataWrapper;
use Netgen\Bundle\InformationCollectionBundle\Action\ActionRegistry;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Events;
use Netgen\Bundle\InformationCollectionBundle\Listener\InformationCollectedListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InformationCollectedListenerTest extends TestCase
{
    protected InformationCollectedListener $listener;

    protected MockObject $registry;

    public function setUp(): void
    {
        $this->registry = $this->getMockBuilder(ActionRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array('act'))
            ->getMock();

        $this->listener = new InformationCollectedListener($this->registry);

        parent::setUp();
    }

    public function testListenerConfiguration(): void
    {
        $this->assertEquals(
            array(Events::INFORMATION_COLLECTED => 'onInformationCollected'),
            InformationCollectedListener::getSubscribedEvents()
        );
    }

    public function testItRunsActions(): void
    {
        $event = new InformationCollected(new DataWrapper('payload'));

        $this->registry->expects($this->once())
            ->method('act')
            ->with($event);

        $this->listener->onInformationCollected($event);
    }
}
