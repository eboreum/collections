<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;
use Throwable;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of Throwable, exclusively.
 *
 * @template T of Throwable
 * @implements CollectionInterface<T>
 * @extends AbstractNamedClassOrInterfaceCollection<T>
 */
class ThrowableCollection extends AbstractNamedClassOrInterfaceCollection implements CollectionInterface
{
    public static function getHandledClassName(): string
    {
        return Throwable::class;
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

    public function first(): ?Throwable
    {
        return parent::first();
    }

    public function get(int|string $key): ?Throwable
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

    public function last(): ?Throwable
    {
        return parent::last();
    }

    public function maxByCallback(Closure $callback): ?Throwable
    {
        return parent::maxByCallback($callback);
    }

    public function minByCallback(Closure $callback): ?Throwable
    {
        return parent::minByCallback($callback);
    }

    public function next(): ?Throwable
    {
        return parent::next();
    }
}
