<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Value;

use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;
use PHPUnit\Framework\TestCase;

class EmailDataTest extends TestCase
{
    /**
     * @dataProvider emailDataProvider
     */
    public function testGetters($recipient, $sender, $subject, $template)
    {
        $emailData = new EmailData($recipient, $sender, $subject, $template);

        $this->assertEquals($recipient, $emailData->getRecipient());
        $this->assertEquals($sender, $emailData->getSender());
        $this->assertEquals($subject, $emailData->getSubject());
        $this->assertEquals($template, $emailData->getTemplate());
    }
    public function emailDataProvider()
    {
        return [
            ['recipient@example.com', 'sender@example.com', 'Test subject', 'NetgenInformationCollectionBundle::email.html.twig'],
            ['recipient1@example.com', 'sender1@example.com', 'Test subject 1', 'NetgenInformationCollectionBundle::email1.html.twig'],
            ['recipient2@example.com', 'sender1@example.com', 'Test subject 2', 'NetgenInformationCollectionBundle::email2.html.twig'],
            ['recipient3@example.com', 'sender1@example.com', 'Test subject 3', 'NetgenInformationCollectionBundle::email2.html.twig'],
        ];
    }
}
