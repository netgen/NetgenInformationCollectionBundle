<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\FieldHandler;

use eZ\Publish\Core\FieldType\Integer\Value as TestValue;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomFieldHandlerInterface;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\FieldHandlerRegistry;
use PHPUnit\Framework\TestCase;

class FieldHandlerRegistryTest extends TestCase
{
    /**
     * @var FieldHandlerRegistry
     */
    protected $registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customHandler1;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customHandler2;

    public function setUp()
    {
        $this->registry = new FieldHandlerRegistry();
        $this->customHandler1 = $this->getMockBuilder(CustomFieldHandlerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('supports', 'toString'))
            ->getMock();

        $this->customHandler2 = $this->getMockBuilder(CustomFieldHandlerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('supports', 'toString'))
            ->getMock();

        parent::setUp();
    }

    public function testAddingHandlers()
    {
        $this->registry->addHandler($this->customHandler1);
        $this->registry->addHandler($this->customHandler2);
    }

    public function testItReturnsProperHandler()
    {
        $value = new TestValue(2);

        $this->registry->addHandler($this->customHandler1);
        $this->registry->addHandler($this->customHandler2);

        $this->customHandler1->expects($this->once())
            ->method('supports')
            ->willReturn(false);

        $this->customHandler2->expects($this->once())
            ->method('supports')
            ->willReturn(true);

        $handler = $this->registry->handle($value);

        $this->assertSame($this->customHandler2, $handler);
    }

    public function testItReturnsNullWhenSupportedHandlerNotFound()
    {
        $value = new TestValue(2);

        $handler = $this->registry->handle($value);

        $this->assertNull($handler);
    }
}
