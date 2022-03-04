<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Mailer;

use Ibexa\Core\FieldType\BinaryFile\Value as BinaryFileValue;
use Netgen\Bundle\InformationCollectionBundle\Mailer\Mailer;
use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;
use PHPUnit\Framework\TestCase;

class MailerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Mailer\MailerInterface
     */
    protected $mailer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $swiftMailer;

    public function setUp(): void
    {
        $this->swiftMailer = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->setMethods(array('createMessage', 'send'))
            ->getMock();

        $this->mailer = new Mailer($this->swiftMailer);
    }

    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\EmailNotSentException
     * @expectedExceptionMessage Error occurred while trying to send email: recipient failed with error Address in mailbox given [[][][]][]] does not comply with RFC 2822, 3.6.2.
     */
    public function testCreateAndSendMessageWithWrongRecipient(): void
    {
        $data = new EmailData('[][][]][]', 'sender@example.com', 'Test', 'Body');

        $this->swiftMailer->expects($this->never())
            ->method('send');

        $this->mailer->createAndSendMessage($data);
    }

    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\EmailNotSentException
     * @expectedExceptionMessage Error occurred while trying to send email: sender failed with error Address in mailbox given [[][][]][]] does not comply with RFC 2822, 3.6.2.
     */
    public function testCreateAndSendMessageWithWrongSender(): void
    {
        $data = new EmailData('recipient@example.com', '[][][]][]', 'Test', 'Body');

        $this->swiftMailer->expects($this->never())
            ->method('send');

        $this->mailer->createAndSendMessage($data);
    }

    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\EmailNotSentException
     * @expectedExceptionMessage Error occurred while trying to send email: send failed with error invalid mailer configuration?
     */
    public function testCreateAndSendMessageWithErrorFromInternalMailer(): void
    {
        $data = new EmailData('recipient@example.com', 'sender@example.com', 'Test', 'Body');

        $this->swiftMailer->expects($this->once())
            ->method('send')
            ->willReturn(0);

        $this->mailer->createAndSendMessage($data);
    }

    public function testCreateAndSendMessage(): void
    {
        $data = new EmailData('recipient@example.com', 'sender@example.com', 'Test', 'Body');

        $this->swiftMailer->expects($this->once())
            ->method('send')
            ->willReturn(1);

        $this->mailer->createAndSendMessage($data);
    }

    public function testCreateAndSendMessageWithAttachments(): void
    {
        $attachments = [
            new BinaryFileValue(
                [
                    'inputUri' => __DIR__ . "\attachment.txt",
                ]
            ),
        ];

        $data = new EmailData('recipient@example.com', 'sender@example.com', 'Test', 'Body', $attachments);

        $this->swiftMailer->expects($this->once())
            ->method('send')
            ->willReturn(1);

        $this->mailer->createAndSendMessage($data);
    }
}
