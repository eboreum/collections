<?php

declare(strict_types=1);

namespace Eboreum\Collections;

use Eboreum\Collections\Contract\CollectionInterface;

/**
 * {@inheritDoc}
 *
 * Contains values of type object -- any object -- exclusively.
 *
 * If you need a collection of a specific instance, please consider using one of the premade named object collections,
 * found under \Eboreum\Collections\Object_, or create your own custom object collection by extending
 * \Eboreum\Collections\Abstraction\AbstractNamedObjectCollection.
 *
 * @template T2 of object
 * @extends Collection<T2>
 */
class ObjectCollection extends Collection
{
    /**
     * {@inheritDoc}
     */
    public static function isElementAccepted($element): bool
    {
        return is_object($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, T2> $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    /**
     * {@inheritDoc}
     *
     * @param T2 $element
     */
    public function contains($element): bool
    {
        return parent::contains($element);
    }

    /**
     * {@inheritDoc}
     */
    public function current(): ?object
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find(\Closure $callback): ?object
    {
        return parent::find($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function first(): ?object
    {
        return parent::first();
    }

    /**
     * {@inheritDoc}
     */
    public function get($key): ?object
    {
        return parent::get($key);
    }

    /**
     * {@inheritDoc}
     *
     * @param T2 $element
     */
    public function indexOf($element)
    {
        return parent::indexOf($element);
    }

    /**
     * {@inheritDoc}
     */
    public function last(): ?object
    {
        return parent::last();
    }

    /**
     * {@inheritDoc}
     */
    public function maxByCallback(\Closure $callback): ?object
    {
        return parent::maxByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function minByCallback(\Closure $callback): ?object
    {
        return parent::minByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): ?object
    {
        return parent::next();
    }
}
