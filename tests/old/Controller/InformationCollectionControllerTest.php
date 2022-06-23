<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Controller;

use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\Core\Repository\Values\Content\Location;
use Netgen\Bundle\IbexaFormsBundle\Form\DataWrapper;
use Netgen\Bundle\IbexaFormsBundle\Form\Payload\InformationCollectionStruct;
use Netgen\Bundle\InformationCollectionBundle\Controller\InformationCollectionController;
use Netgen\Bundle\InformationCollectionBundle\Form\Builder\FormBuilder;
use Netgen\Bundle\InformationCollectionBundle\Tests\ContentViewStub;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder as SymfonyFormBuilder;
use Symfony\Component\HttpFoundation\Request;

class InformationCollectionControllerTest extends TestCase
{
    protected InformationCollectionController $controller;

    protected MockObject $dispatcher;

    protected MockObject $builder;

    protected MockObject $contentView;

    protected MockObject $request;

    protected MockObject $formBuilder;

    protected MockObject $form;

    protected MockObject $container;

    public function setUp(): void
    {
        if (!class_exists(ContentView::class)) {
            $this->markTestSkipped();
        }

        $this->container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'getParameter', 'has', 'hasParameter', 'initialized', 'set', 'setParameter', 'addScope', 'enterScope', 'hasScope', 'isScopeActive', 'leaveScope'))
            ->getMock();

        $this->builder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('createFormForLocation'))
            ->getMock();

        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('dispatch', 'addListener', 'addSubscriber', 'removeListener', 'removeSubscriber', 'getListeners', 'hasListeners', 'getListenerPriority'))
            ->getMock();

        $this->contentView = $this->getMockBuilder(ContentView::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getLocation', 'addParameters'))
            ->getMock();

        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $this->formBuilder = $this->getMockBuilder(SymfonyFormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getForm'))
            ->getMock();

        $this->form = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->setMethods(array('handleRequest', 'isSubmitted', 'isValid', 'getData', 'createView'))
            ->getMock();

        $this->controller = new InformationCollectionController();
        $this->controller->setContainer($this->container);

        parent::setUp();
    }

    public function testInstanceOfContainerAwareInterface(): void
    {
        $this->assertInstanceOf(ContainerAwareInterface::class, $this->controller);
    }

    public function testDisplayAndHandleWithValidFormSubmission(): void
    {
        $location = new Location();

        $this->container->expects($this->exactly(2))
            ->method('get')
            ->with($this->logicalOr(
                $this->equalTo('netgen_information_collection.form.builder'),
                $this->equalTo('event_dispatcher')
            ))
            ->will($this->returnCallback(array($this, 'getService')));

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

        $this->form->expects($this->exactly(2))
            ->method('getData')
            ->willReturn(new DataWrapper(new InformationCollectionStruct()));

        $this->form->expects($this->once())
            ->method('createView');

        $this->dispatcher->expects($this->once())
            ->method('dispatch');

        $this->contentView->expects($this->once())
            ->method('addParameters');

        $this->controller->displayAndHandle($this->contentView, $this->request);
    }

    public function testDisplayAndHandleWithInvalidFormSubmission(): void
    {
        $location = new Location();

        $this->container->expects($this->once())
            ->method('get')
            ->with('netgen_information_collection.form.builder')
            ->willReturn($this->builder);

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
            ->method('isValid')
            ->willReturn(false);

        $this->form->expects($this->exactly(1))
            ->method('getData')
            ->willReturn(new DataWrapper(new InformationCollectionStruct()));

        $this->dispatcher->expects($this->never())
            ->method('dispatch');

        $this->form->expects($this->once())
            ->method('createView');

        $this->contentView->expects($this->once())
            ->method('addParameters');

        $this->controller->displayAndHandle($this->contentView, $this->request);
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Ibexa view needs to implement LocationValueView interface
     */
    public function testIfLocationValueViewIsNotProvidedThrowBadMethodCallException(): void
    {
        $this->container->expects($this->never())
            ->method('get')
            ->with('netgen_information_collection.form.builder')
            ->willReturn($this->builder);

        $this->controller->displayAndHandle(new ContentViewStub(), $this->request);
    }

    public function getService(string $id): object
    {
        switch ($id) {
            case 'netgen_information_collection.form.builder':
                return $this->builder;
            case 'event_dispatcher':
                return $this->dispatcher;
        }
    }
}
