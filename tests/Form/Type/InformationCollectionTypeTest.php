<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Form\Type;

use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use Netgen\Bundle\InformationCollectionBundle\Form\Payload\InformationCollectionStruct;
use Netgen\Bundle\InformationCollectionBundle\Form\Type\InformationCollectionType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilder;

class InformationCollectionTypeTest extends TestCase
{
    public function testItExtendsAbstractType()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);
        $this->assertInstanceOf(AbstractType::class, $infoCollectionType);
    }

    public function testGetName()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);

        $this->assertEquals('netgen_information_collection', $infoCollectionType->getName());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Data must be an instance of Netgen\EzFormsBundle\Form\DataWrapper
     */
    public function testBuildFormWithoutDataWrapperMustThrowException()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $options = array('data' => 'data');

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);
        $infoCollectionType->buildForm($formBuilder, $options);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Data payload must be an instance of Netgen\Bundle\InformationCollectionBundle\Form\Payload\InformationCollectionStruct
     */
    public function testBuildFormDataWrapperPayloadMustBeInformationCollectionStruct()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $options = array('data' => new DataWrapper('payload'));

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);
        $infoCollectionType->buildForm($formBuilder, $options);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Data definition must be an instance of eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    public function testBuildFormDataWrapperDefinitionMustBeContentType()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $infoStruct = new InformationCollectionStruct();

        $options = array('data' => new DataWrapper($infoStruct));

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);
        $infoCollectionType->buildForm($formBuilder, $options);
    }

    public function testBuildFormIfFieldIsNotInfoCollectorSkipIt()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('setDataMapper'))
            ->getMock();

        $formBuilder->expects($this->once())
            ->method('setDataMapper');

        $infoStruct = new InformationCollectionStruct();

        $contentType = new ContentType(
            array(
                'id' => 123,
                'fieldDefinitions' => array(
                    new FieldDefinition(
                        array(
                            'id' => 'id',
                            'identifier' => 'identifier',
                            'isInfoCollector' => false,
                        )
                    ),
                ),
            )
        );

        $options = array('data' => new DataWrapper($infoStruct, $contentType));

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);
        $infoCollectionType->buildForm($formBuilder, $options);
    }

    public function testBuildFormIfFieldUserSkipIt()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('setDataMapper'))
            ->getMock();

        $formBuilder->expects($this->once())
            ->method('setDataMapper');

        $infoStruct = new InformationCollectionStruct();

        $contentType = new ContentType(
            array(
                'id' => 123,
                'fieldDefinitions' => array(
                    new FieldDefinition(
                        array(
                            'id' => 'id',
                            'identifier' => 'identifier',
                            'fieldTypeIdentifier' => 'ezuser',
                            'isInfoCollector' => false,
                        )
                    ),
                ),
            )
        );

        $options = array('data' => new DataWrapper($infoStruct, $contentType));

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);
        $infoCollectionType->buildForm($formBuilder, $options);
    }

    public function testBuildForm()
    {
        $fieldTypeHandler = $this->getMockBuilder(FieldTypeHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(array('buildFieldCreateForm'))
            ->getMockForAbstractClass();

        $fieldTypeHandler->expects($this->once())
            ->method('buildFieldCreateForm');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();

        $handlerRegistry->expects($this->once())
            ->willReturn($fieldTypeHandler)
            ->method('get');

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $infoStruct = new InformationCollectionStruct();

        $contentType = new ContentType(
            array(
                'id' => 123,
                'mainLanguageCode' => 'eng-GB',
                'names' => array('eng-GB'),
                'fieldDefinitions' => array(
                    new FieldDefinition(
                        array(
                            'id' => 'id',
                            'identifier' => 'identifier',
                            'isInfoCollector' => true,
                        )
                    ),
                ),
            )
        );

        $options = array('data' => new DataWrapper($infoStruct, $contentType));

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);
        $infoCollectionType->setLanguages(array('eng-GB'));
        $infoCollectionType->buildForm($formBuilder, $options);
    }

    public function testBuildFormTriggerMainLanguageCodeFromContentType()
    {
        $fieldTypeHandler = $this->getMockBuilder(FieldTypeHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(array('buildFieldCreateForm'))
            ->getMockForAbstractClass();

        $fieldTypeHandler->expects($this->once())
            ->method('buildFieldCreateForm');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();

        $handlerRegistry->expects($this->once())
            ->willReturn($fieldTypeHandler)
            ->method('get');

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $infoStruct = new InformationCollectionStruct();

        $contentType = new ContentType(
            array(
                'id' => 123,
                'mainLanguageCode' => 'eng-GB',
                'names' => array('fre-FR' => 'fre-FR'),
                'fieldDefinitions' => array(
                    new FieldDefinition(
                        array(
                            'id' => 'id',
                            'identifier' => 'identifier',
                            'isInfoCollector' => true,
                        )
                    ),
                ),
            )
        );

        $options = array('data' => new DataWrapper($infoStruct, $contentType));

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);
        $infoCollectionType->setLanguages(array('fre-CH'));
        $infoCollectionType->buildForm($formBuilder, $options);
    }
}
