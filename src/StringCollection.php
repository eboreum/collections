<?php

declare(strict_types=1);

namespace Eboreum\Collections;

use Closure;
use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\UniqueableCollectionInterface;

/**
 * {@inheritDoc}
 *
 * Contains values of type string, exclusively.
 *
 * @template T2 of string
 * @extends Collection<T2>
 * @implements UniqueableCollectionInterface<T2>
 */
class StringCollection extends Collection implements UniqueableCollectionInterface
{
    /**
     * {@inheritDoc}
     */
    public static function isElementAccepted($element): bool
    {
        return is_string($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, T2> $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    /**
     * {@inheritDoc}
     *
     * @param T2 $element
     */
    public function contains($element): bool
    {
        return parent::contains($element);
    }

    /**
     * {@inheritDoc}
     */
    public function current(): ?string
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find(\Closure $callback): ?string
    {
        return parent::find($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function first(): ?string
    {
        return parent::first();
    }

    /**
     * {@inheritDoc}
     */
    public function get(int|string $key): ?string
    {
        return parent::get($key);
    }

    /**
     * {@inheritDoc}
     *
     * @param T2 $element
     */
    public function indexOf($element): int|string|null
    {
        return parent::indexOf($element);
    }

    /**
     * {@inheritDoc}
     */
    public function last(): ?string
    {
        return parent::last();
    }

    /**
     * {@inheritDoc}
     */
    public function maxByCallback(\Closure $callback): ?string
    {
        return parent::maxByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function minByCallback(\Closure $callback): ?string
    {
        return parent::minByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): ?string
    {
        return parent::next();
    }

    /**
     * {@inheritDoc}
     *
     * @return static<T2>
     */
    public function toUnique(bool $isUsingFirstEncounteredElement = true): self
    {
        $collection = $this->toUniqueByCallback(
            /**
             * @return array<T2>
             */
            static function (string $element) {
                return $element;
            },
            $isUsingFirstEncounteredElement,
        );

        assert(is_a($collection, self::class)); // Make phpstan happy

        return $collection;
    }
}
