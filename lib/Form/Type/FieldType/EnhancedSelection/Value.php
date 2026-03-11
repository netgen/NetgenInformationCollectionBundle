<?php

namespace Netgen\InformationCollection\Form\Type\FieldType\EnhancedSelection;

use Ibexa\Core\FieldType\Value as BaseValue;

use function implode;

final class Value extends BaseValue
{
    /**
     * The list of selection identifiers.
     *
     * @var string[]
     */
    public array $identifiers = [];

    /**
     * @param string[] $identifiers
     */
    public function __construct(array $identifiers = [])
    {
        $this->identifiers = $identifiers;
    }

    public function __toString(): string
    {
        return implode(', ', $this->identifiers);
    }
}

