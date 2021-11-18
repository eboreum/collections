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
 */
class ObjectCollection extends Collection
{
    /**
     * {@inheritDoc}
     *
     * @param object $element
     */
    public static function assertIsElementAccepted($element): void
    {
        parent::assertIsElementAccepted($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param object $element
     */
    public static function isElementAccepted($element): bool
    {
        return is_object($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, object> $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    /**
     * {@inheritDoc}
     *
     * @param object $element
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
     * @param object $element
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

    /**
     * {@inheritDoc}
     *
     * @return array<int|string, object>
     */
    public function toArray(): array
    {
        return parent::toArray();
    }

    /**
     * {@inheritDoc}
     *
     * @return array<int, object>
     */
    public function toArrayValues(): array
    {
        return parent::toArrayValues();
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, object>
     */
    public function toCleared(): self
    {
        return parent::toCleared();
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, object>
     */
    public function toReversed(bool $isPreservingKeys = true): self
    {
        return parent::toReversed($isPreservingKeys);
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, object>
     */
    public function toSequential(): self
    {
        return parent::toSequential();
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, object>
     */
    public function toSortedByCallback(\Closure $callback): self
    {
        return parent::toSortedByCallback($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, object>
     */
    public function toUniqueByCallback(\Closure $callback, bool $isUsingFirstEncounteredElement = true): self
    {
        return parent::toUniqueByCallback($callback, $isUsingFirstEncounteredElement);
    }

    /**
     * {@inheritDoc}
     *
     * @param object $element
     * @return static<int|string, object>
     */
    public function withAdded($element): self
    {
        return parent::withAdded($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, object> $elements
     * @return static<int|string, object>
     */
    public function withAddedMultiple(array $elements): self
    {
        return parent::withAddedMultiple($elements);
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, object>
     */
    public function withFiltered(\Closure $callback): self
    {
        return parent::withFiltered($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @param ObjectCollection<int|string, object> $collection
     * @return static<int|string, object>
     */
    public function withMerged(CollectionInterface $collection): self
    {
        return parent::withMerged($collection);
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, object>
     */
    public function withRemoved($key): self
    {
        return parent::withRemoved($key);
    }

    /**
     * {@inheritDoc}
     *
     * @param object $element
     * @return static<int|string, object>
     */
    public function withRemovedElement($element): self
    {
        return parent::withRemovedElement($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param object $element
     * @return static<int|string, object>
     */
    public function withSet($key, $element): self
    {
        return parent::withSet($key, $element);
    }
}
