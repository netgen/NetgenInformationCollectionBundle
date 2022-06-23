<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\FieldHandler\Custom;

use Ibexa\Core\FieldType\Float\Value as FloatValue;
use Ibexa\Core\FieldType\Integer\Value as IntegerValue;
use Ibexa\Core\FieldType\Checkbox\Value as CheckboxValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition as CoreFieldDefinition;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\FloatFieldHandler;
use PHPUnit\Framework\TestCase;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomLegacyFieldHandlerInterface;

class FloatFieldHandlerTest extends TestCase
{
    protected CustomLegacyFieldHandlerInterface $handler;

    public function setUp(): void
    {
        $this->handler = new FloatFieldHandler();
    }

    public function testInstanceOfCustomLegacyFieldHandler(): void
    {
        $this->assertInstanceOf(CustomLegacyFieldHandlerInterface::class, $this->handler);
    }

    public function testSupportsHasValidBehaviour(): void
    {
        $this->assertFalse($this->handler->supports(new CheckboxValue(true)));
        $this->assertFalse($this->handler->supports(new IntegerValue(1)));
        $this->assertTrue($this->handler->supports(new FloatValue(2.0)));
    }

    public function testToString(): void
    {
        $fieldDefinition = $this->createMock(FieldDefinition::class);

        $this->assertEquals("2.5", $this->handler->toString(new FloatValue(2.5), $fieldDefinition));
        $this->assertEquals('55.7', $this->handler->toString(new FloatValue(55.7), $fieldDefinition));
    }

    public function testGetLegacyValue(): void
    {
        $fieldDefinition = new CoreFieldDefinition([
            'id' => 123,
        ]);

        $value = new FloatValue(56.6);
        $data = $this->handler->getLegacyValue($value, $fieldDefinition);

        $this->assertEquals($fieldDefinition->id, $data->getContentClassAttributeId());
        $this->assertEquals(0, $data->getDataInt());
        $this->assertEquals(56.6, $data->getDataFloat());
        $this->assertEquals('', $data->getDataText());

        $value = new FloatValue(34.2);
        $data = $this->handler->getLegacyValue($value, $fieldDefinition);

        $this->assertEquals($fieldDefinition->id, $data->getContentClassAttributeId());
        $this->assertEquals(0, $data->getDataInt());
        $this->assertEquals(34.2, $data->getDataFloat());
        $this->assertEquals('', $data->getDataText());
    }
}
