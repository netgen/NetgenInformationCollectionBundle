<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Form;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

interface DynamicFormBuilderInterface
{
    /**
     * Creates Information collection Form object for given Location object.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    public function createForm(Content $content): FormInterface;

    /**
     * Creates Information collection Form object for given Location object.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    public function createFormWithAjax(Content $content): FormBuilderInterface;
}
