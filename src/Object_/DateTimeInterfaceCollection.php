<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use DateTimeInterface;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\SortableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\UniqueableCollectionInterface;
use Eboreum\Collections\Contract\GeneratedCollectionInterface;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of DateTimeInterface, exclusively.
 */
class DateTimeInterfaceCollection
    extends AbstractNamedClassOrInterfaceCollection
    implements
        SortableCollectionInterface,
        UniqueableCollectionInterface,
        GeneratedCollectionInterface
{
    /**
     * {@inheritDoc}
     *
     * @param DateTimeInterface $element
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
        return DateTimeInterface::class;
    }

    /**
     * {@inheritDoc}
     *
     * @param DateTimeInterface $element
     */
    public static function isElementAccepted($element): bool
    {
        return parent::isElementAccepted($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, DateTimeInterface> $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    /**
     * {@inheritDoc}
     *
     * @param DateTimeInterface $element
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
    public function find(\Closure $callback): ?DateTimeInterface
    {
        return parent::find($callback);
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
    public function get($key): ?DateTimeInterface
    {
        return parent::get($key);
    }

    /**
     * {@inheritDoc}
     *
     * @param DateTimeInterface $element
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
    public function max(\Closure $callback): ?DateTimeInterface
    {
        return parent::max($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function min(\Closure $callback): ?DateTimeInterface
    {
        return parent::min($callback);
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
     * @return array<int|string, DateTimeInterface>
     */
    public function toArray(): array
    {
        return parent::toArray();
    }

    /**
     * {@inheritDoc}
     *
     * @return array<int, DateTimeInterface>
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
     * @return static<int|string, DateTimeInterface>
     */
    public function toSorted(bool $isAscending = true): self
    {
        $direction = ($isAscending ? 1 : -1);

        return $this->toSortedByCallback(static function (DateTimeInterface $a, DateTimeInterface $b) use ($direction) {
            return ($a->getTimestamp() - $b->getTimestamp()) * $direction;
        });
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, DateTimeInterface>
     */
    public function toSortedByCallback(\Closure $callback): self
    {
        return parent::toSortedByCallback($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, DateTimeInterface>
     */
    public function toUnique(bool $isUsingFirstEncounteredElement = true): self
    {
        return $this->toUniqueByCallback(
            static function (DateTimeInterface $element) {
                return (string)$element->getTimestamp();
            },
            $isUsingFirstEncounteredElement,
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, DateTimeInterface>
     */
    public function toUniqueByCallback(\Closure $callback, bool $isUsingFirstEncounteredElement = true): self
    {
        return parent::toUniqueByCallback($callback, $isUsingFirstEncounteredElement);
    }

    /**
     * {@inheritDoc}
     *
     * @param DateTimeInterface $element
     */
    public function withAdded($element): self
    {
        return parent::withAdded($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, DateTimeInterface> $elements
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
     * @param DateTimeInterfaceCollection $collection
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
     * @param DateTimeInterface $element
     */
    public function withRemovedElement($element): self
    {
        return parent::withRemovedElement($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param DateTimeInterface $element
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
