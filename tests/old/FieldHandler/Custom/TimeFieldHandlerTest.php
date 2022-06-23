<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\FieldHandler\Custom;

use Ibexa\Core\FieldType\Float\Value as FloatValue;
use Ibexa\Core\FieldType\Checkbox\Value as CheckboxValue;
use Ibexa\Core\FieldType\Time\Value as TimeValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition as CoreFieldDefinition;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomLegacyFieldHandlerInterface;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\TimeFieldHandler;
use PHPUnit\Framework\TestCase;
use DateTime;

class TimeFieldHandlerTest extends TestCase
{
    protected CustomLegacyFieldHandlerInterface $handler;

    protected DateTime $dt;

    protected TimeValue $value;

    public function setUp(): void
    {
        $this->handler = new TimeFieldHandler();
        $this->dt = new DateTime();
        $this->value = TimeValue::fromDateTime($this->dt);
    }

    public function testInstanceOfCustomLegacyFieldHandler(): void
    {
        $this->assertInstanceOf(CustomLegacyFieldHandlerInterface::class, $this->handler);
    }

    public function testSupportsHasValidBehaviour(): void
    {
        $this->assertFalse($this->handler->supports(new CheckboxValue(true)));
        $this->assertTrue($this->handler->supports($this->value));
        $this->assertFalse($this->handler->supports(new FloatValue(2.0)));
    }

    public function testToString(): void
    {
        $fieldDefinition = $this->createMock(FieldDefinition::class);

        $this->assertEquals($this->dt->format('H:i:s'), $this->handler->toString($this->value, $fieldDefinition));
    }

    public function testGetLegacyValue(): void
    {
        $fieldDefinition = new CoreFieldDefinition([
            'id' => 123,
        ]);

        $data = $this->handler->getLegacyValue($this->value, $fieldDefinition);

        $time = $this->dt->getTimestamp() - $this->dt->setTime(0, 0, 0)->getTimestamp();
        $this->assertEquals($fieldDefinition->id, $data->getContentClassAttributeId());
        $this->assertEquals($time, $data->getDataInt());
        $this->assertEquals(0, $data->getDataFloat());
        $this->assertEquals('', $data->getDataText());
    }
}
