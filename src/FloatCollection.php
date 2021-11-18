<?php

declare(strict_types=1);

namespace Eboreum\Collections;

use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\SortableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\UniqueableCollectionInterface;

/**
 * {@inheritDoc}
 *
 * Contains values of type float, exclusively.
 */
class FloatCollection extends Collection implements SortableCollectionInterface, UniqueableCollectionInterface
{
    /**
     * {@inheritDoc}
     *
     * @param float $element
     */
    public static function assertIsElementAccepted($element): void
    {
        parent::assertIsElementAccepted($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param float $element
     */
    public static function isElementAccepted($element): bool
    {
        return is_float($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, float> $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    /**
     * {@inheritDoc}
     *
     * @param float $element
     */
    public function contains($element): bool
    {
        return parent::contains($element);
    }

    /**
     * {@inheritDoc}
     */
    public function current(): ?float
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find(\Closure $callback): ?float
    {
        return parent::find($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function first(): ?float
    {
        return parent::first();
    }

    /**
     * {@inheritDoc}
     */
    public function get($key): ?float
    {
        return parent::get($key);
    }

    /**
     * {@inheritDoc}
     *
     * @param float $element
     */
    public function indexOf($element)
    {
        return parent::indexOf($element);
    }

    /**
     * {@inheritDoc}
     */
    public function last(): ?float
    {
        return parent::last();
    }

    /**
     * {@inheritDoc}
     */
    public function maxByCallback(\Closure $callback): ?float
    {
        return parent::maxByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function minByCallback(\Closure $callback): ?float
    {
        return parent::minByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): ?float
    {
        return parent::next();
    }

    /**
     * {@inheritDoc}
     *
     * @return array<int|string, float>
     */
    public function toArray(): array
    {
        return parent::toArray();
    }

    /**
     * {@inheritDoc}
     *
     * @return array<int, float>
     */
    public function toArrayValues(): array
    {
        return parent::toArrayValues();
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, float>
     */
    public function toCleared(): self
    {
        return parent::toCleared();
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, float>
     */
    public function toReversed(bool $isPreservingKeys = true): self
    {
        return parent::toReversed($isPreservingKeys);
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, float>
     */
    public function toSequential(): self
    {
        return parent::toSequential();
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, float>
     */
    public function toSorted(bool $isAscending = true): self
    {
        $direction = ($isAscending ? 1 : -1);

        return $this->toSortedByCallback(static function (float $a, float $b) use ($direction) {
            return ($a - $b) * $direction;
        });
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, float>
     */
    public function toSortedByCallback(\Closure $callback): self
    {
        return parent::toSortedByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function toUnique(bool $isUsingFirstEncounteredElement = true): self
    {
        return $this->toUniqueByCallback(
            static function (float $element) {
                return (string)$element;
            },
            $isUsingFirstEncounteredElement,
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, float>
     */
    public function toUniqueByCallback(\Closure $callback, bool $isUsingFirstEncounteredElement = true): self
    {
        return parent::toUniqueByCallback($callback, $isUsingFirstEncounteredElement);
    }

    /**
     * {@inheritDoc}
     *
     * @param float $element
     * @return static<int|string, float>
     */
    public function withAdded($element): self
    {
        return parent::withAdded($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, float> $elements
     * @return static<int|string, float>
     */
    public function withAddedMultiple(array $elements): self
    {
        return parent::withAddedMultiple($elements);
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, float>
     */
    public function withFiltered(\Closure $callback): self
    {
        return parent::withFiltered($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @param FloatCollection<int|string, float> $collection
     * @return static<int|string, float>
     */
    public function withMerged(CollectionInterface $collection): self
    {
        return parent::withMerged($collection);
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, float>
     */
    public function withRemoved($key): self
    {
        return parent::withRemoved($key);
    }

    /**
     * {@inheritDoc}
     *
     * @param float $element
     * @return static<int|string, float>
     */
    public function withRemovedElement($element): self
    {
        return parent::withRemovedElement($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param float $element
     * @return static<int|string, float>
     */
    public function withSet($key, $element): self
    {
        return parent::withSet($key, $element);
    }
}
