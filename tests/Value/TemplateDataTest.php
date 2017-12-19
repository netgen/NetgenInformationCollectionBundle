<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Value;

use eZ\Publish\Core\Repository\Values\Content\Content;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Value\TemplateData;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\TemplateWrapper;

class TemplateDataTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Value\TemplateData
     */
    protected $templateData;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected
     */
    protected $event;

    /**
     * @var \eZ\Publish\Core\Repository\Values\Content\Content
     */
    protected $content;

    /**
     * @var \Twig\TemplateWrapper
     */
    protected $templateWrapper;

    public function setUp()
    {
        $twig = new Environment(
            new ArrayLoader(
                array(
                    'index' => '{% block foo %}{% endblock %}',
                )
            )
        );

        $this->event = new InformationCollected(new DataWrapper('test', null, null));
        $this->content = new Content();
        $this->templateWrapper = new TemplateWrapper($twig, $twig->loadTemplate('index'));

        $this->templateData = new TemplateData(
            array(
                'event' => $this->event,
                'content' => $this->content,
                'templateWrapper' => $this->templateWrapper,
            )
        );
    }

    public function testGetters()
    {
        $this->assertEquals($this->event, $this->templateData->event);
        $this->assertEquals($this->content, $this->templateData->content);
        $this->assertEquals($this->templateWrapper, $this->templateData->templateWrapper);
    }

    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\PropertyNotFoundException
     * @expectedExceptionMessage Property 'test' not found on class 'Netgen\Bundle\InformationCollectionBundle\Value\TemplateData'
     */
    public function testExceptionShouldBeThrownInCaseOfAccessingNonExistingProperty()
    {
        $this->templateData->test;
    }

    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\PropertyReadOnlyException
     * @expectedExceptionMessage Property 'event' is readonly on class 'Netgen\Bundle\InformationCollectionBundle\Value\TemplateData'
     */
    public function testExceptionShouldBeThrownInCaseOfSettingPropertyValue()
    {
        $this->templateData->event = 'event';
    }

    /**
     * @expectedException \Netgen\Bundle\InformationCollectionBundle\Exception\PropertyNotFoundException
     * @expectedExceptionMessage Property 'test' not found on class 'Netgen\Bundle\InformationCollectionBundle\Value\TemplateData'
     */
    public function testExceptionShouldBeThrownInCaseOfSettingPropertyValueOfNonExistingProperty()
    {
        $this->templateData->test = 'test';
    }
}
