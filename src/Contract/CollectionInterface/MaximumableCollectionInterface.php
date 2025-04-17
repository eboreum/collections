<?php

declare(strict_types=1);

namespace Eboreum\Collections\Contract\CollectionInterface;

use Eboreum\Collections\Contract\CollectionInterface;

/**
 * {@inheritDoc}
 *
 * Denotes that the implementing collection class has a means of determining a maximum value within the collection.
 *
 * @template T
 * @extends CollectionInterface<T>
 */
interface MaximumableCollectionInterface extends CollectionInterface
{
    /**
     * Must return the element, which is considered to be the maximum of all elements in the collection. Must return
     * `null` when the collection is empty.
     *
     * Internal logic – exclusively – must handle how the maximum value is found. This may be achieved using the method
     * `maxByCallback`.
     *
     * Corresponds to the PHP core function `max`.
     *
     * @see https://www.php.net/manual/en/function.max.php
     */
    public function max(): mixed;
}
