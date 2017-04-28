<?php

namespace Netgen\Bundle\InformationCollectionBundle;

use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler\ActionsPass;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler\CustomFieldHandlersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NetgenInformationCollectionBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ActionsPass());
        $container->addCompilerPass(new CustomFieldHandlersPass());
    }
}
