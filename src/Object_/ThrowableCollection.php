<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Contract\GeneratedCollectionInterface;
use Throwable;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of Throwable, exclusively.
 *
 * @template T3 of Throwable
 * @extends AbstractNamedClassOrInterfaceCollection<T3>
 */
class ThrowableCollection extends AbstractNamedClassOrInterfaceCollection implements GeneratedCollectionInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getHandledClassName(): string
    {
        return Throwable::class;
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
    public function current(): ?Throwable
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find($key): ?Throwable
    {
        return parent::find($key);
    }

    /**
     * {@inheritDoc}
     */
    public function first(): ?Throwable
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
    public function last(): ?Throwable
    {
        return parent::last();
    }

    /**
     * {@inheritDoc}
     */
    public function maxByCallback(Closure $callback): ?Throwable
    {
        return parent::maxByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function minByCallback(Closure $callback): ?Throwable
    {
        return parent::minByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): ?Throwable
    {
        return parent::next();
    }
}
