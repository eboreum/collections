<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use DateTimeImmutable;
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
 * A collection which contains instances of DateTimeImmutable, exclusively.
 *
 * @template T of DateTimeImmutable
 * @extends AbstractNamedClassOrInterfaceCollection<T>
 * @implements GeneratedCollectionInterface<T>
 * @implements MaximumableCollectionInterface<T>
 * @implements MinimumableCollectionInterface<T>
 * @implements SortableCollectionInterface<T>
 * @implements UniqueableCollectionInterface<T>
 */
class DateTimeImmutableCollection
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
        return DateTimeImmutable::class;
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
    public function current(): ?DateTimeImmutable
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find($key): ?DateTimeImmutable
    {
        return parent::find($key);
    }

    /**
     * {@inheritDoc}
     */
    public function first(): ?DateTimeImmutable
    {
        return parent::first();
    }

    /**
     * {@inheritDoc}
     */
    public function get(int|string $key): ?DateTimeImmutable
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
    public function last(): ?DateTimeImmutable
    {
        return parent::last();
    }

    /**
     * {@inheritDoc}
     */
    public function max(): ?DateTimeImmutable
    {
        if (!$this->elements) {
            return null;
        }

        return $this->maxByCallback(static function (DateTimeImmutable $dateTimeImmutable): int {
            return $dateTimeImmutable->getTimestamp();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function maxByCallback(Closure $callback): ?DateTimeImmutable
    {
        return parent::maxByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function min(): ?DateTimeImmutable
    {
        if (!$this->elements) {
            return null;
        }

        return $this->minByCallback(static function (DateTimeImmutable $dateTimeImmutable): int {
            return $dateTimeImmutable->getTimestamp();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function minByCallback(Closure $callback): ?DateTimeImmutable
    {
        return parent::minByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): ?DateTimeImmutable
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
            static function (DateTimeImmutable $a, DateTimeImmutable $b) use ($direction): int {
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
            static function (DateTimeImmutable $element) {
                return (string)$element->getTimestamp();
            },
            $isUsingFirstEncounteredElement,
        );
    }
}
