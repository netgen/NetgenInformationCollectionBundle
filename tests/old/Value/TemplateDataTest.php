<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Value;

use Ibexa\Core\Repository\Values\Content\Content;
use Netgen\Bundle\IbexaFormsBundle\Form\DataWrapper;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Value\TemplateData;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\TemplateWrapper;

class TemplateDataTest extends TestCase
{
    protected TemplateData $templateData;

    protected InformationCollected $event;

    protected Content $content;

    protected TemplateWrapper $templateWrapper;

    public function setUp(): void
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

        $this->templateData = new TemplateData($this->event, $this->content, $this->templateWrapper);
    }

    public function testGetters(): void
    {
        $this->assertEquals($this->event, $this->templateData->getEvent());
        $this->assertEquals($this->content, $this->templateData->getContent());
        $this->assertEquals($this->templateWrapper, $this->templateData->getTemplateWrapper());
    }
}
