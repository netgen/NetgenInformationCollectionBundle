<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests;

use Ibexa\Core\MVC\Symfony\View\ContentValueView;

class ContentViewStub implements ContentValueView
{
    public function getContent(): void
    {
    }
}
