<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Action;

use Doctrine\DBAL\DBALException;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\TextLine\Value as TextLineValue;
use Ibexa\Core\Repository\ContentService;
use Ibexa\Core\Repository\Repository;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Netgen\Bundle\IbexaFormsBundle\Form\DataWrapper;
use Netgen\Bundle\InformationCollectionBundle\Form\Payload\InformationCollectionStruct;
use Netgen\Bundle\InformationCollectionBundle\Action\DatabaseAction;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Factory\FieldDataFactory;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionAttributeRepository;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DatabaseActionTest extends TestCase
{
    protected DatabaseAction $action;

    protected MockObject $factory;

    protected MockObject $repository;

    protected MockObject $secondRepository;

    protected MockObject $ibexaRepository;

    protected MockObject $contentType;

    protected MockObject $contentService;

    protected array $fields;

    protected InformationCollectionStruct $struct;

    protected LegacyData $legacyData;

    public function setUp(): void
    {
        $this->factory = $this->getMockBuilder(FieldDataFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getLegacyValue'))
            ->getMock();

        $this->repository = $this->getMockBuilder(EzInfoCollectionRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getInstance', 'save'))
            ->getMock();

        $this->secondRepository = $this->getMockBuilder(EzInfoCollectionAttributeRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getInstance', 'save'))
            ->getMock();

        $this->ibexaRepository = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getContentService', 'getCurrentUser'))
            ->getMock();

        $this->contentType = new ContentType(array(
            'fieldDefinitions' => array(
                new FieldDefinition(array(
                    'identifier' => 'some_field',
                    'id' => 321,
                )),
                new FieldDefinition(array(
                    'identifier' => 'some_field_1',
                    'id' => 654,
                )),
                new FieldDefinition(array(
                    'identifier' => 'some_field_2',
                    'id' => 987,
                )),
                new FieldDefinition(array(
                    'identifier' => 'some_field_3',
                    'id' => 12313,
                )),
            ),
        ));

        $this->contentService = $this->getMockBuilder(ContentService::class)
            ->disableOriginalConstructor()
            ->setMethods(array('loadContent'))
            ->getMock();

        $this->fields = array(
            new Field(array(
                'id' => 123,
                'fieldDefIdentifier' => 'some_field',
                'value' => new TextLineValue('some value'),
                'languageCode' => 'eng_GB',
            )),
            new Field(array(
                'id' => 456,
                'fieldDefIdentifier' => 'some_field_1',
                'value' => new TextLineValue('some value 1'),
                'languageCode' => 'eng_GB',
            )),
            new Field(array(
                'id' => 789,
                'fieldDefIdentifier' => 'some_field_2',
                'value' => new TextLineValue('some value 2'),
                'languageCode' => 'eng_GB',
            )),
            new Field(array(
                'id' => 13213,
                'fieldDefIdentifier' => 'some_field_3',
                'value' => null,
                'languageCode' => 'eng_GB',
            )),
        );

        $this->struct = new InformationCollectionStruct();
        foreach ($this->fields as $field) {
            $this->struct->setCollectedFieldValue($field->fieldDefIdentifier, $field->value);
        }

        $this->legacyData = new LegacyData(123, 0, 0.0, 'some value');

        $this->action = new DatabaseAction($this->factory, $this->repository, $this->secondRepository, $this->ibexaRepository);
        parent::setUp();
    }

    public function testAct(): void
    {
        $location = new Location(array(
            'contentInfo' => new ContentInfo(array(
                'id' => 123,
            )),
        ));

        $content = new Content(array(
            'internalFields' => $this->fields,
            'versionInfo' => new VersionInfo(array(
                'contentInfo' => new ContentInfo(array(
                    'mainLanguageCode' => 'eng_GB',
                )),
            )),
        ));

        $dataWrapper = new DataWrapper($this->struct, $this->contentType, $location);
        $event = new InformationCollected($dataWrapper);

        $user = new User(array(
            'content' => new Content(array(
                'versionInfo' => new VersionInfo(array(
                    'contentInfo' => new ContentInfo(array(
                        'id' => 123,
                    )),
                )),
            )),
            'login' => 'login',
        ));

        $ezInfoCollection = new EzInfoCollection();
        $ezInfoCollectionAttribute = new EzInfoCollectionAttribute();

        $this->ibexaRepository->expects($this->once())
            ->method('getContentService')
            ->willReturn($this->contentService);

        $this->contentService->expects($this->once())
            ->method('loadContent')
            ->with(123)
            ->willReturn($content);

        $this->ibexaRepository->expects($this->once())
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

    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException
     */
    public function testActWithExceptionOnInformationCollectionRepository(): void
    {
        $location = new Location(array(
            'contentInfo' => new ContentInfo(array(
                'id' => 123,
            )),
        ));

        $content = new Content(array(
            'internalFields' => $this->fields,
            'versionInfo' => new VersionInfo(array(
                'contentInfo' => new ContentInfo(array(
                    'mainLanguageCode' => 'eng_GB',
                )),
            )),
        ));

        $dataWrapper = new DataWrapper($this->struct, $this->contentType, $location);
        $event = new InformationCollected($dataWrapper);

        $user = new User(array(
            'content' => new Content(array(
                'versionInfo' => new VersionInfo(array(
                    'contentInfo' => new ContentInfo(array(
                        'id' => 123,
                    )),
                )),
            )),
            'login' => 'login',
        ));

        $ezInfoCollection = new EzInfoCollection();

        $this->ibexaRepository->expects($this->once())
            ->method('getContentService')
            ->willReturn($this->contentService);

        $this->contentService->expects($this->once())
            ->method('loadContent')
            ->with(123)
            ->willReturn($content);

        $this->ibexaRepository->expects($this->once())
            ->method('getCurrentUser')
            ->willReturn($user);

        $this->repository->expects($this->once())
            ->method('getInstance')
            ->willReturn($ezInfoCollection);

        $this->repository->expects($this->once())
            ->method('save')
            ->with($ezInfoCollection)
            ->willThrowException(new DBALException());

        $this->factory->expects($this->never())
            ->method('getLegacyValue');

        $this->secondRepository->expects($this->never())
            ->method('getInstance');

        $this->secondRepository->expects($this->never())
            ->method('save');

        $this->action->act($event);
    }

    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException
     */
    public function testActWithExceptionOnInformationCollectionAttributeRepository(): void
    {
        $location = new Location(array(
            'contentInfo' => new ContentInfo(array(
                'id' => 123,
            )),
        ));

        $content = new Content(array(
            'internalFields' => $this->fields,
            'versionInfo' => new VersionInfo(array(
                'contentInfo' => new ContentInfo(array(
                    'mainLanguageCode' => 'eng_GB',
                )),
            )),
        ));

        $dataWrapper = new DataWrapper($this->struct, $this->contentType, $location);
        $event = new InformationCollected($dataWrapper);

        $user = new User(array(
            'content' => new Content(array(
                'versionInfo' => new VersionInfo(array(
                    'contentInfo' => new ContentInfo(array(
                        'id' => 123,
                    )),
                )),
            )),
            'login' => 'login',
        ));

        $ezInfoCollection = new EzInfoCollection();
        $ezInfoCollectionAttribute = new EzInfoCollectionAttribute();

        $this->ibexaRepository->expects($this->once())
            ->method('getContentService')
            ->willReturn($this->contentService);

        $this->contentService->expects($this->once())
            ->method('loadContent')
            ->with(123)
            ->willReturn($content);

        $this->ibexaRepository->expects($this->once())
            ->method('getCurrentUser')
            ->willReturn($user);

        $this->repository->expects($this->once())
            ->method('getInstance')
            ->willReturn($ezInfoCollection);

        $this->repository->expects($this->once())
            ->method('save')
            ->with($ezInfoCollection);

        $this->factory->expects($this->exactly(1))
            ->method('getLegacyValue')
            ->withAnyParameters()
            ->willReturn($this->legacyData);

        $this->secondRepository->expects($this->exactly(1))
            ->method('getInstance')
            ->willReturn($ezInfoCollectionAttribute);

        $this->secondRepository->expects($this->once())
            ->method('save')
            ->willThrowException(new DBALException());

        $this->action->act($event);
    }
}
