<?php

declare(strict_types=1);

namespace Eboreum\Collections;

use Closure;
use Collator;
use Eboreum\Collections\Contract\CollectionInterface\UniqueableCollectionInterface;

use function is_string;

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
    public static function isElementAccepted(mixed $element): bool
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

    public function current(): ?string
    {
        return parent::current();
    }

    public function find(Closure $callback): ?string
    {
        return parent::find($callback);
    }

    public function first(): ?string
    {
        return parent::first();
    }

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

    public function last(): ?string
    {
        return parent::last();
    }

    public function maxByCallback(Closure $callback): ?string
    {
        return parent::maxByCallback($callback);
    }

    public function minByCallback(Closure $callback): ?string
    {
        return parent::minByCallback($callback);
    }

    public function next(): ?string
    {
        return parent::next();
    }

    /**
     * Requires either:
     *
     *   - The PHP extension "intl": https://www.php.net/manual/en/class.collator.php
     *   - A polyfill, e.g. https://packagist.org/packages/symfony/polyfill-intl-icu
     *
     * @return StringCollection<T>
     */
    public function toSortedByCollator(Collator $collator): static
    {
        return $this->toSortedByCallback(
            static function (string $a, string $b) use ($collator): int {
                return $collator->compare($a, $b);
            },
        );
    }

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
