<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\FieldHandler\Custom;

use eZ\Publish\Core\FieldType\Float\Value as FloatValue;
use eZ\Publish\Core\FieldType\Integer\Value as IntegerValue;
use eZ\Publish\Core\FieldType\Checkbox\Value as CheckboxValue;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition as CoreFieldDefinition;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\FloatFieldHandler;
use PHPUnit\Framework\TestCase;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomLegacyFieldHandlerInterface;

class FloatFieldHandlerTest extends TestCase
{
    /**
     * @var CustomLegacyFieldHandlerInterface
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new FloatFieldHandler();
    }

    public function testInstanceOfCustomLegacyFieldHandler()
    {
        $this->assertInstanceOf(CustomLegacyFieldHandlerInterface::class, $this->handler);
    }

    public function testSupportsHasValidBehaviour()
    {
        $this->assertFalse($this->handler->supports(new CheckboxValue(true)));
        $this->assertFalse($this->handler->supports(new IntegerValue(1)));
        $this->assertTrue($this->handler->supports(new FloatValue(2.0)));
    }

    public function testToString()
    {
        $fieldDefinition = $this->createMock(FieldDefinition::class);

        $this->assertEquals("2.5", $this->handler->toString(new FloatValue(2.5), $fieldDefinition));
        $this->assertEquals('55.7', $this->handler->toString(new FloatValue(55.7), $fieldDefinition));
    }

    public function testGetLegacyValue()
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
