<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Persistence\Anonymizer\Visitor\Field;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute;

class Simple extends FieldAnonymizerVisitor
{
    public function accept(EzInfoCollectionAttribute $ezInfoCollectionAttribute, ContentType $contentType)
    {
        return true;
    }

    public function visit(EzInfoCollectionAttribute $ezInfoCollectionAttribute, ContentType $contentType)
    {
        return 'XXXXXXXXXX';
    }
}
