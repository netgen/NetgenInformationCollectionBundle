<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\FieldHandler\Custom;

use Ibexa\Core\FieldType\Float\Value as FloatValue;
use Ibexa\Core\FieldType\Checkbox\Value as CheckboxValue;
use Ibexa\Core\FieldType\DateAndTime\Value as DateAndTimeValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition as CoreFieldDefinition;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomLegacyFieldHandlerInterface;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\DateAndTimeFieldHandler;
use PHPUnit\Framework\TestCase;
use DateTime;

class DateAndTimeFieldHandlerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomLegacyFieldHandlerInterface
     */
    protected $handler;

    /**
     * @var \DateTime
     */
    protected $dt;

    public function setUp()
    {
        $this->handler = new DateAndTimeFieldHandler();
        $this->dt = new DateTime();
    }

    public function testInstanceOfCustomLegacyFieldHandler()
    {
        $this->assertInstanceOf(CustomLegacyFieldHandlerInterface::class, $this->handler);
    }

    public function testSupportsHasValidBehaviour()
    {
        $this->assertFalse($this->handler->supports(new CheckboxValue(true)));
        $this->assertTrue($this->handler->supports(new DateAndTimeValue($this->dt)));
        $this->assertFalse($this->handler->supports(new FloatValue(2.0)));
    }

    public function testToString()
    {
        $fieldDefinition = $this->createMock(FieldDefinition::class);

        $value = new DateAndTimeValue($this->dt);

        $this->assertEquals($this->dt->format('U'), $this->handler->toString($value, $fieldDefinition));
    }

    public function testGetLegacyValue()
    {
        $fieldDefinition = new CoreFieldDefinition([
            'id' => 123,
        ]);

        $value = new DateAndTimeValue($this->dt);
        $data = $this->handler->getLegacyValue($value, $fieldDefinition);

        $this->assertEquals($fieldDefinition->id, $data->getContentClassAttributeId());
        $this->assertEquals($this->dt->getTimestamp(), $data->getDataInt());
        $this->assertEquals(0, $data->getDataFloat());
        $this->assertEquals('', $data->getDataText());
    }
}
