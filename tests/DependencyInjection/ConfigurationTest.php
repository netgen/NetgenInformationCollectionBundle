<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testConfigurationValuesAreOkAndValid()
    {
        $this->assertConfigurationIsValid(
            array(
                'netgen_information_collection' => array(
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
                ),
            )
        );
    }

    public function testConfigurationIsInvalidForDefaultTemplateValue()
    {
        $this->assertConfigurationIsInvalid(
            array(
                'netgen_information_collection' => array(
                    'system' => array(
                        'default' => array(
                            'action_config' => array(
                                'email' => array(
                                    'templates' => array(
                                        'default' => '',
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
                ),
            ),
            'netgen_information_collection.system.default.action_config.email.templates.default'
        );
    }

    public function testConfigurationIsInvalidForDefaultActionsValue()
    {
        $this->assertConfigurationIsInvalid(
            array(
                'netgen_information_collection' => array(
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
                                    '',
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
                ),
            ),
            'netgen_information_collection.system.default.actions.default'
        );
    }

    protected function getConfiguration()
    {
        return new Configuration();
    }
}
