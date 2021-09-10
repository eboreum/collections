<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Contract\GeneratedCollectionInterface;
use SplFileObject;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of SplFileObject, exclusively.
 */
class SplFileObjectCollection extends AbstractNamedClassOrInterfaceCollection implements GeneratedCollectionInterface
{
    /**
     * {@inheritDoc}
     *
     * @param SplFileObject $element
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
        return SplFileObject::class;
    }

    /**
     * {@inheritDoc}
     *
     * @param SplFileObject $element
     */
    public static function isElementAccepted($element): bool
    {
        return parent::isElementAccepted($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, SplFileObject> $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    /**
     * {@inheritDoc}
     *
     * @param SplFileObject $element
     */
    public function contains($element): bool
    {
        return parent::contains($element);
    }

    /**
     * {@inheritDoc}
     */
    public function current(): ?SplFileObject
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find(\Closure $callback): ?SplFileObject
    {
        return parent::find($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function first(): ?SplFileObject
    {
        return parent::first();
    }

    /**
     * {@inheritDoc}
     */
    public function get($key): ?SplFileObject
    {
        return parent::get($key);
    }

    /**
     * {@inheritDoc}
     *
     * @param SplFileObject $element
     */
    public function indexOf($element)
    {
        return parent::indexOf($element);
    }

    /**
     * {@inheritDoc}
     */
    public function last(): ?SplFileObject
    {
        return parent::last();
    }

    /**
     * {@inheritDoc}
     */
    public function max(\Closure $callback): ?SplFileObject
    {
        return parent::max($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function min(\Closure $callback): ?SplFileObject
    {
        return parent::min($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): ?SplFileObject
    {
        return parent::next();
    }

    /**
     * {@inheritDoc}
     *
     * @return array<int|string, SplFileObject>
     */
    public function toArray(): array
    {
        return parent::toArray();
    }

    /**
     * {@inheritDoc}
     *
     * @return array<int, SplFileObject>
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
     */
    public function toSortedByCallback(\Closure $callback): self
    {
        return parent::toSortedByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function toUniqueByCallback(\Closure $callback, bool $isUsingFirstEncounteredElement = true): self
    {
        return parent::toUniqueByCallback($callback, $isUsingFirstEncounteredElement);
    }

    /**
     * {@inheritDoc}
     *
     * @param SplFileObject $element
     */
    public function withAdded($element): self
    {
        return parent::withAdded($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, SplFileObject> $elements
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
     * @param SplFileObjectCollection $collection
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
     * @param SplFileObject $element
     */
    public function withRemovedElement($element): self
    {
        return parent::withRemovedElement($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param SplFileObject $element
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
