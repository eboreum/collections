<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use DateTimeInterface;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\MaximumableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\MinimumableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\SortableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\UniqueableCollectionInterface;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of DateTimeInterface, exclusively.
 *
 * @template T of DateTimeInterface
 * @implements CollectionInterface<T>
 * @implements MaximumableCollectionInterface<T>
 * @implements MinimumableCollectionInterface<T>
 * @implements SortableCollectionInterface<T>
 * @implements UniqueableCollectionInterface<T>
 * @extends AbstractNamedClassOrInterfaceCollection<T>
 */
class DateTimeInterfaceCollection extends AbstractNamedClassOrInterfaceCollection implements
    CollectionInterface,
    MaximumableCollectionInterface,
    MinimumableCollectionInterface,
    SortableCollectionInterface,
    UniqueableCollectionInterface
{
    public static function getHandledClassName(): string
    {
        return DateTimeInterface::class;
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

    public function current(): ?DateTimeInterface
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find($key): ?DateTimeInterface
    {
        return parent::find($key);
    }

    public function first(): ?DateTimeInterface
    {
        return parent::first();
    }

    public function get(int|string $key): ?DateTimeInterface
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

    public function last(): ?DateTimeInterface
    {
        return parent::last();
    }

    public function max(): ?DateTimeInterface
    {
        if (!$this->elements) {
            return null;
        }

        return $this->maxByCallback(static function (DateTimeInterface $dateTime): int {
            return $dateTime->getTimestamp();
        });
    }

    public function maxByCallback(Closure $callback): ?DateTimeInterface
    {
        return parent::maxByCallback($callback);
    }

    public function min(): ?DateTimeInterface
    {
        if (!$this->elements) {
            return null;
        }

        return $this->minByCallback(static function (DateTimeInterface $dateTime): int {
            return $dateTime->getTimestamp();
        });
    }

    public function minByCallback(Closure $callback): ?DateTimeInterface
    {
        return parent::minByCallback($callback);
    }

    public function next(): ?DateTimeInterface
    {
        return parent::next();
    }

    public function toSorted(bool $isAscending = true): static
    {
        $direction = ($isAscending ? 1 : -1);

        return $this->toSortedByCallback(
            static function (DateTimeInterface $a, DateTimeInterface $b) use ($direction): int {
                return ($a->getTimestamp() - $b->getTimestamp()) * $direction;
            }
        );
    }

    public function toUnique(bool $isUsingFirstEncounteredElement = true): static
    {
        return $this->toUniqueByCallback(
            static function (DateTimeInterface $element) {
                return (string)$element->getTimestamp();
            },
            $isUsingFirstEncounteredElement,
        );
    }
}
