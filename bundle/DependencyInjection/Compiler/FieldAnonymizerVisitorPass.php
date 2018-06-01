<?php

namespace Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FieldAnonymizerVisitorPass implements CompilerPassInterface
{
    /**
     * Service ID of aggregate visitor.
     *
     * @see \Netgen\Bundle\InformationCollectionBundle\Core\Persistence\Anonymizer\Visitor\Field\Aggregate
     *
     * @var string
     */
    private $aggregateVisitorId = 'netgen_information_collection.anonymizer.visitor.field.aggregate';

    /**
     * Service tag used for field anonymizer visitors.
     *
     * @see \Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor
     *
     * @var string
     */
    private $visitorTag = 'netgen_information_collection.anonymizer.visitor.field';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->aggregateVisitorId)) {
            return;
        }

        $aggregateVisitorDefinition = $container->getDefinition($this->aggregateVisitorId);
        $visitors = $container->findTaggedServiceIds($this->visitorTag);
        $visitorsByPriority = [];

        foreach ($visitors as $id => $tags) {
            foreach ($tags as $tag) {
                $priority = isset($tag['priority']) ? (int)$tag['priority'] : 0;
                $visitorsByPriority[$priority][] = new Reference($id);
            }
        }

        if (count($visitorsByPriority) > 0) {
            krsort($visitorsByPriority);
            $visitors = array_merge(...$visitorsByPriority);
            $aggregateVisitorDefinition->setArguments([$visitors]);
        }
    }
}
