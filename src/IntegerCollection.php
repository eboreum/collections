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
 * @template T of int
 * @extends Collection<T>
 * @implements MaximumableCollectionInterface<T>
 * @implements MinimumableCollectionInterface<T>
 * @implements SortableCollectionInterface<T>
 * @implements UniqueableCollectionInterface<T>
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
     * @param T $element
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
     */
    public function toSorted(bool $isAscending = true): static
    {
        $direction = ($isAscending ? 1 : -1);

        return $this->toSortedByCallback(static function (int $a, int $b) use ($direction) {
            return ($a - $b) * $direction;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function toUnique(bool $isUsingFirstEncounteredElement = true): static
    {
        return $this->toUniqueByCallback(
            static function (int $element) {
                return (string)$element;
            },
            $isUsingFirstEncounteredElement,
        );
    }
}
