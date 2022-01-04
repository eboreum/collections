<?php

declare(strict_types=1);

namespace Eboreum\Collections\Contract;

/**
 * {@inheritDoc}
 *
 * The implementing class will only ever contain values being objects of a certain class or subclass thereof.
 */
interface ObjectCollectionInterface
{
    /**
     * Must return the name of the class being handled. E.g. "stdClass", in which case, it is recommended naming the
     * implementing class "stdClassCollection".
     *
     * @return class-string
     */
    public static function getHandledClassName(): string;
}
