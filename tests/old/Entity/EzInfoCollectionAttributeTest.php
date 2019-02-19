<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Entity;

use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute;
use PHPUnit\Framework\TestCase;

class EzInfoCollectionAttributeTest extends TestCase
{
    /**
     * @var EzInfoCollectionAttribute
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new EzInfoCollectionAttribute();
        parent::setUp();
    }

    public function testSetters()
    {
        $this->entity->setId(2342);
        $this->entity->setContentObjectId(24343);
        $this->entity->setContentClassAttributeId(2342363);
        $this->entity->setContentObjectAttributeId(64634);
        $this->entity->setInformationCollectionId(1234);
        $this->entity->setDataFloat(12.3);
        $this->entity->setDataInt(12);
        $this->entity->setDataText('test');

        $this->assertEquals(2342, $this->entity->getId());
        $this->assertEquals(24343, $this->entity->getContentObjectId());
        $this->assertEquals(2342363, $this->entity->getContentClassAttributeId());
        $this->assertEquals(64634, $this->entity->getContentObjectAttributeId());
        $this->assertEquals(1234, $this->entity->getInformationCollectionId());
        $this->assertEquals(12.3, $this->entity->getDataFloat());
        $this->assertEquals(12, $this->entity->getDataInt());
        $this->assertEquals('test', $this->entity->getDataText());
        $this->assertEquals('test', $this->entity->getValue());


        $this->entity->setDataText('');
        $this->assertEquals(12, $this->entity->getValue());

        $this->entity->setDataInt(0);
        $this->assertEquals(12.3, $this->entity->getValue());

        $this->entity->setDataFloat(0);
        $this->assertEquals('', $this->entity->getValue());
    }
}
