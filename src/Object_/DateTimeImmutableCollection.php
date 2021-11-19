<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

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
 */
class DateTimeImmutableCollection
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
     *
     * @param DateTimeImmutable $element
     */
    public static function assertIsElementAccepted($element): void
    {
        parent::assertIsElementAccepted($element);
    }

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
     * @param DateTimeImmutable $element
     */
    public static function isElementAccepted($element): bool
    {
        return parent::isElementAccepted($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, DateTimeImmutable> $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    /**
     * {@inheritDoc}
     *
     * @param DateTimeImmutable $element
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
    public function find(\Closure $callback): ?DateTimeImmutable
    {
        return parent::find($callback);
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
    public function get($key): ?DateTimeImmutable
    {
        return parent::get($key);
    }

    /**
     * {@inheritDoc}
     *
     * @param DateTimeImmutable $element
     */
    public function indexOf($element)
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
    public function maxByCallback(\Closure $callback): ?DateTimeImmutable
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
    public function minByCallback(\Closure $callback): ?DateTimeImmutable
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
     *
     * @return array<int|string, DateTimeImmutable>
     */
    public function toArray(): array
    {
        return parent::toArray();
    }

    /**
     * {@inheritDoc}
     *
     * @return array<int, DateTimeImmutable>
     */
    public function toArrayValues(): array
    {
        return parent::toArrayValues();
    }

    /**
     * {@inheritDoc}
     */
    public function toCleared(): self
    {
        return parent::toCleared();
    }

    /**
     * {@inheritDoc}
     */
    public function toReversed(bool $isPreservingKeys = true): self
    {
        return parent::toReversed($isPreservingKeys);
    }

    /**
     * {@inheritDoc}
     */
    public function toSequential(): self
    {
        return parent::toSequential();
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, DateTimeImmutable>
     */
    public function toSorted(bool $isAscending = true): self
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
     *
     * @return static<int|string, DateTimeImmutable>
     */
    public function toSortedByCallback(\Closure $callback): self
    {
        return parent::toSortedByCallback($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, DateTimeImmutable>
     */
    public function toUnique(bool $isUsingFirstEncounteredElement = true): self
    {
        return $this->toUniqueByCallback(
            static function (DateTimeImmutable $element) {
                return (string)$element->getTimestamp();
            },
            $isUsingFirstEncounteredElement,
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, DateTimeImmutable>
     */
    public function toUniqueByCallback(\Closure $callback, bool $isUsingFirstEncounteredElement = true): self
    {
        return parent::toUniqueByCallback($callback, $isUsingFirstEncounteredElement);
    }

    /**
     * {@inheritDoc}
     *
     * @param DateTimeImmutable $element
     */
    public function withAdded($element): self
    {
        return parent::withAdded($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, DateTimeImmutable> $elements
     */
    public function withAddedMultiple(array $elements): self
    {
        return parent::withAddedMultiple($elements);
    }

    /**
     * {@inheritDoc}
     */
    public function withFiltered(\Closure $callback): self
    {
        return parent::withFiltered($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @param DateTimeImmutableCollection $collection
     */
    public function withMerged(CollectionInterface $collection): self
    {
        return parent::withMerged($collection);
    }

    /**
     * {@inheritDoc}
     */
    public function withRemoved($key): self
    {
        return parent::withRemoved($key);
    }

    /**
     * {@inheritDoc}
     *
     * @param DateTimeImmutable $element
     */
    public function withRemovedElement($element): self
    {
        return parent::withRemovedElement($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param DateTimeImmutable $element
     */
    public function withSet($key, $element): self
    {
        return parent::withSet($key, $element);
    }

    /**
     * {@inheritDoc}
     */
    public function withSliced(int $offset, ?int $length = null): self
    {
        return parent::withSliced($offset, $length);
    }
}
