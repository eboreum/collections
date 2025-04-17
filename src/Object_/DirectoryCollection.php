<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use Directory;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of Directory, exclusively.
 *
 * @template T of Directory
 * @implements CollectionInterface<T>
 * @extends AbstractNamedClassOrInterfaceCollection<T>
 */
class DirectoryCollection extends AbstractNamedClassOrInterfaceCollection implements CollectionInterface
{
    public static function getHandledClassName(): string
    {
        return Directory::class;
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

    public function first(): ?Directory
    {
        return parent::first();
    }

    public function get(int|string $key): ?Directory
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

    public function last(): ?Directory
    {
        return parent::last();
    }

    public function maxByCallback(Closure $callback): ?Directory
    {
        return parent::maxByCallback($callback);
    }

    public function minByCallback(Closure $callback): ?Directory
    {
        return parent::minByCallback($callback);
    }

    public function next(): ?Directory
    {
        return parent::next();
    }
}
