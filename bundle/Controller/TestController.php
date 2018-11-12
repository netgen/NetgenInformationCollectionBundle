<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller as BaseController;
use Netgen\Bundle\InformationCollectionBundle\Form\RepositoryForms\InformationCollectionMapper;
use Netgen\Bundle\InformationCollectionBundle\Form\RepositoryForms\InformationCollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController extends BaseController
{
    public function test(Request $request)
    {
        $contentId = 235;

        $content = $this->getRepository()->getContentService()->loadContent($contentId);
        $contentType = $this->getRepository()->getContentTypeService()->loadContentType($content->contentInfo->contentTypeId);

        $informationCollectionMapper = new InformationCollectionMapper();

        $data = $informationCollectionMapper->mapToFormData($content, [
            'languageCode' => $content->contentInfo->mainLanguageCode,
            'contentType' => $contentType,
        ]);

        $form = $this->get('form.factory')->create(InformationCollectionType::class, $data, [
            'languageCode' => $content->contentInfo->mainLanguageCode,
            'mainLanguageCode' => $content->contentInfo->mainLanguageCode,
        ]);

        $form->handleRequest($request);

        dump($form->getData());


        return $this->render('@NetgenInformationCollection/test.html.twig', ['form' => $form->createView()]);
    }
}