<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit_Framework_TestCase;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Configuration;

class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testConfigurationValuesAreOkAndValid()
    {
        $this->assertConfigurationIsValid(
            [
                'netgen_information_collection' => [
                    'system' => [
                        'default' => [
                            'action_config' => [
                                'email' => [
                                    'templates' => [
                                        'default' => 'some_template',
                                        'content_types' => [
                                            'content_type1' => 'content_type1_template',
                                            'content_type2' => 'content_type2_template',
                                        ],
                                    ],
                                    'default_variables' => [
                                        'sender' => 'sender',
                                        'recipient' => 'recipient',
                                        'subject' => 'subject',
                                    ],
                                ],
                            ],
                            'actions' => [
                                'default' => [
                                    'action1',
                                    'action2',
                                ],
                                'content_types' => [
                                    'content_type1' => [
                                        'action3',
                                        'action4',
                                    ],
                                    'content_type2' => [
                                        'action5',
                                        'action6',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    public function testConfigurationIsInvalidForDefaultTemplateValue()
    {
        $this->assertConfigurationIsInvalid(
            [
                'netgen_information_collection' => [
                    'system' => [
                        'default' => [
                            'action_config' => [
                                'email' => [
                                    'templates' => [
                                        'default' => '',
                                        'content_types' => [
                                            'content_type1' => 'content_type1_template',
                                            'content_type2' => 'content_type2_template',
                                        ],
                                    ],
                                    'default_variables' => [
                                        'sender' => 'sender',
                                        'recipient' => 'recipient',
                                        'subject' => 'subject',
                                    ],
                                ],
                            ],
                            'actions' => [
                                'default' => [
                                    'action1',
                                    'action2',
                                ],
                                'content_types' => [
                                    'content_type1' => [
                                        'action3',
                                        'action4',
                                    ],
                                    'content_type2' => [
                                        'action5',
                                        'action6',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'netgen_information_collection.system.default.action_config.email.templates.default'
        );
    }

    public function testConfigurationIsInvalidForDefaultActionsValue()
    {
        $this->assertConfigurationIsInvalid(
            [
                'netgen_information_collection' => [
                    'system' => [
                        'default' => [
                            'action_config' => [
                                'email' => [
                                    'templates' => [
                                        'default' => 'some_template',
                                        'content_types' => [
                                            'content_type1' => 'content_type1_template',
                                            'content_type2' => 'content_type2_template',
                                        ],
                                    ],
                                    'default_variables' => [
                                        'sender' => 'sender',
                                        'recipient' => 'recipient',
                                        'subject' => 'subject',
                                    ],
                                ],
                            ],
                            'actions' => [
                                'default' => [
                                    '',
                                ],
                                'content_types' => [
                                    'content_type1' => [
                                        'action3',
                                        'action4',
                                    ],
                                    'content_type2' => [
                                        'action5',
                                        'action6',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'netgen_information_collection.system.default.actions.default'
        );
    }
}
