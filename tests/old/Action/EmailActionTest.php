<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Action;

use Netgen\Bundle\IbexaFormsBundle\Form\DataWrapper;
use Netgen\Bundle\IbexaFormsBundle\Form\Payload\InformationCollectionStruct;
use Netgen\Bundle\InformationCollectionBundle\Action\EmailAction;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Exception\EmailNotSentException;
use Netgen\Bundle\InformationCollectionBundle\Factory\EmailDataFactory;
use Netgen\Bundle\InformationCollectionBundle\Mailer\MailerInterface;
use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EmailActionTest extends TestCase
{
    protected EmailAction $action;

    protected MockObject $factory;

    protected MockObject $mailer;

    protected MockObject $emailData;

    public function setUp(): void
    {
        $this->factory = $this->getMockBuilder(EmailDataFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(array('build'))
            ->getMock();

        $this->mailer = $this->getMockBuilder(MailerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('createAndSendMessage'))
            ->getMock();

        $this->emailData = $this->getMockBuilder(EmailData::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getSubject', 'getRecipient', 'getSender', 'getBody'))
            ->getMock();

        $this->action = new EmailAction($this->factory, $this->mailer);
        parent::setUp();
    }

    public function testAct()
    {
        $informationCollectionStruct = new InformationCollectionStruct();
        $dataWrapper = new DataWrapper($informationCollectionStruct, null, null);
        $event = new InformationCollected($dataWrapper);

        $this->factory->expects($this->once())
            ->method('build')
            ->with($event)
            ->willReturn($this->emailData);

        $this->mailer->expects($this->once())
            ->method('createAndSendMessage')
            ->with($this->emailData);

        $this->action->act($event);
    }

    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException
     * @expectedExceptionMessage Error occurred while trying to send email: test@example.com failed with error invalid email address
     */
    public function testActWithException()
    {
        $informationCollectionStruct = new InformationCollectionStruct();
        $dataWrapper = new DataWrapper($informationCollectionStruct, null, null);
        $event = new InformationCollected($dataWrapper);

        $this->factory->expects($this->once())
            ->method('build')
            ->with($event)
            ->willReturn($this->emailData);

        $exception = new EmailNotSentException('test@example.com', 'invalid email address');

        $this->mailer->expects($this->once())
            ->method('createAndSendMessage')
            ->with($this->emailData)
            ->willThrowException($exception);

        $this->action->act($event);
    }
}
