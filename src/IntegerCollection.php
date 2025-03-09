<?php

declare(strict_types=1);

namespace Eboreum\Collections;

use Closure;
use Eboreum\Collections\Contract\CollectionInterface\MaximumableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\MinimumableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\SortableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\UniqueableCollectionInterface;

use function is_int;
use function max;
use function min;

/**
 * {@inheritDoc}
 *
 * Contains values of type integer (int), exclusively.
 *
 * @template T of int
 * @implements MaximumableCollectionInterface<T>
 * @implements MinimumableCollectionInterface<T>
 * @implements SortableCollectionInterface<T>
 * @implements UniqueableCollectionInterface<T>
 * @extends Collection<T>
 */
class IntegerCollection extends Collection implements
    MaximumableCollectionInterface,
    MinimumableCollectionInterface,
    SortableCollectionInterface,
    UniqueableCollectionInterface
{
    public static function isElementAccepted(mixed $element): bool
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

    public function current(): ?int
    {
        return parent::current();
    }

    public function find(Closure $callback): ?int
    {
        return parent::find($callback);
    }

    public function first(): ?int
    {
        return parent::first();
    }

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

    public function last(): ?int
    {
        return parent::last();
    }

    public function max(): ?int
    {
        if (!$this->elements) {
            return null;
        }

        return max($this->elements);
    }

    public function maxByCallback(Closure $callback): ?int
    {
        return parent::maxByCallback($callback);
    }

    public function min(): ?int
    {
        if (!$this->elements) {
            return null;
        }

        return min($this->elements);
    }

    public function minByCallback(Closure $callback): ?int
    {
        return parent::minByCallback($callback);
    }

    public function next(): ?int
    {
        return parent::next();
    }

    public function toSorted(bool $isAscending = true): static
    {
        $direction = ($isAscending ? 1 : -1);

        return $this->toSortedByCallback(static function (int $a, int $b) use ($direction) {
            return ($a - $b) * $direction;
        });
    }

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
