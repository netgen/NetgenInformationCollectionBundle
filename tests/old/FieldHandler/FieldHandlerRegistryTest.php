<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\FieldHandler;

use Ibexa\Core\FieldType\Integer\Value as TestValue;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomFieldHandlerInterface;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\FieldHandlerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FieldHandlerRegistryTest extends TestCase
{
    protected FieldHandlerRegistry $registry;

    protected MockObject $customHandler1;

    protected MockObject $customHandler2;

    public function setUp(): void
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

    public function testAddingHandlers(): void
    {
        $this->registry->addHandler($this->customHandler1);
        $this->registry->addHandler($this->customHandler2);
    }

    public function testItReturnsProperHandler(): void
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

    public function testItReturnsNullWhenSupportedHandlerNotFound(): void
    {
        $value = new TestValue(2);

        $handler = $this->registry->handle($value);

        $this->assertNull($handler);
    }
}
