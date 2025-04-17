<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;
use SplFileObject;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of SplFileObject, exclusively.
 *
 * @template T of SplFileObject
 * @implements CollectionInterface<T>
 * @extends AbstractNamedClassOrInterfaceCollection<T>
 */
class SplFileObjectCollection extends AbstractNamedClassOrInterfaceCollection implements CollectionInterface
{
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

    public function first(): ?SplFileObject
    {
        return parent::first();
    }

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

    public function last(): ?SplFileObject
    {
        return parent::last();
    }

    public function maxByCallback(Closure $callback): ?SplFileObject
    {
        return parent::maxByCallback($callback);
    }

    public function minByCallback(Closure $callback): ?SplFileObject
    {
        return parent::minByCallback($callback);
    }

    public function next(): ?SplFileObject
    {
        return parent::next();
    }
}
