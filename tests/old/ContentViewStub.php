<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests;

use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;

class ContentViewStub implements ContentValueView
{
    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
    }
}
