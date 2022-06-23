<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\FieldHandler\Custom;

use Ibexa\Core\FieldType\Float\Value as FloatValue;
use Ibexa\Core\FieldType\Integer\Value as IntegerValue;
use Ibexa\Core\FieldType\Checkbox\Value as CheckboxValue;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\IntegerFieldHandler;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition as CoreFieldDefinition;
use PHPUnit\Framework\TestCase;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomLegacyFieldHandlerInterface;

class IntegerFieldHandlerTest extends TestCase
{
    protected CustomLegacyFieldHandlerInterface $handler;

    public function setUp(): void
    {
        $this->handler = new IntegerFieldHandler();
    }

    public function testInstanceOfCustomLegacyFieldHandler(): void
    {
        $this->assertInstanceOf(CustomLegacyFieldHandlerInterface::class, $this->handler);
    }

    public function testSupportsHasValidBehaviour(): void
    {
        $this->assertFalse($this->handler->supports(new CheckboxValue(true)));
        $this->assertTrue($this->handler->supports(new IntegerValue(1)));
        $this->assertFalse($this->handler->supports(new FloatValue(2.0)));
    }

    public function testToString(): void
    {
        $fieldDefinition = $this->createMock(FieldDefinition::class);
        $this->assertEquals('55', $this->handler->toString(new IntegerValue(55), $fieldDefinition));
        $this->assertEquals('32', $this->handler->toString(new IntegerValue(32), $fieldDefinition));
    }

    public function testGetLegacyValue(): void
    {
        $fieldDefinition = new CoreFieldDefinition([
            'id' => 123,
        ]);

        $value = new IntegerValue(23);
        $data = $this->handler->getLegacyValue($value, $fieldDefinition);

        $this->assertEquals($fieldDefinition->id, $data->getContentClassAttributeId());
        $this->assertEquals(23, $data->getDataInt());
        $this->assertEquals(0, $data->getDataFloat());
        $this->assertEquals('', $data->getDataText());

        $value = new IntegerValue(55);
        $data = $this->handler->getLegacyValue($value, $fieldDefinition);

        $this->assertEquals($fieldDefinition->id, $data->getContentClassAttributeId());
        $this->assertEquals(55, $data->getDataInt());
        $this->assertEquals(0, $data->getDataFloat());
        $this->assertEquals('', $data->getDataText());
    }
}
