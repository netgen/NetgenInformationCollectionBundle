<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Factory;

use eZ\Publish\Core\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use eZ\Publish\Core\FieldType\EmailAddress\Value as EmailValue;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\Helper\FieldHelper;
use Netgen\Bundle\InformationCollectionBundle\Factory\EmailDataFactory;
use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;
use PHPUnit\Framework\TestCase;

class EmailDataFactoryTest extends TestCase
{
    /**
     * @var EmailDataFactory
     */
    protected $factory;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $translationHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentTypeService;

    /**
     * @var ContentType
     */
    protected $contentType;

    /**
     * @var ContentType
     */
    protected $contentType2;

    /**
     * @var VersionInfo
     */
    protected $versionInfo;

    public function setUp()
    {
        $this->config = [
            'templates' => [
                'default' => 'AcmeBundle::email.html.twig',
                'test_content_type' => 'AcmeBundle::test_content_type.html.twig',
            ],
            'default_variables' => [
                'sender' => 'sender@example.com',
                'recipient' => 'recipient@example.com',
                'subject' => 'subject',
            ],
        ];

        $this->translationHelper = $this->getMockBuilder(TranslationHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTranslatedField'])
            ->getMock();

        $this->fieldHelper = $this->getMockBuilder(FieldHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(['isFieldEmpty'])
            ->getMock();

        $this->contentTypeService = $this->getMockBuilder(ContentTypeService::class)
            ->disableOriginalConstructor()
            ->setMethods(['loadContentType'])
            ->getMock();

        $this->contentType = new ContentType([
            'identifier' => 'test_content_type',
            'fieldDefinitions' => [],
        ]);

        $this->contentType2 = new ContentType([
            'identifier' => 'test_content_type2',
            'fieldDefinitions' => [],
        ]);

        $this->versionInfo = new VersionInfo([
            'contentInfo' => new ContentInfo([
                'contentTypeId' => 123,
            ])
        ]);

        $this->factory = new EmailDataFactory(
            $this->config,
            $this->translationHelper,
            $this->fieldHelper,
            $this->contentTypeService
        );
        parent::setUp();
    }

    public function testBuildingWithSenderRecipientAndSubjectFromContent()
    {
        $recipientField = new Field([
            'value' => new EmailValue('recipient@test.com'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'recipient'
        ]);

        $senderField = new Field([
            'value' => new EmailValue('sender@test.com'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'sender'
        ]);

        $subjectField = new Field([
            'value' => new TextLineValue('subject test'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'subject',
        ]);

        $content = new Content([
            'internalFields' => [
                $recipientField, $senderField, $subjectField,
            ],
            'versionInfo' => $this->versionInfo,
        ]);

        $this->fieldHelper->expects($this->exactly(3))
            ->method('isFieldEmpty')
            ->withAnyParameters()
            ->willReturn(false);


        $this->translationHelper->expects($this->at(0))
            ->method('getTranslatedField')
            ->with($content, 'recipient')
            ->willReturn($recipientField);


        $this->translationHelper->expects($this->at(1))
            ->method('getTranslatedField')
            ->with($content, 'sender')
            ->willReturn($senderField);

        $this->translationHelper->expects($this->at(2))
            ->method('getTranslatedField')
            ->with($content, 'subject')
            ->willReturn($subjectField);

        $this->contentTypeService->expects($this->once())
            ->method('loadContentType')
            ->with(123)
            ->willReturn($this->contentType);

        $value = $this->factory->build($content);

        $this->assertInstanceOf(EmailData::class, $value);
        $this->assertEquals('recipient@test.com', $value->getRecipient());
        $this->assertEquals('sender@test.com', $value->getSender());
        $this->assertEquals('subject test', $value->getSubject());
    }

    public function testBuildingWithSenderRecipientAndSubjectFromConfiguration()
    {
        $recipientField = new Field([
            'value' => new EmailValue('recipient@test.com'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'recipient'
        ]);

        $senderField = new Field([
            'value' => new EmailValue('sender@test.com'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'sender'
        ]);

        $subjectField = new Field([
            'value' => new TextLineValue('subject test'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'subject',
        ]);

        $content = new Content([
            'internalFields' => [
                $recipientField, $senderField, $subjectField,
            ],
            'versionInfo' => $this->versionInfo,
        ]);

        $this->fieldHelper->expects($this->exactly(3))
            ->method('isFieldEmpty')
            ->withAnyParameters()
            ->willReturn(true);


        $this->translationHelper->expects($this->never())
            ->method('getTranslatedField')
            ->with($content, 'recipient');

        $this->translationHelper->expects($this->never())
            ->method('getTranslatedField')
            ->with($content, 'sender');

        $this->translationHelper->expects($this->never())
            ->method('getTranslatedField')
            ->with($content, 'subject');

        $this->contentTypeService->expects($this->once())
            ->method('loadContentType')
            ->with(123)
            ->willReturn($this->contentType);

        $value = $this->factory->build($content);

        $this->assertInstanceOf(EmailData::class, $value);
        $this->assertEquals($this->config['default_variables']['recipient'], $value->getRecipient());
        $this->assertEquals($this->config['default_variables']['sender'], $value->getSender());
        $this->assertEquals($this->config['default_variables']['subject'], $value->getSubject());
    }

    public function testBuildingWithDefaultTemplate()
    {
        $recipientField = new Field([
            'value' => new EmailValue('recipient@test.com'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'recipient'
        ]);

        $senderField = new Field([
            'value' => new EmailValue('sender@test.com'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'sender'
        ]);

        $subjectField = new Field([
            'value' => new TextLineValue('subject test'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'subject',
        ]);

        $content = new Content([
            'internalFields' => [
                $recipientField, $senderField, $subjectField,
            ],
            'versionInfo' => $this->versionInfo,
        ]);

        $this->fieldHelper->expects($this->exactly(3))
            ->method('isFieldEmpty')
            ->withAnyParameters()
            ->willReturn(false);


        $this->translationHelper->expects($this->at(0))
            ->method('getTranslatedField')
            ->with($content, 'recipient')
            ->willReturn($recipientField);


        $this->translationHelper->expects($this->at(1))
            ->method('getTranslatedField')
            ->with($content, 'sender')
            ->willReturn($senderField);

        $this->translationHelper->expects($this->at(2))
            ->method('getTranslatedField')
            ->with($content, 'subject')
            ->willReturn($subjectField);

        $this->contentTypeService->expects($this->once())
            ->method('loadContentType')
            ->with(123)
            ->willReturn($this->contentType2);

        $value = $this->factory->build($content);

        $this->assertInstanceOf(EmailData::class, $value);
        $this->assertEquals($this->config['templates']['default'], $value->getTemplate());
    }

    public function testBuildingWithTemplateResolvedByContentType()
    {
        $recipientField = new Field([
            'value' => new EmailValue('recipient@test.com'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'recipient'
        ]);

        $senderField = new Field([
            'value' => new EmailValue('sender@test.com'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'sender'
        ]);

        $subjectField = new Field([
            'value' => new TextLineValue('subject test'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'subject',
        ]);

        $content = new Content([
            'internalFields' => [
                $recipientField, $senderField, $subjectField,
            ],
            'versionInfo' => $this->versionInfo,
        ]);

        $this->fieldHelper->expects($this->exactly(3))
            ->method('isFieldEmpty')
            ->withAnyParameters()
            ->willReturn(false);


        $this->translationHelper->expects($this->at(0))
            ->method('getTranslatedField')
            ->with($content, 'recipient')
            ->willReturn($recipientField);


        $this->translationHelper->expects($this->at(1))
            ->method('getTranslatedField')
            ->with($content, 'sender')
            ->willReturn($senderField);

        $this->translationHelper->expects($this->at(2))
            ->method('getTranslatedField')
            ->with($content, 'subject')
            ->willReturn($subjectField);

        $this->contentTypeService->expects($this->once())
            ->method('loadContentType')
            ->with(123)
            ->willReturn($this->contentType);

        $value = $this->factory->build($content);

        $this->assertInstanceOf(EmailData::class, $value);
        $this->assertEquals($this->config['templates']['test_content_type'], $value->getTemplate());
    }
}
