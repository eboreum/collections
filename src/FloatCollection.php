<?php

declare(strict_types=1);

namespace Eboreum\Collections;

use Closure;
use Eboreum\Collections\Contract\CollectionInterface\MaximumableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\MinimumableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\SortableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\UniqueableCollectionInterface;

use function intval;
use function is_float;
use function max;
use function min;

/**
 * {@inheritDoc}
 *
 * Contains values of type float, exclusively.
 *
 * @template T of float
 * @implements MaximumableCollectionInterface<T>
 * @implements MinimumableCollectionInterface<T>
 * @implements SortableCollectionInterface<T>
 * @implements UniqueableCollectionInterface<T>
 * @extends Collection<T>
 */
class FloatCollection extends Collection implements
    MaximumableCollectionInterface,
    MinimumableCollectionInterface,
    SortableCollectionInterface,
    UniqueableCollectionInterface
{
    public static function isElementAccepted(mixed $element): bool
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

    public function current(): ?float
    {
        return parent::current();
    }

    public function find(Closure $callback): ?float
    {
        return parent::find($callback);
    }

    public function first(): ?float
    {
        return parent::first();
    }

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

    public function last(): ?float
    {
        return parent::last();
    }

    public function max(): ?float
    {
        if (!$this->elements) {
            return null;
        }

        return max($this->elements);
    }

    public function maxByCallback(Closure $callback): ?float
    {
        return parent::maxByCallback($callback);
    }

    public function min(): ?float
    {
        if (!$this->elements) {
            return null;
        }

        return min($this->elements);
    }

    public function minByCallback(Closure $callback): ?float
    {
        return parent::minByCallback($callback);
    }

    public function next(): ?float
    {
        return parent::next();
    }

    public function toSorted(bool $isAscending = true): static
    {
        $direction = ($isAscending ? 1 : -1);

        return $this->toSortedByCallback(static function (float $a, float $b) use ($direction): int {
            return intval($a - $b) * $direction;
        });
    }

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
