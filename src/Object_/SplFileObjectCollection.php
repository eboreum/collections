<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Contract\GeneratedCollectionInterface;
use SplFileObject;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of SplFileObject, exclusively.
 *
 * @template T of SplFileObject
 * @extends AbstractNamedClassOrInterfaceCollection<T>
 * @implements GeneratedCollectionInterface<T>
 */
class SplFileObjectCollection extends AbstractNamedClassOrInterfaceCollection implements GeneratedCollectionInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getHandledClassName(): string
    {
        return SplFileObject::class;
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

    /**
     * {@inheritDoc}
     */
    public function current(): ?SplFileObject
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find($key): ?SplFileObject
    {
        return parent::find($key);
    }

    /**
     * {@inheritDoc}
     */
    public function first(): ?SplFileObject
    {
        return parent::first();
    }

    /**
     * {@inheritDoc}
     */
    public function get(int|string $key): ?SplFileObject
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

    /**
     * {@inheritDoc}
     */
    public function last(): ?SplFileObject
    {
        return parent::last();
    }

    /**
     * {@inheritDoc}
     */
    public function maxByCallback(Closure $callback): ?SplFileObject
    {
        return parent::maxByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function minByCallback(Closure $callback): ?SplFileObject
    {
        return parent::minByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): ?SplFileObject
    {
        return parent::next();
    }
}
