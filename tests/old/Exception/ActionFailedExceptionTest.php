<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Exception;

use Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException;
use PHPUnit\Framework\TestCase;

class ActionFailedExceptionTest extends TestCase
{
    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException
     * @expectedExceptionMessage InformationCollection action test failed with reason some reason
     */
    public function testException()
    {
        throw new ActionFailedException('test', 'some reason');
    }
}
