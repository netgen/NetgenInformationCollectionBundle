<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Value;

use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;
use PHPUnit\Framework\TestCase;

class EmailDataTest extends TestCase
{
    /**
     * @dataProvider emailDataProvider
     *
     * @param mixed $recipient
     * @param mixed $sender
     * @param mixed $subject
     * @param mixed $body
     */
    public function testGetters($recipient, $sender, $subject, $body)
    {
        $emailData = new EmailData($recipient, $sender, $subject, $body);

        $this->assertEquals($recipient, $emailData->getRecipient());
        $this->assertEquals($sender, $emailData->getSender());
        $this->assertEquals($subject, $emailData->getSubject());
        $this->assertEquals($body, $emailData->getBody());
        $this->assertNull($emailData->getAttachments());
    }

    public function emailDataProvider()
    {
        return array(
            array('recipient@example.com', 'sender@example.com', 'Test subject', 'Email body'),
            array('recipient1@example.com', 'sender1@example.com', 'Test subject 1', 'Email body 1'),
            array('recipient2@example.com', 'sender1@example.com', 'Test subject 2', 'Email body 2'),
            array('recipient3@example.com', 'sender1@example.com', 'Test subject 3', 'Email body 3'),
        );
    }
}
