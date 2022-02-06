<?php

declare(strict_types=1);

namespace Eboreum\Collections\Contract;

use Closure;
use Eboreum\Collections\Attribute\Method\Overridden;

/**
 * {@inheritDoc}
 *
 * Denotes that the implementing class has been programmatically generated, commonly by
 * "eboreum/collection-class-generator".
 *
 * @see https://packagist.org/packages/eboreum/collection-class-generator
 *
 * @template T
 * @extends CollectionInterface<T>
 */
interface GeneratedCollectionInterface extends CollectionInterface
{
    /**
     * {@inheritDoc}
     */
    #[Overridden]
    public function contains($element): bool;

    /**
     * {@inheritDoc}
     */
    #[Overridden]
    public function current();

    /**
     * {@inheritDoc}
     */
    #[Overridden]
    public function find(Closure $callback);

    /**
     * {@inheritDoc}
     */
    #[Overridden]
    public function first();

    /**
     * {@inheritDoc}
     */
    #[Overridden]
    public function get(int|string $key);

    /**
     * {@inheritDoc}
     */
    #[Overridden]
    public function indexOf($element): int|string|null;

    /**
     * {@inheritDoc}
     */
    #[Overridden]
    public function last();

    /**
     * {@inheritDoc}
     */
    #[Overridden]
    public function maxByCallback(Closure $callback);

    /**
     * {@inheritDoc}
     */
    #[Overridden]
    public function minByCallback(Closure $callback);

    /**
     * {@inheritDoc}
     */
    #[Overridden]
    public function next();
}
