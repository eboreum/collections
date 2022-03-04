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
 * @template T of DateTimeInterface
 * @extends AbstractNamedClassOrInterfaceCollection<T>
 * @implements GeneratedCollectionInterface<T>
 * @implements MaximumableCollectionInterface<T>
 * @implements MinimumableCollectionInterface<T>
 * @implements SortableCollectionInterface<T>
 * @implements UniqueableCollectionInterface<T>
 */
class DateTimeInterfaceCollection
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
     */
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
     */
    public function toSorted(bool $isAscending = true): static
    {
        $direction = ($isAscending ? 1 : -1);

        return $this->toSortedByCallback(
            static function (DateTimeInterface $a, DateTimeInterface $b) use ($direction): int {
                return ($a->getTimestamp() - $b->getTimestamp()) * $direction;
            }
        );
    }

    /**
     * {@inheritDoc}
     */
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
