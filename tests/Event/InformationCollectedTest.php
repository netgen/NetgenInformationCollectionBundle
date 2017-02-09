<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Event;

use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use PHPUnit\Framework\TestCase;

class InformationCollectedTest extends TestCase
{
    /**
     * @var InformationCollected
     */
    protected $event;

    public function setUp()
    {
        $data = new DataWrapper('payload', 'definition', 'target');
        $this->event = new InformationCollected($data, 'additional content');

        parent::setUp();
    }

    public function testGetters()
    {
        $this->assertEquals('payload', $this->event->getInformationCollectionStruct());
        $this->assertEquals('definition', $this->event->getContentType());
        $this->assertEquals('target', $this->event->getLocation());
        $this->assertEquals('additional content', $this->event->getAdditionalContent());
    }
}
