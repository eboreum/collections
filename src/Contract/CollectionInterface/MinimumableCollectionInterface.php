<?php

declare(strict_types=1);

namespace Eboreum\Collections\Contract\CollectionInterface;

use Eboreum\Collections\Contract\CollectionInterface;

/**
 * {@inheritDoc}
 *
 * Denotes that the implementing collection class has a means of determining a minimum value within the collection.
 */
interface MinimumableCollectionInterface extends CollectionInterface
{
    /**
     * Must return the element, which is considered to be the minimum of all elements in the collection. Must return
     * `null` when the collection is empty.
     *
     * Internal logic – exclusively – must handle how the minimum value is found. This may be achieved using the method
     * `minByCallback`.
     *
     * Corresponds to the PHP core function `min`.
     *
     * @see https://www.php.net/manual/en/function.min.php
     *
     * @return mixed|null
     */
    public function min();
}
