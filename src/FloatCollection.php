<?php

declare(strict_types=1);

namespace Eboreum\Collections;

use Closure;
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
 * @template T of float
 * @extends Collection<T>
 * @implements MaximumableCollectionInterface<T>
 * @implements MinimumableCollectionInterface<T>
 * @implements SortableCollectionInterface<T>
 * @implements UniqueableCollectionInterface<T>
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
    public function current(): ?float
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find(Closure $callback): ?float
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
    public function get(int|string $key): ?float
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
    public function maxByCallback(Closure $callback): ?float
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
    public function minByCallback(Closure $callback): ?float
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
     */
    public function toSorted(bool $isAscending = true): static
    {
        $direction = ($isAscending ? 1 : -1);

        return $this->toSortedByCallback(static function (float $a, float $b) use ($direction): int {
            return intval($a - $b) * $direction;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function toUnique(bool $isUsingFirstEncounteredElement = true): static
    {
        return $this->toUniqueByCallback(
            static function (float $element) {
                return (string)$element;
            },
            $isUsingFirstEncounteredElement,
        );
    }
}
