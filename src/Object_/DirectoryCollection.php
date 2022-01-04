<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use Directory;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Contract\GeneratedCollectionInterface;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of Directory, exclusively.
 *
 * @template T3 of Directory
 * @extends AbstractNamedClassOrInterfaceCollection<T3>
 */
class DirectoryCollection extends AbstractNamedClassOrInterfaceCollection implements GeneratedCollectionInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getHandledClassName(): string
    {
        return Directory::class;
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
    public function current(): ?Directory
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find($key): ?Directory
    {
        return parent::find($key);
    }

    /**
     * {@inheritDoc}
     */
    public function first(): ?Directory
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
    public function last(): ?Directory
    {
        return parent::last();
    }

    /**
     * {@inheritDoc}
     */
    public function maxByCallback(Closure $callback): ?Directory
    {
        return parent::maxByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function minByCallback(Closure $callback): ?Directory
    {
        return parent::minByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): ?Directory
    {
        return parent::next();
    }
}
