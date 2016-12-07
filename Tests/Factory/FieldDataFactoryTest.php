<?php
namespace Netgen\Bundle\InformationCollectionBundle\Tests\Factory;

use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\InformationCollectionBundle\Factory\FieldDataFactory;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomFieldHandlerInterface;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\FieldHandlerRegistry;
use eZ\Publish\Core\FieldType\TextLine\Value as TextValue;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class FieldDataFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var FieldDataFactory
     */
    protected $factory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = $this->getMockBuilder(FieldHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();

        $this->factory = new FieldDataFactory($this->registry);

        parent::setUp();
    }

    public function testGetLegacyValueWithoutCustomHandler()
    {
        $value = new TextValue('some value');
        $definition = new FieldDefinition([
            'id' => 123,
        ]);

        $this->registry->expects($this->once())
            ->method('handle')
            ->with($value)
            ->willReturn(null);

        $data = $this->factory->getLegacyValue($value, $definition);

        $this->assertInstanceOf(LegacyData::class, $data);
        $this->assertEquals(123, $data->getContentClassAttributeId());
        $this->assertEquals(0.0, $data->getDataFloat());
        $this->assertEquals(0, $data->getDataInt());
        $this->assertEquals((string)$value, $data->getDataText());
    }

    public function testGetLegacyValueWithCustomHandler()
    {
        $value = new TextValue('some value');
        $definition = new FieldDefinition([
            'id' => 123,
        ]);

        $handler = $this->getMockBuilder(CustomFieldHandlerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['toString', 'supports'])
            ->getMock();

        $handler->expects($this->once())
            ->method('toString')
            ->with($value, $definition)
            ->willReturn((string)$value);

        $this->registry->expects($this->once())
            ->method('handle')
            ->with($value)
            ->willReturn($handler);

        $data = $this->factory->getLegacyValue($value, $definition);

        $this->assertInstanceOf(LegacyData::class, $data);
        $this->assertEquals(123, $data->getContentClassAttributeId());
        $this->assertEquals(0.0, $data->getDataFloat());
        $this->assertEquals(0, $data->getDataInt());
        $this->assertEquals((string)$value, $data->getDataText());
    }
}