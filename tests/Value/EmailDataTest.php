<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Value;

use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;
use PHPUnit\Framework\TestCase;

class EmailDataTest extends TestCase
{
    /**
     * @dataProvider emailDataProvider
     */
    public function testGetters($recipient, $sender, $subject, $body)
    {
        $emailData = new EmailData($recipient, $sender, $subject, $body);

        $this->assertEquals($recipient, $emailData->getRecipient());
        $this->assertEquals($sender, $emailData->getSender());
        $this->assertEquals($subject, $emailData->getSubject());
        $this->assertEquals($body, $emailData->getBody());
    }
    public function emailDataProvider()
    {
        return [
            ['recipient@example.com', 'sender@example.com', 'Test subject', 'Email body'],
            ['recipient1@example.com', 'sender1@example.com', 'Test subject 1', 'Email body 1'],
            ['recipient2@example.com', 'sender1@example.com', 'Test subject 2', 'Email body 2'],
            ['recipient3@example.com', 'sender1@example.com', 'Test subject 3', 'Email body 3'],
        ];
    }
}
