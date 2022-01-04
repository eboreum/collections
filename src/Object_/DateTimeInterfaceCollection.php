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
use Eboreum\Collections\Contract\GeneratedCollectionInterface;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of DateTimeInterface, exclusively.
 *
 * @template T3 of DateTimeInterface
 * @extends AbstractNamedClassOrInterfaceCollection<T3>
 * @implements MaximumableCollectionInterface<T3>
 * @implements MinimumableCollectionInterface<T3>
 * @implements SortableCollectionInterface<T3>
 * @implements UniqueableCollectionInterface<T3>
 */
class DateTimeInterfaceCollection
    extends AbstractNamedClassOrInterfaceCollection
    implements
        MaximumableCollectionInterface,
        MinimumableCollectionInterface,
        SortableCollectionInterface,
        UniqueableCollectionInterface,
        GeneratedCollectionInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getHandledClassName(): string
    {
        return DateTimeInterface::class;
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, T3> $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    /**
     * {@inheritDoc}
     *
     * @param T3 $element
     */
    public function contains($element): bool
    {
        return parent::contains($element);
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
    public function first(): ?DateTimeInterface
    {
        return parent::first();
    }

    /**
     * {@inheritDoc}
     *
     * @param T3 $element
     */
    public function indexOf($element)
    {
        return parent::indexOf($element);
    }

    /**
     * {@inheritDoc}
     */
    public function last(): ?DateTimeInterface
    {
        return parent::last();
    }

    /**
     * {@inheritDoc}
     */
    public function max(): ?DateTimeInterface
    {
        if (!$this->elements) {
            return null;
        }

        return $this->maxByCallback(static function (DateTimeInterface $dateTime): int {
            return $dateTime->getTimestamp();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function maxByCallback(Closure $callback): ?DateTimeInterface
    {
        return parent::maxByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function min(): ?DateTimeInterface
    {
        if (!$this->elements) {
            return null;
        }

        return $this->minByCallback(static function (DateTimeInterface $dateTime): int {
            return $dateTime->getTimestamp();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function minByCallback(Closure $callback): ?DateTimeInterface
    {
        return parent::minByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): ?DateTimeInterface
    {
        return parent::next();
    }

    /**
     * {@inheritDoc}
     *
     * @return static<T3>
     */
    public function toSorted(bool $isAscending = true): self
    {
        $direction = ($isAscending ? 1 : -1);

        $collection = $this->toSortedByCallback(
            static function (DateTimeInterface $a, DateTimeInterface $b) use ($direction): int {
                return ($a->getTimestamp() - $b->getTimestamp()) * $direction;
            }
        );

        assert(is_a($collection, __CLASS__));

        return $collection;
    }

    /**
     * {@inheritDoc}
     *
     * @return static<T3>
     */
    public function toUnique(bool $isUsingFirstEncounteredElement = true): self
    {
        $collection = $this->toUniqueByCallback(
            static function (DateTimeInterface $element) {
                return (string)$element->getTimestamp();
            },
            $isUsingFirstEncounteredElement,
        );

        assert(is_a($collection, __CLASS__));

        return $collection;
    }
}
