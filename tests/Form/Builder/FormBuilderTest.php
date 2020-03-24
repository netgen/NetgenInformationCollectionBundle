<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\FieldHandler;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\ContentTypeService;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\InformationCollectionBundle\Form\Builder\FormBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder as SymfonyFormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Router;

class FormBuilderTest extends TestCase
{
    /**
     * @var FormBuilder
     */
    protected $formBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $formFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentTypeService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $router;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $innerFormBuilder;

    public function setUp()
    {
        $this->formFactory = $this->getMockBuilder(FormFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(array('createBuilder', 'setAction', 'createNamedBuilder'))
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

    public function testFormBuildUp()
    {
        $location = new Location(array(
            'id' => 456,
            'contentInfo' => new ContentInfo(array(
                'contentTypeId' => 123,
            )),
        ));

        $contentType = new ContentType([
            'identifier' => 'test_content_type',
        ]);

        $this->contentTypeService->expects($this->once())
            ->method('loadContentType')
            ->with(123)
            ->willReturn($contentType);

        $this->formFactory->expects($this->once())
            ->method('createNamedBuilder')
            ->willReturn($this->innerFormBuilder);

        $this->innerFormBuilder->expects($this->never())
            ->method('setAction');

        $builder = $this->formBuilder->createFormForLocation($location, false);

        $this->assertSame($this->innerFormBuilder, $builder);
    }

    public function testFormBuildUpWithCustomURL()
    {
        $location = new Location(array(
            'contentInfo' => new ContentInfo(array(
                'contentTypeId' => 123,
            )),
        ));

        $contentType = new ContentType([
            'identifier' => 'test_content_type',
        ]);

        $this->contentTypeService->expects($this->once())
            ->method('loadContentType')
            ->with(123)
            ->willReturn($contentType);

        $this->formFactory->expects($this->once())
            ->method('createNamedBuilder')
            ->willReturn($this->innerFormBuilder);

        $this->innerFormBuilder->expects($this->once())
            ->method('setAction');

        $this->router->expects($this->once())
            ->method('generate');

        $builder = $this->formBuilder->createFormForLocation($location, true);

        $this->assertSame($this->innerFormBuilder, $builder);
    }
}
