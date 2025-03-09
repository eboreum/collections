<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of Closure, exclusively.
 *
 * @template T of Closure
 * @implements CollectionInterface<T>
 * @extends AbstractNamedClassOrInterfaceCollection<T>
 */
class ClosureCollection extends AbstractNamedClassOrInterfaceCollection implements CollectionInterface
{
    public static function getHandledClassName(): string
    {
        return Closure::class;
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, T> $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    /**
     * {@inheritDoc}
     *
     * @param T $element
     */
    public function contains($element): bool
    {
        return parent::contains($element);
    }

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

    public function first(): ?Closure
    {
        return parent::first();
    }

    public function get(int|string $key): ?Closure
    {
        return parent::get($key);
    }

    /**
     * {@inheritDoc}
     *
     * @param T $element
     */
    public function indexOf($element): int|string|null
    {
        return parent::indexOf($element);
    }

    public function last(): ?Closure
    {
        return parent::last();
    }

    public function maxByCallback(Closure $callback): ?Closure
    {
        return parent::maxByCallback($callback);
    }

    public function minByCallback(Closure $callback): ?Closure
    {
        return parent::minByCallback($callback);
    }

    public function next(): ?Closure
    {
        return parent::next();
    }
}
