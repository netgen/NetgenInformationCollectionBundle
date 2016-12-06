<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Entity;

use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection;
use PHPUnit_Framework_TestCase;

class EzInfoCollectionTest extends PHPUnit_Framework_TestCase
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
    }
}