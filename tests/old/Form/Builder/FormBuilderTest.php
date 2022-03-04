<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\FieldHandler;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\Repository\ContentTypeService;
use Ibexa\Core\Repository\Values\Content\Location;
use Netgen\Bundle\InformationCollectionBundle\Form\Builder\FormBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder as SymfonyFormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Router;

class FormBuilderTest extends TestCase
{
    protected FormBuilder $formBuilder;

    protected MockObject $formFactory;

    protected MockObject $contentTypeService;

    protected MockObject $router;

    protected MockObject $innerFormBuilder;

    public function setUp(): void
    {
        $this->formFactory = $this->getMockBuilder(FormFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(array('createBuilder', 'setAction'))
            ->getMock();

        $this->contentTypeService = $this->getMockBuilder(ContentTypeService::class)
            ->disableOriginalConstructor()
            ->setMethods(array('loadContentType'))
            ->getMock();

        $this->router = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->setMethods(array('generate'))
            ->getMock();

        $this->formBuilder = new FormBuilder($this->formFactory, $this->contentTypeService, $this->router, true);
        $this->innerFormBuilder = $this->getMockBuilder(SymfonyFormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        parent::setUp();
    }

    public function testFormBuildUp(): void
    {
        $location = new Location(array(
            'contentInfo' => new ContentInfo(array(
                'contentTypeId' => 123,
            )),
        ));

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

    public function testFormBuildUpWithCustomURL(): void
    {
        $location = new Location(array(
            'contentInfo' => new ContentInfo(array(
                'contentTypeId' => 123,
            )),
        ));

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
