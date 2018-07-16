<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Exception;

use Netgen\Bundle\InformationCollectionBundle\Exception\MissingValueException;
use PHPUnit\Framework\TestCase;

class MissingValueExceptionTest extends TestCase
{
    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\MissingValueException
     * @expectedExceptionMessage There is no value for field my_field specified.
     */
    public function testException()
    {
        throw new MissingValueException('my_field');
    }
}
