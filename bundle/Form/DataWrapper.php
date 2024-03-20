<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form;

final class DataWrapper
{
    /**
     * One of the Ibexa Platform structs, like ContentCreateStruct, UserUpdateStruct and so on.
     */
    public mixed $payload;

    /**
     * Definition of the target.
     *
     * In case of Content or User target, this must be the corresponding ContentType.
     *
     */
    public mixed $definition;

    /**
     * The target struct that applies to. E.g. Content, User, Section object and so on.
     *
     * This target makes sense only in update context, when creating target does not
     * exist (yet to be created).
     *
     */
    public mixed $target;

    /**
     * Construct from payload, target and definition.
     *
     * @param mixed $payload
     * @param mixed|null $target
     * @param mixed|null $definition
     */
    public function __construct(mixed $payload, mixed $definition = null, mixed $target = null)
    {
        $this->payload = $payload;
        $this->definition = $definition;
        $this->target = $target;
    }
}
