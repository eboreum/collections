<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Contract\GeneratedCollectionInterface;
use Error;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of Error, exclusively.
 *
 * @template T3 of Error
 * @extends AbstractNamedClassOrInterfaceCollection<T3>
 */
class ErrorCollection extends AbstractNamedClassOrInterfaceCollection implements GeneratedCollectionInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getHandledClassName(): string
    {
        return Error::class;
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
    public function current(): ?Error
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find($key): ?Error
    {
        return parent::find($key);
    }

    /**
     * {@inheritDoc}
     */
    public function first(): ?Error
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
    public function last(): ?Error
    {
        return parent::last();
    }

    /**
     * {@inheritDoc}
     */
    public function maxByCallback(Closure $callback): ?Error
    {
        return parent::maxByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function minByCallback(Closure $callback): ?Error
    {
        return parent::minByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): ?Error
    {
        return parent::next();
    }
}
