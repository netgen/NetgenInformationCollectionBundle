<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Controller;

use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\InformationCollectionBundle\Controller\InformationCollectionController;
use Netgen\Bundle\InformationCollectionBundle\Form\Builder\FormBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class InformationCollectionControllerTest extends TestCase
{
    /**
     * @var InformationCollectionController
     */
    protected $controller;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dispatcher;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $builder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentView;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $formBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $form;

    public function setUp()
    {
        $this->builder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['createFormForLocation'])
            ->getMock();

        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['dispatch', 'addListener', 'addSubscriber', 'removeListener', 'removeSubscriber', 'getListeners', 'hasListeners'])
            ->getMock();

        $this->contentView = $this->getMockBuilder(ContentView::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLocation', 'addParameters'])
            ->getMock();

        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->formBuilder = $this->getMockBuilder(\Symfony\Component\Form\FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getForm'])
            ->getMock();

        $this->form = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->setMethods(['handleRequest', 'isSubmitted', 'isValid', 'getData', 'createView'])
            ->getMock();

        $this->controller = new InformationCollectionController($this->builder, $this->dispatcher);
        parent::setUp();
    }

    public function testDisplayAndHandleWithValidFormSubmission()
    {
        $location = new Location();

        $this->contentView->expects($this->once())
            ->method('getLocation')
            ->willReturn($location);

        $this->formBuilder->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->builder->expects($this->once())
            ->method('createFormForLocation')
            ->with($location, false)
            ->willReturn($this->formBuilder);

        $this->form->expects($this->once())
            ->method('handleRequest')
            ->with($this->request);

        $this->form->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);

        $this->form->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->form->expects($this->once())
            ->method('getData')
            ->willReturn(new DataWrapper('payload'));

        $this->form->expects($this->once())
            ->method('createView');

        $this->dispatcher->expects($this->once())
            ->method('dispatch');

        $this->contentView->expects($this->once())
            ->method('addParameters');

        $this->controller->displayAndHandle($this->contentView, $this->request);
    }

    public function testDisplayAndHandleWithInvalidFormSubmission()
    {
        $location = new Location();

        $this->contentView->expects($this->once())
            ->method('getLocation')
            ->willReturn($location);

        $this->formBuilder->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->builder->expects($this->once())
            ->method('createFormForLocation')
            ->with($location, false)
            ->willReturn($this->formBuilder);

        $this->form->expects($this->once())
            ->method('handleRequest')
            ->with($this->request);

        $this->form->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);

        $this->form->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $this->form->expects($this->never())
            ->method('getData');

        $this->dispatcher->expects($this->never())
            ->method('dispatch');

        $this->form->expects($this->once())
            ->method('createView');

        $this->contentView->expects($this->once())
            ->method('addParameters');

        $this->controller->displayAndHandle($this->contentView, $this->request);
    }
}
