<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Exception;

use Netgen\Bundle\InformationCollectionBundle\Exception\PropertyReadOnlyException;
use PHPUnit\Framework\TestCase;

class PropertyReadOnlyExceptionTest extends TestCase
{
    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\PropertyReadOnlyException
     * @expectedExceptionMessage Property 'my_property' is readonly on class 'Netgen\Bundle\InformationCollectionBundle\Tests\Exception\PropertyReadOnlyExceptionTest'
     */
    public function testException()
    {
        throw new PropertyReadOnlyException('my_property', get_class());
    }
}
