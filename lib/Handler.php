<?php

declare(strict_types=1);

namespace Netgen\InformationCollection;

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
use Symfony\Component\HttpFoundation\Request;

final class Handler
{
    private FormFactoryInterface $formFactory;

    private ContentTypeService $contentTypeService;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(FormFactoryInterface $formFactory, ContentTypeService $contentTypeService, EventDispatcherInterface $eventDispatcher)
    {
        $this->formFactory = $formFactory;
        $this->contentTypeService = $contentTypeService;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getForm(Content $content, Location $location, Request $request): FormInterface
    {
        $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);

        $informationCollectionMapper = new InformationCollectionMapper();

        $data = $informationCollectionMapper->mapToFormData($content, $location, $contentType);
        $discriminator = $this->resolveDiscriminator($request);

        return $this->formFactory->createNamed(
            InformationCollectionType::FORM_BLOCK_PREFIX . '_' . $discriminator,
            InformationCollectionType::class,
            $data,
            [
                'languageCode' => $content->contentInfo->mainLanguageCode,
                'mainLanguageCode' => $content->contentInfo->mainLanguageCode,
                'discriminator' => $discriminator,
            ],
        );
    }

    public function handle(InformationCollectionStruct $struct, array $options, array $additionalParameters = []): void
    {
        $event = new InformationCollected($struct, $options, $additionalParameters);

        $this->eventDispatcher->dispatch($event, Events::INFORMATION_COLLECTED);
    }

    private function resolveDiscriminator(Request $request): string
    {
        foreach ($request->request as $key => $value) {
            if (str_starts_with($key, InformationCollectionType::FORM_BLOCK_PREFIX)) {
                if (isset($value['discriminator'])) {
                    return $value['discriminator'];
                }

                break;
            }
        }

        return uniqid('', true);
    }
}
