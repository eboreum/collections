<?php

declare(strict_types=1);

namespace Eboreum\Collections;

use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\MaximumableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\MinimumableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\SortableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\UniqueableCollectionInterface;

/**
 * {@inheritDoc}
 *
 * Contains values of type float, exclusively.
 *
 * @template T2 of float
 * @extends Collection<T2>
 * @implements MaximumableCollectionInterface<T2>
 * @implements MinimumableCollectionInterface<T2>
 * @implements SortableCollectionInterface<T2>
 * @implements UniqueableCollectionInterface<T2>
 */
class FloatCollection
    extends Collection
    implements
        MaximumableCollectionInterface,
        MinimumableCollectionInterface,
        SortableCollectionInterface,
        UniqueableCollectionInterface
{
    /**
     * {@inheritDoc}
     */
    public static function isElementAccepted($element): bool
    {
        return is_float($element);
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
     * @param T2 $element
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
    public function max(): ?float
    {
        if (!$this->elements) {
            return null;
        }

        $float = max($this->elements);

        assert(is_float($float)); // Should not be possible that $float is false, as we check for the empty array above

        return $float;
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
    public function min(): ?float
    {
        if (!$this->elements) {
            return null;
        }

        $float = min($this->elements);

        assert(is_float($float)); // Should not be possible that $float is false, as we check for the empty array above

        return $float;
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
     * @return static<T2>
     */
    public function toSorted(bool $isAscending = true): self
    {
        $direction = ($isAscending ? 1 : -1);

        $collection = $this->toSortedByCallback(static function (float $a, float $b) use ($direction) {
            return ($a - $b) * $direction;
        });

        assert(is_a($collection, __CLASS__)); // Make phpstan happy

        return $collection;
    }

    /**
     * {@inheritDoc}
     *
     * @return static<T2>
     */
    public function toUnique(bool $isUsingFirstEncounteredElement = true): self
    {
        $collection = $this->toUniqueByCallback(
            static function (float $element) {
                return (string)$element;
            },
            $isUsingFirstEncounteredElement,
        );

        assert(is_a($collection, __CLASS__)); // Make phpstan happy

        return $collection;
    }
}
