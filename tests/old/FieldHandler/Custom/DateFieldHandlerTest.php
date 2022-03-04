<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\FieldHandler\Custom;

use Ibexa\Core\FieldType\Float\Value as FloatValue;
use Ibexa\Core\FieldType\Checkbox\Value as CheckboxValue;
use Ibexa\Core\FieldType\Date\Value as DateValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition as CoreFieldDefinition;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomLegacyFieldHandlerInterface;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\DateFieldHandler;
use PHPUnit\Framework\TestCase;
use DateTime;

class DateFieldHandlerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomLegacyFieldHandlerInterface
     */
    protected $handler;

    /**
     * @var \DateTime
     */
    protected $dt;

    public function setUp(): void
    {
        $this->handler = new DateFieldHandler();
        $this->dt = new DateTime();
        $this->dt->setTime(0, 0, 0);
    }

    public function testInstanceOfCustomLegacyFieldHandler(): void
    {
        $this->assertInstanceOf(CustomLegacyFieldHandlerInterface::class, $this->handler);
    }

    public function testSupportsHasValidBehaviour(): void
    {
        $this->assertFalse($this->handler->supports(new CheckboxValue(true)));
        $this->assertTrue($this->handler->supports(new DateValue($this->dt)));
        $this->assertFalse($this->handler->supports(new FloatValue(2.0)));
    }

    public function testToString(): void
    {
        $fieldDefinition = $this->createMock(FieldDefinition::class);

        $value = new DateValue($this->dt);

        $this->assertEquals($this->dt->format('l d F Y'), $this->handler->toString($value, $fieldDefinition));
    }

    public function testGetLegacyValue(): void
    {
        $fieldDefinition = new CoreFieldDefinition([
            'id' => 123,
        ]);

        $value = new DateValue($this->dt);
        $data = $this->handler->getLegacyValue($value, $fieldDefinition);

        $this->assertEquals($fieldDefinition->id, $data->getContentClassAttributeId());
        $this->assertEquals($this->dt->getTimestamp(), $data->getDataInt());
        $this->assertEquals(0, $data->getDataFloat());
        $this->assertEquals('', $data->getDataText());
    }
}
