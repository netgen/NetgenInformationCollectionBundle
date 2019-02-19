<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Entity;

use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection;
use PHPUnit\Framework\TestCase;

class EzInfoCollectionTest extends TestCase
{
    /**
     * @var EzInfoCollection
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new EzInfoCollection();
        parent::setUp();
    }

    public function testGettersAndSetters()
    {
        $id = 123;
        $this->entity->setId($id);
        $this->assertEquals($id, $this->entity->getId());

        $this->entity->setContentObjectId(4234);
        $this->entity->setCreated(4535);
        $this->entity->setCreatorId(43432456);
        $this->entity->setModified(43432);
        $this->entity->setUserIdentifier(546);


        $this->assertEquals(4234, $this->entity->getContentObjectId());
        $this->assertEquals(4535, $this->entity->getCreated());
        $this->assertEquals(43432456, $this->entity->getCreatorId());
        $this->assertEquals(43432, $this->entity->getModified());
        $this->assertEquals(546, $this->entity->getUserIdentifier());
    }
}
