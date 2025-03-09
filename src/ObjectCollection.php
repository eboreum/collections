<?php

declare(strict_types=1);

namespace Eboreum\Collections;

use Closure;

use function is_object;

/**
 * {@inheritDoc}
 *
 * Contains values of type object -- any object -- exclusively.
 *
 * If you need a collection of a specific instance, please consider using one of the premade named object collections,
 * found under \Eboreum\Collections\Object_, or create your own custom object collection by extending
 * \Eboreum\Collections\Abstraction\AbstractNamedObjectCollection.
 *
 * @template T of object
 * @extends Collection<T>
 */
class ObjectCollection extends Collection
{
    public static function isElementAccepted(mixed $element): bool
    {
        return is_object($element);
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

    public function current(): ?object
    {
        return parent::current();
    }

    public function find(Closure $callback): ?object
    {
        return parent::find($callback);
    }

    public function first(): ?object
    {
        return parent::first();
    }

    public function get(int|string $key): ?object
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

    public function last(): ?object
    {
        return parent::last();
    }

    public function maxByCallback(Closure $callback): ?object
    {
        return parent::maxByCallback($callback);
    }

    public function minByCallback(Closure $callback): ?object
    {
        return parent::minByCallback($callback);
    }

    public function next(): ?object
    {
        return parent::next();
    }
}
