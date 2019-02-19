<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\NetgenInformationCollectionExtension;

class NetgenInformationCollectionExtensionTest extends AbstractExtensionTestCase
{
    public function testItSetsValidContainerParameters()
    {
        $this->container->setParameter('ezpublish.siteaccess.list', array());
        $this->load();
    }

    protected function getContainerExtensions()
    {
        return array(
            new NetgenInformationCollectionExtension(),
        );
    }

    protected function getMinimalConfiguration()
    {
        return array(
            'system' => array(
                'default' => array(
                    'action_config' => array(
                        'email' => array(
                            'templates' => array(
                                'default' => 'some_template',
                                'content_types' => array(
                                    'content_type1' => 'content_type1_template',
                                    'content_type2' => 'content_type2_template',
                                ),
                            ),
                            'default_variables' => array(
                                'sender' => 'sender',
                                'recipient' => 'recipient',
                                'subject' => 'subject',
                            ),
                        ),
                    ),
                    'actions' => array(
                        'default' => array(
                            'action1',
                            'action2',
                        ),
                        'content_types' => array(
                            'content_type1' => array(
                                'action3',
                                'action4',
                            ),
                            'content_type2' => array(
                                'action5',
                                'action6',
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
}
