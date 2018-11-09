<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\RepositoryForms;

use eZ\Publish\Core\Repository\Values\Content\ContentUpdateStruct;
use EzSystems\RepositoryForms\Data\Content\ContentData;

class InformationCollectionData extends ContentUpdateStruct
{
    use ContentData;

    protected $contentDraft;
}