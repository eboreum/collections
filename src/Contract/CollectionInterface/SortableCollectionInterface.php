<?php

declare(strict_types=1);

namespace Eboreum\Collections\Contract\CollectionInterface;

use Eboreum\Collections\Contract\CollectionInterface;

/**
 * {@inheritDoc}
 *
 * Denotes that the implementing collection class may be sorted (immutably) using internaly logic within the collection,
 * exclusively.
 *
 * @template T
 * @extends CollectionInterface<T>
 */
interface SortableCollectionInterface extends CollectionInterface
{
    /**
     * Must return a clone containing sorted elements for the given collection.
     *
     * This may be achieved using the method `toSortedByCallback`.
     */
    public function toSorted(bool $isAscending = true): static;
}
