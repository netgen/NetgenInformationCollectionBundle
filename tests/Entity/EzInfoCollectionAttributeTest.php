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
    }
}
