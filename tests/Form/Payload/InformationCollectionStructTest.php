<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Form\Payload;

use Netgen\Bundle\InformationCollectionBundle\Form\Payload\InformationCollectionStruct;
use PHPUnit\Framework\TestCase;

class InformationCollectionStructTest extends TestCase
{
    public function testGetCollectedFieldValue()
    {
        $struct = new InformationCollectionStruct();
        $struct->setCollectedFieldValue('some_field', 'some_value');

        $this->assertEquals('some_value', $struct->getCollectedFieldValue('some_field'));
        $this->assertEquals(null, $struct->getCollectedFieldValue('some_field_not_existing'));
    }

    public function testGetCollectedFields()
    {
        $fields = array(
            'some_field_1' => 'some_value_1',
            'some_field_2' => 'some_value_2',
        );

        $struct = new InformationCollectionStruct();
        $struct->setCollectedFieldValue('some_field_1', 'some_value_1');
        $struct->setCollectedFieldValue('some_field_2', 'some_value_2');

        $this->assertEquals($fields, $struct->getCollectedFields());
    }
}
