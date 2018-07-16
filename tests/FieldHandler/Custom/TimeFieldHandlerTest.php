<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\FieldHandler\Custom;

use eZ\Publish\Core\FieldType\Float\Value as FloatValue;
use eZ\Publish\Core\FieldType\Checkbox\Value as CheckboxValue;
use eZ\Publish\Core\FieldType\Time\Value as TimeValue;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition as CoreFieldDefinition;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomLegacyFieldHandlerInterface;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\TimeFieldHandler;
use PHPUnit\Framework\TestCase;
use DateTime;

class TimeFieldHandlerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomLegacyFieldHandlerInterface
     */
    protected $handler;

    /**
     * @var \DateTime
     */
    protected $dt;

    /**
     * @var \eZ\Publish\Core\FieldType\Time\Value
     */
    protected $value;

    public function setUp()
    {
        $this->handler = new TimeFieldHandler();
        $this->dt = new DateTime();
        $this->value = TimeValue::fromDateTime($this->dt);
    }

    public function testInstanceOfCustomLegacyFieldHandler()
    {
        $this->assertInstanceOf(CustomLegacyFieldHandlerInterface::class, $this->handler);
    }

    public function testSupportsHasValidBehaviour()
    {
        $this->assertFalse($this->handler->supports(new CheckboxValue(true)));
        $this->assertTrue($this->handler->supports($this->value));
        $this->assertFalse($this->handler->supports(new FloatValue(2.0)));
    }

    public function testToString()
    {
        $fieldDefinition = $this->createMock(FieldDefinition::class);

        $this->assertEquals($this->dt->format('H:i:s'), $this->handler->toString($this->value, $fieldDefinition));
    }

    public function testGetLegacyValue()
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
