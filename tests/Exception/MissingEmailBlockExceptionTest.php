<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Exception;

use Netgen\Bundle\InformationCollectionBundle\Exception\MissingEmailBlockException;
use PHPUnit\Framework\TestCase;

class MissingEmailBlockExceptionTest extends TestCase
{
    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\MissingEmailBlockException
     * @expectedExceptionMessage Missing email block in AcmeBundle::email.html.twig template, currently there is none available.
     */
    public function testExceptionWithNoBlocks()
    {
        throw new MissingEmailBlockException('AcmeBundle::email.html.twig', array());
    }

    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\MissingEmailBlockException
     * @expectedExceptionMessage Missing email block in AcmeBundle::email.html.twig template, currently there is email available.
     */
    public function testExceptionWithSingleBlock()
    {
        throw new MissingEmailBlockException('AcmeBundle::email.html.twig', array('email'));
    }

    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\MissingEmailBlockException
     * @expectedExceptionMessage Missing email block in AcmeBundle::email.html.twig template, currently there are recipient, email available.
     */
    public function testExceptionWithMultipleBlocks()
    {
        throw new MissingEmailBlockException('AcmeBundle::email.html.twig', array('recipient', 'email'));
    }
}
