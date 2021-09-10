<?php

declare(strict_types=1);

namespace Eboreum\Collections\Contract\CollectionInterface;

use Eboreum\Collections\Contract\CollectionInterface;

/**
 * {@inheritDoc}
 *
 * Denotes that the implementing collection class may be made unique (immutably) using internaly logic within the
 * collection, exclusively.
 */
interface UniqueableCollectionInterface extends CollectionInterface
{
    /**
     * Must return a clone containing only elements considered to be unique for the given collection.
     *
     * Internal logic – exclusively – must handle how uniqueness is considered. This may be achieved using the method
     * `toUniqueByCallback`.
     */
    public function toUnique(bool $isUsingFirstEncounteredElement = true): self;
}
