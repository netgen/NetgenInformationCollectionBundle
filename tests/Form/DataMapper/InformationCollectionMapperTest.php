<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Form\DataMapper;

use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\InformationCollectionBundle\Form\DataMapper\InformationCollectionMapper;
use Netgen\Bundle\InformationCollectionBundle\Form\Payload\InformationCollectionStruct;
use PHPUnit\Framework\TestCase;

class InformationCollectionMapperTest extends TestCase
{
    /**
     * @var InformationCollectionMapper
     */
    private $mapper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $handler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $propertyAccessor;

    protected function setUp()
    {
        $this->propertyAccessor = $this->getMockBuilder('Symfony\Component\PropertyAccess\PropertyAccessorInterface')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $this->registry = $this->getMockBuilder('Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $this->handler = $this->getMockBuilder('Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $this->mapper = new InformationCollectionMapper($this->registry, $this->propertyAccessor);
    }

    public function testInstanceOfDataMapper()
    {
        $this->assertInstanceOf('\Netgen\Bundle\EzFormsBundle\Form\DataMapper', $this->mapper);
    }

    public function testMapFormsToData()
    {
        $contentType = new ContentType(
            array(
                'id' => 123,
                'fieldDefinitions' => array(
                    new FieldDefinition(
                        array(
                            'id' => 'id',
                            'identifier' => 'name',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new TextLineValue('Some name'),
                        )
                    ),
                ),
            )
        );

        $this->registry->expects($this->once())
            ->method('get')
            ->with('eztext')
            ->will($this->returnValue($this->handler));

        $this->handler->expects($this->once())
            ->method('convertFieldValueFromForm')
            ->willReturn(new TextLineValue('Some name'));

        $infoStruct = new InformationCollectionStruct();
        $data = new DataWrapper($infoStruct, $contentType);

        $config = $this->getMockBuilder('Symfony\Component\Form\FormConfigBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped'))
            ->getMock();

        $config->expects($this->once())
            ->willReturn(true)
            ->method('getMapped');

        $propertyPath = $this->getMockBuilder('Symfony\Component\PropertyAccess\PropertyPathInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('__toString'))
            ->getMockForAbstractClass();

        $propertyPath->expects($this->once())
            ->willReturn('name')
            ->method('__toString');

        $form = $this->getForm();

        $form->expects($this->once())
            ->willReturn(true)
            ->method('isSubmitted');

        $form->expects($this->once())
            ->willReturn(true)
            ->method('isSynchronized');

        $form->expects($this->once())
            ->willReturn(false)
            ->method('isDisabled');

        $form->expects($this->once())
            ->willReturn('Some name')
            ->method('getData');

        $form->expects($this->once())
            ->willReturn($config)
            ->method('getConfig');

        $form->expects($this->once())
            ->willReturn($propertyPath)
            ->method('getPropertyPath');

        $this->mapper->mapFormsToData(array($form), $data);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testMapFormsToDataWithoutValidFieldDefinition()
    {
        $contentType = new ContentType(
            array(
                'id' => 123,
                'fieldDefinitions' => array(
                    new FieldDefinition(
                        array(
                            'id' => 'id',
                            'identifier' => 'test',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new TextLineValue('Some name'),
                        )
                    ),
                ),
            )
        );

        $infoStruct = new InformationCollectionStruct();
        $data = new DataWrapper($infoStruct, $contentType);

        $config = $this->getMockBuilder('Symfony\Component\Form\FormConfigBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped'))
            ->getMock();

        $config->expects($this->once())
            ->willReturn(true)
            ->method('getMapped');

        $propertyPath = $this->getMockBuilder('Symfony\Component\PropertyAccess\PropertyPathInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('__toString'))
            ->getMockForAbstractClass();

        $propertyPath->expects($this->once())
            ->willReturn('name')
            ->method('__toString');

        $form = $this->getForm();

        $form->expects($this->once())
            ->willReturn(true)
            ->method('isSubmitted');

        $form->expects($this->once())
            ->willReturn(true)
            ->method('isSynchronized');

        $form->expects($this->once())
            ->willReturn(false)
            ->method('isDisabled');

        $form->expects($this->once())
            ->willReturn($config)
            ->method('getConfig');

        $form->expects($this->once())
            ->willReturn($propertyPath)
            ->method('getPropertyPath');

        $this->mapper->mapFormsToData(array($form), $data);
    }

    public function testMapDataToForms()
    {
        $contentType = new ContentType(
            array(
                'id' => 123,
                'fieldDefinitions' => array(
                    new FieldDefinition(
                        array(
                            'id' => 'id',
                            'identifier' => 'name',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new TextLineValue('Some name'),
                        )
                    ),
                ),
            )
        );

        $this->registry->expects($this->once())
            ->method('get')
            ->with('eztext')
            ->will($this->returnValue($this->handler));

        $this->handler->expects($this->once())
            ->method('convertFieldValueToForm')
            ->willReturn('Some name');

        $infoStruct = new InformationCollectionStruct();
        $data = new DataWrapper($infoStruct, $contentType);

        $config = $this->getMockBuilder('Symfony\Component\Form\FormConfigBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped'))
            ->getMock();

        $config->expects($this->once())
            ->willReturn(true)
            ->method('getMapped');

        $propertyPath = $this->getMockBuilder('Symfony\Component\PropertyAccess\PropertyPathInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('__toString'))
            ->getMockForAbstractClass();

        $propertyPath->expects($this->once())
            ->willReturn('name')
            ->method('__toString');

        $form = $this->getForm();

        $form->expects($this->once())
            ->method('setData');

        $form->expects($this->once())
            ->willReturn($config)
            ->method('getConfig');

        $form->expects($this->once())
            ->willReturn($propertyPath)
            ->method('getPropertyPath');

        $this->mapper->mapDataToForms($data, array($form));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testMapDataToFormsWithInvalidFieldDefinition()
    {
        $contentType = new ContentType(
            array(
                'id' => 123,
                'fieldDefinitions' => array(
                    new FieldDefinition(
                        array(
                            'id' => 'id',
                            'identifier' => 'test',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new TextLineValue('Some name'),
                        )
                    ),
                ),
            )
        );

        $infoStruct = new InformationCollectionStruct();
        $data = new DataWrapper($infoStruct, $contentType);

        $config = $this->getMockBuilder('Symfony\Component\Form\FormConfigBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped'))
            ->getMock();

        $config->expects($this->once())
            ->willReturn(true)
            ->method('getMapped');

        $propertyPath = $this->getMockBuilder('Symfony\Component\PropertyAccess\PropertyPathInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('__toString'))
            ->getMockForAbstractClass();

        $propertyPath->expects($this->once())
            ->willReturn('name')
            ->method('__toString');

        $form = $this->getForm();

        $form->expects($this->once())
            ->willReturn($config)
            ->method('getConfig');

        $form->expects($this->once())
            ->willReturn($propertyPath)
            ->method('getPropertyPath');

        $this->mapper->mapDataToForms($data, array($form));
    }

    private function getForm()
    {
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array('getData', 'setData', 'getPropertyPath', 'getConfig', 'isSubmitted', 'isSynchronized', 'isDisabled'))
            ->getMock();

        return $form;
    }
}
