<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Contract\GeneratedCollectionInterface;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of Closure, exclusively.
 *
 * @template T3 of Closure
 * @extends AbstractNamedClassOrInterfaceCollection<T3>
 * @implements GeneratedCollectionInterface<T3>
 */
class ClosureCollection extends AbstractNamedClassOrInterfaceCollection implements GeneratedCollectionInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getHandledClassName(): string
    {
        return Closure::class;
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, T3> $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    /**
     * {@inheritDoc}
     *
     * @param T3 $element
     */
    public function contains($element): bool
    {
        return parent::contains($element);
    }

    /**
     * {@inheritDoc}
     */
    public function current(): ?Closure
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find($key): ?Closure
    {
        return parent::find($key);
    }

    /**
     * {@inheritDoc}
     */
    public function first(): ?Closure
    {
        return parent::first();
    }

    /**
     * {@inheritDoc}
     */
    public function get(int|string $key): ?Closure
    {
        return parent::get($key);
    }

    /**
     * {@inheritDoc}
     *
     * @param T3 $element
     */
    public function indexOf($element): int|string|null
    {
        return parent::indexOf($element);
    }

    /**
     * {@inheritDoc}
     */
    public function last(): ?Closure
    {
        return parent::last();
    }

    /**
     * {@inheritDoc}
     */
    public function maxByCallback(Closure $callback): ?Closure
    {
        return parent::maxByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function minByCallback(Closure $callback): ?Closure
    {
        return parent::minByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): ?Closure
    {
        return parent::next();
    }
}
