<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Exception;

use Netgen\Bundle\InformationCollectionBundle\Exception\EmailNotSentException;
use PHPUnit\Framework\TestCase;

class EmailNotSentExceptionTest extends TestCase
{
    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\EmailNotSentException
     * @expectedExceptionMessage Error occurred while trying to send email: test@example.com failed with error invalid email
     */
    public function testException()
    {
        throw new EmailNotSentException('test@example.com', 'invalid email');
    }
}
