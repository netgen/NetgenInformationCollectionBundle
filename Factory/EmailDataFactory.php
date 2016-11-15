<?php

namespace Netgen\Bundle\InformationCollectionBundle\Factory;

use eZ\Publish\Core\Helper\FieldHelper;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\API\Repository\Values\Content\Content;
use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;

class EmailDataFactory
{
    /**
     * @var ConfigResolverInterface
     */
    protected $configResolver;

    /**
     * @var TranslationHelper
     */
    protected $translationHelper;

    /**
     * @var FieldHelper
     */
    protected $fieldHelper;

    public function __construct(
        ConfigResolverInterface $configResolver,
        TranslationHelper $translationHelper,
        FieldHelper $fieldHelper
    )
    {
        $this->configResolver = $configResolver;
        $this->translationHelper = $translationHelper;
        $this->fieldHelper = $fieldHelper;
    }

    public function build(Content $content)
    {
        return new EmailData(
            $this->resolve($content, 'recipient', 'email'),
            $this->resolve($content, 'sender', 'email'),
            $this->resolve($content, 'subject'),
            $this->resolveTemplate()
        );
    }

    protected function resolve(Content $content, $field, $property = 'text')
    {
        if (
            array_key_exists($field, $content->fields) &&
            !$this->fieldHelper->isFieldEmpty($content, $field)
        ) {

            $fieldValue = $this->translationHelper->getTranslatedField($content, $field);

            return $fieldValue->value->$property;
        } else {

            return $this->configResolver->getParameter('information_collection.email.' . $field, 'netgen');

        }
    }

    protected function resolveTemplate()
    {
        return $this->configResolver->getParameter('information_collection.email.template', 'netgen');
    }
}