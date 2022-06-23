<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Exception;

use Netgen\Bundle\InformationCollectionBundle\Exception\MissingEmailBlockException;
use PHPUnit\Framework\TestCase;

class MissingEmailBlockExceptionTest extends TestCase
{
    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\MissingEmailBlockException
     * @expectedExceptionMessage Missing email block in @Acme/email.html.twig template, currently there is none available.
     */
    public function testExceptionWithNoBlocks(): void
    {
        throw new MissingEmailBlockException('@Acme/email.html.twig', array());
    }

    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\MissingEmailBlockException
     * @expectedExceptionMessage Missing email block in @Acme/email.html.twig template, currently there is email available.
     */
    public function testExceptionWithSingleBlock(): void
    {
        throw new MissingEmailBlockException('@Acme/email.html.twig', array('email'));
    }

    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\MissingEmailBlockException
     * @expectedExceptionMessage Missing email block in @Acme/email.html.twig template, currently there are recipient, email available.
     */
    public function testExceptionWithMultipleBlocks(): void
    {
        throw new MissingEmailBlockException('@Acme/email.html.twig', array('recipient', 'email'));
    }
}
