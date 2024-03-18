<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form;

final class DataWrapper
{
    /**
     * One of the Ibexa Platform structs, like ContentCreateStruct, UserUpdateStruct and so on.
     *
     * @var mixed
     */
    public $payload;

    /**
     * Definition of the target.
     *
     * In case of Content or User target, this must be the corresponding ContentType.
     *
     * @var mixed|null
     */
    public $definition;

    /**
     * The target struct that applies to. E.g. Content, User, Section object and so on.
     *
     * This target makes sense only in update context, when creating target does not
     * exist (yet to be created).
     *
     * @var mixed|null
     */
    public $target;

    /**
     * Construct from payload, target and definition.
     *
     * @param mixed $payload
     * @param mixed|null $target
     * @param mixed|null $definition
     */
    public function __construct($payload, $definition = null, $target = null)
    {
        $this->payload = $payload;
        $this->definition = $definition;
        $this->target = $target;
    }
}
