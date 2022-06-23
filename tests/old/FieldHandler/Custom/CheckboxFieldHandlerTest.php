<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\FieldHandler\Custom;

use Ibexa\Core\FieldType\Float\Value as FloatValue;
use Ibexa\Core\FieldType\Integer\Value as IntegerValue;
use Ibexa\Core\FieldType\Checkbox\Value as CheckboxValue;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CheckboxFieldHandler;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition as CoreFieldDefinition;
use PHPUnit\Framework\TestCase;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomLegacyFieldHandlerInterface;

class CheckboxFieldHandlerTest extends TestCase
{
    protected CustomLegacyFieldHandlerInterface $handler;

    public function setUp(): void
    {
        $this->handler = new CheckboxFieldHandler();
    }

    public function testInstanceOfCustomLegacyFieldHandler(): void
    {
        $this->assertInstanceOf(CustomLegacyFieldHandlerInterface::class, $this->handler);
    }

    public function testSupportsHasValidBehaviour(): void
    {
        $this->assertTrue($this->handler->supports(new CheckboxValue(true)));
        $this->assertFalse($this->handler->supports(new IntegerValue(1)));
        $this->assertFalse($this->handler->supports(new FloatValue(2.0)));
    }

    public function testToString(): void
    {
        $fieldDefinition = $this->createMock(FieldDefinition::class);
        $this->assertEquals('1', $this->handler->toString(new CheckboxValue(true), $fieldDefinition));
        $this->assertEquals('0', $this->handler->toString(new CheckboxValue(false), $fieldDefinition));
    }

    public function testGetLegacyValue(): void
    {
        $fieldDefinition = new CoreFieldDefinition([
            'id' => 123,
        ]);

        $value = new CheckboxValue(true);
        $data = $this->handler->getLegacyValue($value, $fieldDefinition);

        $this->assertEquals($fieldDefinition->id, $data->getContentClassAttributeId());
        $this->assertEquals(1, $data->getDataInt());
        $this->assertEquals(0, $data->getDataFloat());
        $this->assertEquals('', $data->getDataText());

        $value = new CheckboxValue(false);
        $data = $this->handler->getLegacyValue($value, $fieldDefinition);

        $this->assertEquals($fieldDefinition->id, $data->getContentClassAttributeId());
        $this->assertEquals(0, $data->getDataInt());
        $this->assertEquals(0, $data->getDataFloat());
        $this->assertEquals('', $data->getDataText());
    }
}
