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
 * Contains values of type integer (int), exclusively.
 *
 * @template T2 of int
 * @extends Collection<T2>
 * @implements MaximumableCollectionInterface<T2>
 * @implements MinimumableCollectionInterface<T2>
 * @implements SortableCollectionInterface<T2>
 * @implements UniqueableCollectionInterface<T2>
 */
class IntegerCollection
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
        return is_int($element);
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
    public function current(): ?int
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find(\Closure $callback): ?int
    {
        return parent::find($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function first(): ?int
    {
        return parent::first();
    }

    /**
     * {@inheritDoc}
     */
    public function get(int|string $key): ?int
    {
        return parent::get($key);
    }

    /**
     * {@inheritDoc}
     *
     * @param T2 $element
     */
    public function indexOf($element): int|string|null
    {
        return parent::indexOf($element);
    }

    /**
     * {@inheritDoc}
     */
    public function last(): ?int
    {
        return parent::last();
    }

    /**
     * {@inheritDoc}
     */
    public function max(): ?int
    {
        if (!$this->elements) {
            return null;
        }

        $int = max($this->elements);

        assert(is_int($int)); // Should not be possible that $int is false, as we check for the empty array above

        return $int;
    }

    /**
     * {@inheritDoc}
     */
    public function maxByCallback(\Closure $callback): ?int
    {
        return parent::maxByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function min(): ?int
    {
        if (!$this->elements) {
            return null;
        }

        $int = min($this->elements);

        assert(is_int($int)); // Should not be possible that $int is false, as we check for the empty array above

        return $int;
    }

    /**
     * {@inheritDoc}
     */
    public function minByCallback(\Closure $callback): ?int
    {
        return parent::minByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): ?int
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

        $collection = $this->toSortedByCallback(static function (int $a, int $b) use ($direction) {
            return ($a - $b) * $direction;
        });

        assert(is_a($collection, self::class)); // Make phpstan happy

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
            static function (int $element) {
                return (string)$element;
            },
            $isUsingFirstEncounteredElement,
        );

        assert(is_a($collection, self::class)); // Make phpstan happy

        return $collection;
    }
}
