<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Templating\Twig;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\InformationCollectionBundle\Templating\Twig\AdminGlobalVariable;
use PHPUnit\Framework\TestCase;

class AdminGlobalVariableTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockBuilder
     */
    protected $configResolver;

    public function setUp()
    {
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
    }

    public function testGetterSetter()
    {
        $admin = new AdminGlobalVariable($this->configResolver);

        $this->configResolver->expects($this->once())
            ->method('getParameter')
            ->with('admin.pagelayout', 'netgen_information_collection')
            ->willReturn('some_template');

        $this->assertEquals('some_template', $admin->getPageLayoutTemplate());
    }
}
