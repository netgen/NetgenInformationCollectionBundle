<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\FieldHandler;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\ContentTypeService;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\InformationCollectionBundle\Form\Builder\FormBuilder;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Router;
use Symfony\Component\Form\FormBuilder as SymfonyFormBuilder;

class FormBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var FormBuilder
     */
    protected $formBuilder;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $formFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentTypeService;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $router;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $innerFormBuilder;

    public function setUp()
    {
        $this->formFactory = $this->getMockBuilder(FormFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createBuilder', 'setAction'])
            ->getMock();

        $this->contentTypeService = $this->getMockBuilder(ContentTypeService::class)
            ->disableOriginalConstructor()
            ->setMethods(['loadContentType'])
            ->getMock();

        $this->router = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->setMethods(['generate'])
            ->getMock();

        $this->formBuilder = new FormBuilder($this->formFactory, $this->contentTypeService, $this->router, true);
        $this->innerFormBuilder = $this->getMockBuilder(SymfonyFormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        parent::setUp();
    }

    public function testFormBuildUp()
    {
        $location = new Location([
            'contentInfo' => new ContentInfo([
                'contentTypeId' => 123,
            ]),
        ]);

        $this->contentTypeService->expects($this->once())
            ->method('loadContentType')
            ->with(123);

        $this->formFactory->expects($this->once())
            ->method('createBuilder')
            ->willReturn($this->innerFormBuilder);

        $this->innerFormBuilder->expects($this->never())
            ->method('setAction');

        $builder = $this->formBuilder->createFormForLocation($location, false);

        $this->assertSame($this->innerFormBuilder, $builder);
    }

    public function testFormBuildUpWithCustomURL()
    {
        $location = new Location([
            'contentInfo' => new ContentInfo([
                'contentTypeId' => 123,
            ]),
        ]);

        $this->contentTypeService->expects($this->once())
            ->method('loadContentType')
            ->with(123);

        $this->formFactory->expects($this->once())
            ->method('createBuilder')
            ->willReturn($this->innerFormBuilder);

        $this->innerFormBuilder->expects($this->once())
            ->method('setAction');

        $this->router->expects($this->once())
            ->method('generate');

        $builder = $this->formBuilder->createFormForLocation($location, true);

        $this->assertSame($this->innerFormBuilder, $builder);
    }
}
