<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Contract\GeneratedCollectionInterface;
use SplFileObject;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of SplFileObject, exclusively.
 *
 * @template T3 of SplFileObject
 * @extends AbstractNamedClassOrInterfaceCollection<T3>
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
     *
     * @param T3 $element
     */
    public function indexOf($element)
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
    public function maxByCallback(\Closure $callback): ?SplFileObject
    {
        return parent::maxByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function minByCallback(\Closure $callback): ?SplFileObject
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
