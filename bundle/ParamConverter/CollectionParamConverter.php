<?php


namespace Netgen\Bundle\InformationCollectionBundle\ParamConverter;

use Netgen\InformationCollection\API\Service\InformationCollection;
use Netgen\InformationCollection\API\Value\Collection;
use Netgen\InformationCollection\API\Value\Filter\CollectionId;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

final class CollectionParamConverter implements ParamConverterInterface
{
    protected InformationCollection $informationCollection;

    public function __construct(InformationCollection $informationCollection)
    {
        $this->informationCollection = $informationCollection;
    }

    /**
     * Stores the object in the request.
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        if (!$request->attributes->has('collectionId')) {
            return false;
        }

        $collectionId = $request->attributes->get('collectionId');
        if (!$collectionId && $configuration->isOptional()) {
            return false;
        }

        $request->attributes->set(
            $configuration->getName(), $this->informationCollection->getCollection(new CollectionId($collectionId))
        );

        return true;
    }

    /**
     * Checks if the object is supported.
     */
    public function supports(ParamConverter $configuration): bool
    {
        return is_a($configuration->getClass(), Collection::class, true);
    }
}
