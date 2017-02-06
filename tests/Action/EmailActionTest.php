<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Action;

use eZ\Publish\Core\Repository\ContentService;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Content;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;
use Netgen\Bundle\InformationCollectionBundle\Action\EmailAction;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Factory\EmailDataFactory;
use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine;
use Swift_Mailer;

class EmailActionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EmailAction
     */
    protected $action;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $factory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $template;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentService;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $mailer;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentType;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailData;

    public function setUp()
    {
        $this->factory = $this->getMockBuilder(EmailDataFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();

        $this->template = $this->getMockBuilder(DelegatingEngine::class)
            ->disableOriginalConstructor()
            ->setMethods(['render'])
            ->getMock();

        $this->contentService = $this->getMockBuilder(ContentService::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->mailer = $this->getMockBuilder(Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();

        $this->contentType = $this->getMockBuilder(ContentType::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->emailData = $this->getMockBuilder(EmailData::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSubject', 'getRecipient', 'getSender', 'getTemplate'])
            ->getMock();

        $this->action = new EmailAction($this->factory, $this->mailer, $this->template, $this->contentService);
        parent::setUp();
    }

    public function testAct()
    {
        $informationCollectionStruct = new InformationCollectionStruct();
        $location = new Location([
            'contentInfo' => new ContentInfo([
                'id' => 123,
            ]),
        ]);

        $content = new Content();
        $dataWrapper = new DataWrapper($informationCollectionStruct, $this->contentType, $location);
        $event = new InformationCollected($dataWrapper);

        $this->contentService->expects($this->once())
            ->method('loadContent')
            ->with(123)
            ->willReturn($content);

        $this->emailData->expects($this->once())
            ->method('getSubject')
            ->willReturn('subject');

        $this->emailData->expects($this->once())
            ->method('getRecipient')
            ->willReturn('recipient@test.com');

        $this->emailData->expects($this->once())
            ->method('getSender')
            ->willReturn('sender@test.com');

        $this->emailData->expects($this->once())
            ->method('getTemplate')
            ->willReturn('template');

        $this->factory->expects($this->once())
            ->method('build')
            ->with($content)
            ->willReturn($this->emailData);

        $this->template->expects($this->once())
            ->method('render');

        $this->mailer->expects($this->once())
            ->method('send')
            ->willReturn(1);

        $this->action->act($event);
    }

    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException
     */
    public function testActWithException()
    {
        $informationCollectionStruct = new InformationCollectionStruct();
        $location = new Location([
            'contentInfo' => new ContentInfo([
                'id' => 123,
            ]),
        ]);

        $content = new Content();
        $dataWrapper = new DataWrapper($informationCollectionStruct, $this->contentType, $location);
        $event = new InformationCollected($dataWrapper);

        $this->contentService->expects($this->once())
            ->method('loadContent')
            ->with(123)
            ->willReturn($content);

        $this->emailData->expects($this->once())
            ->method('getSubject')
            ->willReturn('subject');

        $this->emailData->expects($this->once())
            ->method('getRecipient')
            ->willReturn('recipient@test.com');

        $this->emailData->expects($this->once())
            ->method('getSender')
            ->willReturn('sender@test.com');

        $this->emailData->expects($this->once())
            ->method('getTemplate')
            ->willReturn('template');

        $this->factory->expects($this->once())
            ->method('build')
            ->with($content)
            ->willReturn($this->emailData);

        $this->template->expects($this->once())
            ->method('render');

        $this->mailer->expects($this->once())
            ->method('send')
            ->willReturn(false);

        $this->action->act($event);
    }
}
