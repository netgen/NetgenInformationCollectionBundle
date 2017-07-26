<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Factory;

use eZ\Publish\Core\FieldType\TextLine\Value as TextValue;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\InformationCollectionBundle\Factory\FieldDataFactory;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomFieldHandlerInterface;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\FieldHandlerRegistry;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;
use PHPUnit\Framework\TestCase;

class FieldDataFactoryTest extends TestCase
{
    /**
     * @var FieldDataFactory
     */
    protected $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = $this->getMockBuilder(FieldHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array('handle'))
            ->getMock();

        $this->factory = new FieldDataFactory($this->registry);

        parent::setUp();
    }

    public function testGetLegacyValueWithoutCustomHandler()
    {
        $value = new TextValue('some value');
        $definition = new FieldDefinition(array(
            'id' => 123,
        ));

        $this->registry->expects($this->once())
            ->method('handle')
            ->with($value)
            ->willReturn(null);

        /** @var LegacyData $data */
        $data = $this->factory->getLegacyValue($value, $definition);

        $this->assertInstanceOf(LegacyData::class, $data);
        $this->assertEquals(123, $data->contentClassAttributeId);
        $this->assertEquals(0.0, $data->dataFloat);
        $this->assertEquals(0, $data->dataInt);
        $this->assertEquals((string) $value, $data->dataText);
    }

    public function testGetLegacyValueWithCustomHandler()
    {
        $value = new TextValue('some value');
        $definition = new FieldDefinition(array(
            'id' => 123,
        ));

        $handler = $this->getMockBuilder(CustomFieldHandlerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('toString', 'supports'))
            ->getMock();

        $handler->expects($this->once())
            ->method('toString')
            ->with($value, $definition)
            ->willReturn((string) $value);

        $this->registry->expects($this->once())
            ->method('handle')
            ->with($value)
            ->willReturn($handler);

        /** @var LegacyData $data */
        $data = $this->factory->getLegacyValue($value, $definition);

        $this->assertInstanceOf(LegacyData::class, $data);
        $this->assertEquals(123, $data->contentClassAttributeId);
        $this->assertEquals(0.0, $data->dataFloat);
        $this->assertEquals(0, $data->dataInt);
        $this->assertEquals((string) $value, $data->dataText);
    }
}
