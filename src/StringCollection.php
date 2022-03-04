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
 * @template T of string
 * @extends Collection<T>
 * @implements UniqueableCollectionInterface<T>
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
     * @param T $element
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
     */
    public function toUnique(bool $isUsingFirstEncounteredElement = true): static
    {
        return $this->toUniqueByCallback(
            /**
             * @return array<T>
             */
            static function (string $element) {
                return $element;
            },
            $isUsingFirstEncounteredElement,
        );
    }
}
