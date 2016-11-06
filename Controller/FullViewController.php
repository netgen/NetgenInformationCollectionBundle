<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller;

use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Events;
use Netgen\Bundle\InformationCollectionBundle\Form\Builder\FormBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

class FullViewController
{
    /**
     * @var FormBuilder
     */
    protected $builder;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * FullViewController constructor.
     *
     * @param FormBuilder $builder
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(FormBuilder $builder, EventDispatcherInterface $dispatcher)
    {
        $this->builder = $builder;
        $this->dispatcher = $dispatcher;
    }
    public function displayAndHandleInformationCollector(ContentView $view, Request $request)
    {
        $isValid = false;

        $location = $view->getLocation();
        /** @var FormBuilderInterface $formBuilder */
        $form = $this->builder->createFormForLocation($location);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isValid = true;

            $event = new InformationCollected($form->getData());
            $this->dispatcher->dispatch(Events::INFORMATION_COLLECTED, $event);
        }

        $parameters = [
            'is_valid' => $isValid,
            'form' => $form->createView(),
        ];

        $view->addParameters($parameters);
        return $view;
    }
}