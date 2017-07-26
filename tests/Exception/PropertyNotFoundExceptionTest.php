<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Exception;

use Netgen\Bundle\InformationCollectionBundle\Exception\PropertyNotFoundException;
use PHPUnit\Framework\TestCase;

class PropertyNotFoundExceptionTest extends TestCase
{
    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\PropertyNotFoundException
     * @expectedExceptionMessage Property 'my_property' not found on class 'Netgen\Bundle\InformationCollectionBundle\Tests\Exception\PropertyNotFoundExceptionTest'
     */
    public function testException()
    {
        throw new PropertyNotFoundException('my_property', get_class());
    }
}
