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

    /**
     * EmailDataFactory constructor.
     *
     * @param ConfigResolverInterface $configResolver
     * @param TranslationHelper $translationHelper
     * @param FieldHelper $fieldHelper
     */
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

    /**
     * Factory method
     *
     * @param Content $content
     *
     * @return EmailData
     */
    public function build(Content $content)
    {
        return new EmailData(
            $this->resolve($content, 'recipient', 'email'),
            $this->resolve($content, 'sender', 'email'),
            $this->resolve($content, 'subject'),
            $this->resolveTemplate()
        );
    }

    /**
     * Returns resolved parameter
     *
     * @param Content $content
     * @param string $field
     * @param string $property
     *
     * @return mixed
     */
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

    /**
     * Returns resolved template name
     *
     * @return string
     */
    protected function resolveTemplate()
    {
        return $this->configResolver->getParameter('information_collection.email.template', 'netgen');
    }
}