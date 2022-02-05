<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use DateTime;
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
 * A collection which contains instances of DateTime, exclusively.
 *
 * @template T3 of DateTime
 * @extends AbstractNamedClassOrInterfaceCollection<T3>
 * @implements GeneratedCollectionInterface<T3>
 * @implements MaximumableCollectionInterface<T3>
 * @implements MinimumableCollectionInterface<T3>
 * @implements SortableCollectionInterface<T3>
 * @implements UniqueableCollectionInterface<T3>
 */
class DateTimeCollection
    extends AbstractNamedClassOrInterfaceCollection
    implements
        GeneratedCollectionInterface,
        MaximumableCollectionInterface,
        MinimumableCollectionInterface,
        SortableCollectionInterface,
        UniqueableCollectionInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getHandledClassName(): string
    {
        return DateTime::class;
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
    public function current(): ?DateTime
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find($key): ?DateTime
    {
        return parent::find($key);
    }

    /**
     * {@inheritDoc}
     */
    public function first(): ?DateTime
    {
        return parent::first();
    }

    /**
     * {@inheritDoc}
     */
    public function get(int|string $key): ?DateTime
    {
        return parent::get($key);
    }

    /**
     * {@inheritDoc}
     *
     * @param T3 $element
     */
    public function indexOf($element): int|string|null
    {
        return parent::indexOf($element);
    }

    /**
     * {@inheritDoc}
     */
    public function last(): ?DateTime
    {
        return parent::last();
    }

    /**
     * {@inheritDoc}
     */
    public function max(): ?DateTime
    {
        if (!$this->elements) {
            return null;
        }

        return $this->maxByCallback(static function (DateTime $dateTime): int {
            return $dateTime->getTimestamp();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function maxByCallback(Closure $callback): ?DateTime
    {
        return parent::maxByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function min(): ?DateTime
    {
        if (!$this->elements) {
            return null;
        }

        return $this->minByCallback(static function (DateTime $dateTime): int {
            return $dateTime->getTimestamp();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function minByCallback(Closure $callback): ?DateTime
    {
        return parent::minByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): ?DateTime
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
            static function (DateTime $a, DateTime $b) use ($direction): int {
                return ($a->getTimestamp() - $b->getTimestamp()) * $direction;
            }
        );

        assert(is_a($collection, self::class));

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
            static function (DateTime $element) {
                return (string)$element->getTimestamp();
            },
            $isUsingFirstEncounteredElement,
        );

        assert(is_a($collection, self::class));

        return $collection;
    }
}
