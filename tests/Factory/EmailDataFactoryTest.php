<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Factory;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\EmailAddress\Value as EmailValue;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use eZ\Publish\Core\Helper\FieldHelper;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\Repository\ContentService;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Factory\EmailDataFactory;
use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;
use PHPUnit\Framework\TestCase;
use Twig_Environment;
use Twig_Loader_Array;
use Twig_TemplateWrapper;

class EmailDataFactoryTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Factory\EmailDataFactory
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
    protected $contentService;

    /**
     * @var \eZ\Publish\Core\Repository\Values\ContentType\ContentType
     */
    protected $contentType;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $twig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateWrapper;

    /**
     * @var \eZ\Publish\Core\Repository\Values\ContentType\ContentType
     */
    protected $contentType2;

    /**
     * @var \eZ\Publish\Core\Repository\Values\Content\VersionInfo
     */
    protected $versionInfo;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configResolver;

    public function setUp()
    {
        $this->config = array(
            'templates' => array(
                'default' => 'AcmeBundle::email.html.twig',
                'content_types' => array(
                    'test_content_type' => 'AcmeBundle::test_content_type.html.twig',
                )
            ),
            'default_variables' => array(
                'sender' => 'sender@example.com',
                'recipient' => 'recipient@example.com',
                'subject' => 'subject',
            ),
        );

        $this->translationHelper = $this->getMockBuilder(TranslationHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getTranslatedField'))
            ->getMock();

        $this->fieldHelper = $this->getMockBuilder(FieldHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(array('isFieldEmpty'))
            ->getMock();

        $this->contentService = $this->getMockBuilder(ContentService::class)
            ->disableOriginalConstructor()
            ->setMethods(array('loadContent'))
            ->getMock();

        $this->twig = $this->getMockBuilder(\Twig_Environment::class)
            ->disableOriginalConstructor()
            ->setMethods(array('load'))
            ->getMock();

        $this->contentType = new ContentType(array(
            'identifier' => 'test_content_type',
            'fieldDefinitions' => array(),
        ));

        $this->contentType2 = new ContentType(array(
            'identifier' => 'test_content_type2',
            'fieldDefinitions' => array(),
        ));

        $this->versionInfo = new VersionInfo(array(
            'contentInfo' => new ContentInfo(array(
                'contentTypeId' => 123,
            )),
        ));

        $this->configResolver = $this->createMock(ConfigResolverInterface::class);

        parent::setUp();
    }

    public function testBuildingWithSenderRecipientAndSubjectFromContent()
    {
        $twig = new Twig_Environment(
            new Twig_Loader_Array(
                array(
                    'index' => '{% block email %}{{ "email body" }}{% endblock %}',
                )
            )
        );

        $templateWrapper = new Twig_TemplateWrapper($twig, $twig->loadTemplate('index'));

        $this->configResolver->expects($this->once())
            ->method('getParameter')
            ->with('action_config.email', 'netgen_information_collection')
            ->willReturn($this->config);

        $this->factory = new EmailDataFactory(
            $this->configResolver,
            $this->translationHelper,
            $this->fieldHelper,
            $this->contentService,
            $this->twig
        );

        $recipientField = new Field(array(
            'value' => new EmailValue('recipient@test.com'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'recipient',
        ));

        $senderField = new Field(array(
            'value' => new EmailValue('sender@test.com'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'sender',
        ));

        $subjectField = new Field(array(
            'value' => new TextLineValue('subject test'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'subject',
        ));

        $content = new Content(array(
            'internalFields' => array(
                $recipientField, $senderField, $subjectField,
            ),
            'versionInfo' => $this->versionInfo,
        ));

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

        $this->contentService->expects($this->once())
            ->method('loadContent')
            ->with(123)
            ->willReturn($content);

        $location = new Location(
            array(
                'id' => 12345,
                'contentInfo' => new ContentInfo(array('id' => 123)),
            )
        );

        $contentType = new ContentType(array(
            'identifier' => 'test',
            'fieldDefinitions' => array(),
        ));

        $informationCollectionStruct = new InformationCollectionStruct();
        $informationCollectionStruct->setCollectedFieldValue('my_value_1', new TextLineValue("My value 1"));
        $informationCollectionStruct->setCollectedFieldValue('my_value_2', new TextLineValue("My value 2"));
        $event = new InformationCollected(new DataWrapper($informationCollectionStruct, $contentType, $location));

        $this->twig->expects($this->once())
            ->method('load')
            ->willReturn($templateWrapper);

        $value = $this->factory->build($event);

        $this->assertInstanceOf(EmailData::class, $value);
        $this->assertEquals('recipient@test.com', $value->getRecipient());
        $this->assertEquals('sender@test.com', $value->getSender());
        $this->assertEquals('subject test', $value->getSubject());
        $this->assertEquals('email body', $value->getBody());
    }

    public function testBuildingWithSenderRecipientAndSubjectFromTemplate()
    {
        $template = <<<TEMPLATE
            {% block email %}{{ 'My email body' }}{% endblock %}
            {% block subject %}{{ 'My custom subject' }}{% endblock %}
            {% block recipient %}{{ 'recipient@template.com' }}{% endblock %}
            {% block sender %}{{ 'sender@template.com' }}{% endblock %}
TEMPLATE;

        $twig = new Twig_Environment(
            new Twig_Loader_Array(
                array(
                    'index' => $template,
                )
            )
        );

        $templateWrapper = new Twig_TemplateWrapper($twig, $twig->loadTemplate('index'));

        $this->configResolver->expects($this->once())
            ->method('getParameter')
            ->with('action_config.email', 'netgen_information_collection')
            ->willReturn($this->config);

        $this->factory = new EmailDataFactory(
            $this->configResolver,
            $this->translationHelper,
            $this->fieldHelper,
            $this->contentService,
            $this->twig
        );

        $recipientField = new Field(array(
            'value' => new EmailValue('recipient@test.com'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'recipient',
        ));

        $senderField = new Field(array(
            'value' => new EmailValue('sender@test.com'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'sender',
        ));

        $subjectField = new Field(array(
            'value' => new TextLineValue('subject test'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'subject',
        ));

        $content = new Content(array(
            'internalFields' => array(
                $recipientField, $senderField, $subjectField,
            ),
            'versionInfo' => $this->versionInfo,
        ));

        $this->fieldHelper->expects($this->never())
            ->method('isFieldEmpty');

        $this->translationHelper->expects($this->never())
            ->method('getTranslatedField');

        $this->contentService->expects($this->once())
            ->method('loadContent')
            ->with(123)
            ->willReturn($content);

        $location = new Location(
            array(
                'id' => 12345,
                'contentInfo' => new ContentInfo(array('id' => 123)),
            )
        );

        $contentType = new ContentType(array(
            'identifier' => 'test_content_type',
            'fieldDefinitions' => array(),
        ));

        $informationCollectionStruct = new InformationCollectionStruct();
        $informationCollectionStruct->setCollectedFieldValue('my_value_1', new TextLineValue("My value 1"));
        $informationCollectionStruct->setCollectedFieldValue('my_value_2', new TextLineValue("My value 2"));
        $event = new InformationCollected(new DataWrapper($informationCollectionStruct, $contentType, $location));

        $this->twig->expects($this->once())
            ->method('load')
            ->willReturn($templateWrapper);

        $value = $this->factory->build($event);

        $this->assertInstanceOf(EmailData::class, $value);
        $this->assertEquals('recipient@template.com', $value->getRecipient());
        $this->assertEquals('sender@template.com', $value->getSender());
        $this->assertEquals('My custom subject', $value->getSubject());
        $this->assertEquals('My email body', $value->getBody());
    }

    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\MissingEmailBlockException
     * @expectedExceptionMessage Missing email block in index template, currently there is foo available.
     */
    public function testBuildingWithNoEmailBlockInTemplate()
    {
        $twig = new Twig_Environment(
            new Twig_Loader_Array(
                array(
                    'index' => '{% block foo %}{% endblock %}',
                )
            )
        );

        $this->configResolver->expects($this->once())
            ->method('getParameter')
            ->with('action_config.email', 'netgen_information_collection')
            ->willReturn($this->config);

        $this->factory = new EmailDataFactory(
            $this->configResolver,
            $this->translationHelper,
            $this->fieldHelper,
            $this->contentService,
            $this->twig
        );

        $templateWrapper = new Twig_TemplateWrapper($twig, $twig->loadTemplate('index'));

        $recipientField = new Field(array(
            'value' => new EmailValue('recipient@test.com'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'recipient',
        ));

        $senderField = new Field(array(
            'value' => new EmailValue('sender@test.com'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'sender',
        ));

        $subjectField = new Field(array(
            'value' => new TextLineValue('subject test'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'subject',
        ));

        $content = new Content(array(
            'internalFields' => array(
                $recipientField, $senderField, $subjectField,
            ),
            'versionInfo' => $this->versionInfo,
        ));

        $this->fieldHelper->expects($this->never())
            ->method('isFieldEmpty');

        $this->translationHelper->expects($this->never())
            ->method('getTranslatedField');

        $this->contentService->expects($this->once())
            ->method('loadContent')
            ->with(123)
            ->willReturn($content);

        $location = new Location(
            array(
                'id' => 12345,
                'contentInfo' => new ContentInfo(array('id' => 123)),
            )
        );

        $contentType = new ContentType(array(
            'identifier' => 'test',
            'fieldDefinitions' => array(),
        ));

        $informationCollectionStruct = new InformationCollectionStruct();
        $informationCollectionStruct->setCollectedFieldValue('my_value_1', new TextLineValue("My value 1"));
        $informationCollectionStruct->setCollectedFieldValue('my_value_2', new TextLineValue("My value 2"));
        $event = new InformationCollected(new DataWrapper($informationCollectionStruct, $contentType, $location));

        $this->twig->expects($this->once())
            ->method('load')
            ->willReturn($templateWrapper);

        $value = $this->factory->build($event);

        $this->assertInstanceOf(EmailData::class, $value);
        $this->assertEquals('recipient@test.com', $value->getRecipient());
        $this->assertEquals('sender@test.com', $value->getSender());
        $this->assertEquals('subject test', $value->getSubject());
        $this->assertEquals('body test', $value->getBody());
    }

    public function testBuildingWithSenderRecipientAndSubjectFromConfiguration()
    {
        $twig = new Twig_Environment(
            new Twig_Loader_Array(
                array(
                    'index' => '{% block email %}{% endblock %}',
                )
            )
        );

        $templateWrapper = new Twig_TemplateWrapper($twig, $twig->loadTemplate('index'));

        $this->configResolver->expects($this->once())
            ->method('getParameter')
            ->with('action_config.email', 'netgen_information_collection')
            ->willReturn($this->config);

        $this->factory = new EmailDataFactory(
            $this->configResolver,
            $this->translationHelper,
            $this->fieldHelper,
            $this->contentService,
            $this->twig
        );

        $recipientField = new Field(array(
            'value' => new EmailValue('recipient@test.com'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'recipient',
        ));

        $senderField = new Field(array(
            'value' => new EmailValue('sender@test.com'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'sender',
        ));

        $subjectField = new Field(array(
            'value' => new TextLineValue('subject test'),
            'languageCode' => 'eng_GB',
            'fieldDefIdentifier' => 'subject',
        ));

        $content = new Content(array(
            'internalFields' => array(
                $recipientField, $senderField, $subjectField,
            ),
            'versionInfo' => $this->versionInfo,
        ));

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

        $this->contentService->expects($this->once())
            ->method('loadContent')
            ->with(123)
            ->willReturn($content);

        $location = new Location(
            array(
                'id' => 12345,
                'contentInfo' => new ContentInfo(array('id' => 123)),
            )
        );

        $contentType = new ContentType(array(
            'identifier' => 'test',
            'fieldDefinitions' => array(),
        ));

        $informationCollectionStruct = new InformationCollectionStruct();
        $informationCollectionStruct->setCollectedFieldValue('my_value_1', new TextLineValue("My value 1"));
        $informationCollectionStruct->setCollectedFieldValue('my_value_2', new TextLineValue("My value 2"));
        $event = new InformationCollected(new DataWrapper($informationCollectionStruct, $contentType, $location));

        $this->twig->expects($this->once())
            ->method('load')
            ->willReturn($templateWrapper);

        $value = $this->factory->build($event);

        $this->assertInstanceOf(EmailData::class, $value);
        $this->assertEquals($this->config['default_variables']['recipient'], $value->getRecipient());
        $this->assertEquals($this->config['default_variables']['sender'], $value->getSender());
        $this->assertEquals($this->config['default_variables']['subject'], $value->getSubject());
    }
}
