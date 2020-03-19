<?php

namespace Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ExportResponseFormattersPass implements CompilerPassInterface
{
    /**
     * Service ID of formatters registry.
     *
     * @see \Netgen\Bundle\InformationCollectionBundle\Core\Export\ExportResponseFormatterRegistry
     *
     * @var string
     */
    private $formattersRegistryId = 'netgen_information_collection.core.export.registry';

    /**
     * Service tag used for export response formatter.
     *
     * @see \Netgen\Bundle\InformationCollectionBundle\API\Export\ExportResponseFormatter
     *
     * @var string
     */
    private $formatterTag = 'netgen_information_collection.export.formatter';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->formattersRegistryId)) {
            return;
        }

        $registryDefinition = $container->getDefinition($this->formattersRegistryId);
        $formatters = $container->findTaggedServiceIds($this->formatterTag);
        $formattersByPriority = [];

        foreach ($formatters as $id => $tags) {
            foreach ($tags as $tag) {
                $priority = isset($tag['priority']) ? (int)$tag['priority'] : 0;
                $formattersByPriority[$priority][] = new Reference($id);
            }
        }

        if (count($formattersByPriority) > 0) {
            krsort($formattersByPriority);
            $formatters = array_merge(...$formattersByPriority);
            $registryDefinition->setArguments([$formatters]);
        }
    }
}
