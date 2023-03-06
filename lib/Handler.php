<?php

namespace Netgen\InformationCollection;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\InformationCollection\API\Events;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Netgen\InformationCollection\API\Value\InformationCollectionStruct;
use Netgen\Bundle\InformationCollectionBundle\EzPlatform\RepositoryForms\InformationCollectionMapper;
use Netgen\Bundle\InformationCollectionBundle\EzPlatform\RepositoryForms\InformationCollectionType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class Handler
{
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

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
    private function determineLanguageToLoad(Content $content)
    {
        $versionInfo = $content->getVersionInfo();

        $siteAccessLanguagesCodes = (array)$this->configResolver->getParameter('languages');
        foreach($siteAccessLanguagesCodes as $languageCode)
        {
            if (in_array($languageCode, $versionInfo->languageCodes))
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
