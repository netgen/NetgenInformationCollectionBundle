<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Event;

use Netgen\Bundle\IbexaFormsBundle\Form\DataWrapper;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use PHPUnit\Framework\TestCase;

class InformationCollectedTest extends TestCase
{
    protected InformationCollected $event;

    public function setUp(): void
    {
        $data = new DataWrapper('payload', 'definition', 'target');
        $this->event = new InformationCollected($data, 'additional content');

        parent::setUp();
    }

    public function testGetters(): void
    {
        $this->assertEquals('payload', $this->event->getInformationCollectionStruct());
        $this->assertEquals('definition', $this->event->getContentType());
        $this->assertEquals('target', $this->event->getLocation());
        $this->assertEquals('additional content', $this->event->getAdditionalContent());
    }
}
