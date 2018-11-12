<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Templating\Twig;

use Netgen\Bundle\InformationCollectionBundle\Templating\Twig\AdminGlobalVariable;
use PHPUnit\Framework\TestCase;

class AdminGlobalVariableTest extends TestCase
{
    public function testGetterSetter()
    {
        $admin = new AdminGlobalVariable();

        $this->assertNull($admin->getPageLayoutTemplate());

        $admin->setPageLayoutTemplate('this_is_my_page_layout');
        $this->assertEquals('this_is_my_page_layout', $admin->getPageLayoutTemplate());

        $admin->setPageLayoutTemplate();
        $this->assertNull($admin->getPageLayoutTemplate());
    }
}
