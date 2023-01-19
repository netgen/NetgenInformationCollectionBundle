<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\DataCollector;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Helper\TranslationHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Netgen\Bundle\InformationCollectionBundle\Ibexa\ContentForms\InformationCollectionType;

class InformationCollectionCollector extends DataCollector
{
    private Repository $repository;

    private TranslationHelper $translationHelper;

    public function __construct(Repository $repository, TranslationHelper $translationHelper)
    {
        $this->repository = $repository;
        $this->data = [
            'count' => 0,
            'collections' => [],
            'content_type' => null,
            'content' => null,
        ];

        $this->translationHelper = $translationHelper;
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        if ($request->get(InformationCollectionType::FORM_BLOCK_PREFIX) !== null) {
            $this->mapCollectedData($request);

            return;
        }

        $this->data = [];

    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function getName(): string
    {
        return 'netgen_information_collection_collector';
    }

    public function getCollections(): array
    {
        return $this->data['collections'] ?? [];
    }

    public function getCollectionCount(): int
    {
        return $this->data['count'] ?? 0;
    }

    public function getContent(): string
    {
        return $this->data['content'];
    }

    public function getContentId(): int
    {
        return $this->data['content_id'];
    }

    public function getContentType(): string
    {
        return $this->data['content_type'];
    }

    public function getContentTypeId(): int
    {
        return $this->data['content_type_id'];
    }

    public function getAdminSiteaccess(): string
    {
        return 'admin';
    }

    public function getContentTypeGroupId(): int
    {
        return $this->data['content_type_group_id'];
    }

    private function mapCollectedData(Request $request): void
    {
        $mapped = [];

        $data = $request->get('information_collection');

        $contentId = $data['content_id'];
        $contentTypeId = intval($data['content_type_id']);
        /** @var ContentType $contentType */
        $contentType = $this->repository->sudo(
            function(Repository $repository) use ($contentTypeId) {
                return $repository->getContentTypeService()->loadContentType($contentTypeId);
            }
        );

        $content = $this->repository->sudo(
            function(Repository $repository) use ($contentId) {
                return $repository->getContentService()->loadContent((int)$contentId);
            }
        );

        foreach ($data as $identifier => $datum) {
            if (is_array($datum) && array_key_exists('value', $datum)) {

                $fieldDefinition = $contentType->getFieldDefinition($identifier);
                if ($fieldDefinition === null) {
                    continue;
                }

                $mapped['collections'][] = [
                    'identifier' => $identifier,
                    'value' => $datum['value'],
                    'name' => $this->translationHelper->getTranslatedByMethod($fieldDefinition, 'getName'),
                    'type' => $fieldDefinition->fieldTypeIdentifier,
                ];
            }
        }

        $mapped['content'] = $this->translationHelper->getTranslatedContentName($content);
        $mapped['content_id'] = $content->id;
        $mapped['content_type'] = $this->translationHelper->getTranslatedByMethod($contentType, 'getName');
        $mapped['content_type_id'] = $contentType->id;
        $mapped['content_type_group_id'] = $contentType->getContentTypeGroups()[0]->id;
        $mapped['count'] = count($mapped['collections']);

        $this->data = $mapped;
    }
}
