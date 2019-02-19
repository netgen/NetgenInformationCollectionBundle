<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Value;

use eZ\Publish\Core\Repository\Values\Content\Content;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Value\TemplateData;
use PHPUnit\Framework\TestCase;
use Twig_Environment;
use Twig_Loader_Array;
use Twig_TemplateWrapper;

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
     * @var \Twig_TemplateWrapper
     */
    protected $templateWrapper;

    public function setUp()
    {
        $twig = new Twig_Environment(
            new Twig_Loader_Array(
                array(
                    'index' => '{% block foo %}{% endblock %}',
                )
            )
        );

        $this->event = new InformationCollected(new DataWrapper('test', null, null));
        $this->content = new Content();
        $this->templateWrapper = new Twig_TemplateWrapper($twig, $twig->loadTemplate('index'));

        $this->templateData = new TemplateData($this->event, $this->content, $this->templateWrapper);
    }

    public function testGetters()
    {
        $this->assertEquals($this->event, $this->templateData->getEvent());
        $this->assertEquals($this->content, $this->templateData->getContent());
        $this->assertEquals($this->templateWrapper, $this->templateData->getTemplateWrapper());
    }
}
