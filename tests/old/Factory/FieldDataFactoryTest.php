<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Factory;

use Ibexa\Core\FieldType\TextLine\Value as TextValue;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\InformationCollectionBundle\Factory\FieldDataFactory;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomFieldHandlerInterface;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomLegacyFieldHandlerInterface;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\FieldHandlerRegistry;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FieldDataFactoryTest extends TestCase
{
    protected FieldDataFactory $factory;

    protected MockObject $registry;

    public function setUp(): void
    {
        $this->registry = $this->getMockBuilder(FieldHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array('handle'))
            ->getMock();

        $this->factory = new FieldDataFactory($this->registry);

        parent::setUp();
    }

    public function testGetLegacyValueWithoutCustomHandler(): void
    {
        $value = new TextValue('some value');
        $definition = new FieldDefinition(array(
            'id' => 123,
        ));

        $this->registry->expects($this->once())
            ->method('handle')
            ->with($value)
            ->willReturn(null);

        $data = $this->factory->getLegacyValue($value, $definition);

        $this->assertInstanceOf(LegacyData::class, $data);
        $this->assertEquals(123, $data->getContentClassAttributeId());
        $this->assertEquals(0.0, $data->getDataFloat());
        $this->assertEquals(0, $data->getDataInt());
        $this->assertEquals((string) $value, $data->getDataText());
    }

    public function testGetLegacyValueWithCustomHandler(): void
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

        $data = $this->factory->getLegacyValue($value, $definition);

        $this->assertInstanceOf(LegacyData::class, $data);
        $this->assertEquals(123, $data->getContentClassAttributeId());
        $this->assertEquals(0.0, $data->getDataFloat());
        $this->assertEquals(0, $data->getDataInt());
        $this->assertEquals((string) $value, $data->getDataText());
    }

    public function testGetLegacyValueWithCustomLegacyHandler(): void
    {
        $value = new TextValue('some value');
        $definition = new FieldDefinition(array(
            'id' => 123,
        ));

        $handler = $this->createMock(CustomLegacyFieldHandlerInterface::class);

        $handler->expects($this->once())
            ->method('getLegacyValue')
            ->with($value, $definition)
            ->willReturn(new LegacyData(123, 44.5, 1, 'test'));

        $this->registry->expects($this->once())
            ->method('handle')
            ->with($value)
            ->willReturn($handler);

        $data = $this->factory->getLegacyValue($value, $definition);

        $this->assertInstanceOf(LegacyData::class, $data);
        $this->assertEquals(123, $data->getContentClassAttributeId());
        $this->assertEquals(44.5, $data->getDataFloat());
        $this->assertEquals(1, $data->getDataInt());
        $this->assertEquals((string) 'test', $data->getDataText());
    }
}
