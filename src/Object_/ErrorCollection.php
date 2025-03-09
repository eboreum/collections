<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;
use Error;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of Error, exclusively.
 *
 * @template T of Error
 * @implements CollectionInterface<T>
 * @extends AbstractNamedClassOrInterfaceCollection<T>
 */
class ErrorCollection extends AbstractNamedClassOrInterfaceCollection implements CollectionInterface
{
    public static function getHandledClassName(): string
    {
        return Error::class;
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

    public function first(): ?Error
    {
        return parent::first();
    }

    public function get(int|string $key): ?Error
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

    public function last(): ?Error
    {
        return parent::last();
    }

    public function maxByCallback(Closure $callback): ?Error
    {
        return parent::maxByCallback($callback);
    }

    public function minByCallback(Closure $callback): ?Error
    {
        return parent::minByCallback($callback);
    }

    public function next(): ?Error
    {
        return parent::next();
    }
}
