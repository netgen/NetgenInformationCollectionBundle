<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Action;

use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\Repository\ContentService;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\InformationCollectionBundle\Action\DatabaseAction;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute;
use Netgen\Bundle\InformationCollectionBundle\Factory\FieldDataFactory;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionAttributeRepository;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\User\User;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class DatabaseActionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DatabaseAction
     */
    protected $action;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $factory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $secondRepository;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $ezRepository;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentType;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentService;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var InformationCollectionStruct
     */
    protected $struct;

    /**
     * @var LegacyData
     */
    protected $legacyData;

    public function setUp()
    {
        $this->factory = $this->getMockBuilder(FieldDataFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLegacyValue'])
            ->getMock();

        $this->repository = $this->getMockBuilder(EzInfoCollectionRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['getInstance', 'save'])
            ->getMock();

        $this->secondRepository = $this->getMockBuilder(EzInfoCollectionAttributeRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['getInstance', 'save'])
            ->getMock();

        $this->ezRepository = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->setMethods(['getContentService', 'getCurrentUser'])
            ->getMock();

        $this->contentType = new ContentType([
            'fieldDefinitions' => [
                new FieldDefinition([
                    'identifier' => 'some_field',
                    'id' => 321,
                ]),
                new FieldDefinition([
                    'identifier' => 'some_field_1',
                    'id' => 654,
                ]),
                new FieldDefinition([
                    'identifier' => 'some_field_2',
                    'id' => 987,
                ]),
            ],
        ]);

        $this->contentService = $this->getMockBuilder(ContentService::class)
            ->disableOriginalConstructor()
            ->setMethods(['loadContent'])
            ->getMock();

        $this->fields = [
            new Field([
                'id' => 123,
                'fieldDefIdentifier' => 'some_field',
                'value' => new TextLineValue("some value"),
                'languageCode' => 'eng_GB',
            ]),
            new Field([
                'id' => 456,
                'fieldDefIdentifier' => 'some_field_1',
                'value' => new TextLineValue("some value 1"),
                'languageCode' => 'eng_GB',
            ]),
            new Field([
                'id' => 789,
                'fieldDefIdentifier' => 'some_field_2',
                'value' => new TextLineValue("some value 2"),
                'languageCode' => 'eng_GB',
            ]),
        ];

        $this->struct = new InformationCollectionStruct();
        foreach ($this->fields as $field) {
            $this->struct->setCollectedFieldValue($field->fieldDefIdentifier, $field->value);
        }

        $this->legacyData = new LegacyData(123, 0, 0.0, 'some value');

        $this->action = new DatabaseAction($this->factory, $this->repository, $this->secondRepository, $this->ezRepository);
        parent::setUp();
    }

    public function testAct()
    {
        $location = new Location([
            'contentInfo' => new ContentInfo([
                'id' => 123,
            ]),
        ]);

        $content = new Content([
            'internalFields' => $this->fields,
            'versionInfo' => new VersionInfo([
                'contentInfo' => new ContentInfo([
                    'mainLanguageCode' => 'eng_GB'
                ]),
            ]),
        ]);

        $dataWrapper = new DataWrapper($this->struct, $this->contentType, $location);
        $event = new InformationCollected($dataWrapper);

        $user = new User([
            'content' => new Content([
                'versionInfo' => new VersionInfo([
                    'contentInfo' => new ContentInfo([
                        'id' => 123,
                    ]),
                ]),
            ]),
            'login' => 'login',
        ]);

        $ezInfoCollection = new EzInfoCollection();
        $ezInfoCollectionAttribute = new EzInfoCollectionAttribute();

        $this->ezRepository->expects($this->once())
            ->method('getContentService')
            ->willReturn($this->contentService);

        $this->contentService->expects($this->once())
            ->method('loadContent')
            ->with(123)
            ->willReturn($content);

        $this->ezRepository->expects($this->once())
            ->method('getCurrentUser')
            ->willReturn($user);

        $this->repository->expects($this->once())
            ->method('getInstance')
            ->willReturn($ezInfoCollection);

        $this->repository->expects($this->once())
            ->method('save')
            ->with($ezInfoCollection);

        $this->factory->expects($this->exactly(3))
            ->method('getLegacyValue')
            ->withAnyParameters()
            ->willReturn($this->legacyData);

        $this->secondRepository->expects($this->exactly(3))
            ->method('getInstance')
            ->willReturn($ezInfoCollectionAttribute);

        $this->secondRepository->expects($this->exactly(3))
            ->method('save');

        $this->action->act($event);
    }
}