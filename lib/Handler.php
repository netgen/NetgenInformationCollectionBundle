<?php

declare(strict_types=1);

namespace Netgen\InformationCollection;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Netgen\Bundle\InformationCollectionBundle\Ibexa\ContentForms\InformationCollectionMapper;
use Netgen\Bundle\InformationCollectionBundle\Ibexa\ContentForms\InformationCollectionType;
use Netgen\InformationCollection\API\Events;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Netgen\InformationCollection\API\Value\InformationCollectionStruct;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class Handler
{
    private FormFactoryInterface $formFactory;

    private ContentTypeService $contentTypeService;

    private EventDispatcherInterface $eventDispatcher;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        FormFactoryInterface $formFactory,
        ContentTypeService $contentTypeService,
        EventDispatcherInterface $eventDispatcher,
        ConfigResolverInterface $configResolver
    )
    {
        $this->formFactory = $formFactory;
        $this->contentTypeService = $contentTypeService;
        $this->eventDispatcher = $eventDispatcher;
        $this->configResolver = $configResolver;
    }

    public function getForm(Content $content, Location $location): FormInterface
    {
        $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);

        $informationCollectionMapper = new InformationCollectionMapper();

        $data = $informationCollectionMapper->mapToFormData($content, $location, $contentType);

        return $this->formFactory->create(InformationCollectionType::class, $data, [
            'languageCode' => $this->determineLanguageToLoad($content),
            'mainLanguageCode' => $content->contentInfo->mainLanguageCode,
        ]);
    }

    /**
     * @param Content $content
     * @return string
     */
    private function determineLanguageToLoad(Content $content): string
    {
        $versionInfo = $content->getVersionInfo();

        $siteAccessLanguagesCodes = (array)$this->configResolver->getParameter('languages');
        foreach($siteAccessLanguagesCodes as $languageCode)
        {
            if (in_array($languageCode, $versionInfo->languageCodes, true))
            {
                return $languageCode;
            }
        }

        return $content->contentInfo->mainLanguageCode;
    }

    public function handle(InformationCollectionStruct $struct, array $options): void
    {
        $event = new InformationCollected($struct, $options);

        $this->eventDispatcher->dispatch($event, Events::INFORMATION_COLLECTED);
    }
}
