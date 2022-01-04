<?php

declare(strict_types=1);

namespace Eboreum\Collections\Contract;

use Closure;
use Eboreum\Collections\Exception\InvalidArgumentException;
use Eboreum\Collections\Exception\RuntimeException;

/**
 * {@inheritDoc}
 *
 * The implementing class must be immutable, but changing the array pointer is allowed.
 *
 * Do NOT extend this interface with \ArrayAccess. This library exists to combat arbitrary arrays and this interface
 * is immutable, which \ArrayAccess would break.
 *
 * @template T
 * @extends \IteratorAggregate<int|string, T>
 */
interface CollectionInterface extends ImmutableObjectInterface, \Countable, \IteratorAggregate
{
    /**
     * Do nothing if the provided $element argument is accepted in the implementing collection class. Otherwise,
     * must throw an InvalidArgumentException.
     *
     * @param mixed $element
     * @throws InvalidArgumentException
     */
    public static function assertIsElementAccepted($element): void;

    /**
     * Returns a human readable array with strings describing the sequence of invalid elements.
     *
     * @param array<int|string, mixed> $elements
     * @return array<string>
     */
    public static function makeInvalids(array $elements): array;

    /**
     * Returns `true` when the $element arguemnt is accepted in the implementing collection class. Otherwise, returns
     * `false`.
     *
     * @param mixed $element
     */
    public static function isElementAccepted($element): bool;

    /**
     * @param array<int|string, T> $elements Must throw a RuntimeException when one or more elements are not accepted
     *                                       by the implementing collection class.
     * @throws RuntimeException
     */
    public function __construct(array $elements = []);

    /**
     * Must chunk the current collection into sub-collections, producing a new collection containing n collections of T,
     * where T is the implementing class. Each sub-collection must be a clone of T.
     *
     * Corresponds to the core PHP function `array_chunk`.
     *
     * @see https://www.php.net/manual/en/function.array-chunk.php
     *
     * @param int<1, max> $chunkSize Must be > 0. Otherwise, a RuntimeException must be thrown.
     * @throws RuntimeException
     * @return CollectionInterface<T>
     */
    public function chunk(int $chunkSize): CollectionInterface;

    /**
     * Returns `true` when the collection contains the $element argument. Otherwise, returns `false`.
     * Must throw a RuntimeException when the $element argument is invalid for the implementing collection class.
     *
     * @param T $element
     * @throws RuntimeException
     */
    public function contains($element): bool;

    /**
     * Returns the current element for the array pointer in the collection's elements. If empty, returns `null`.
     *
     * Corresponds to the core PHP function `current`.
     *
     * @see https://www.php.net/manual/en/function.current.php
     *
     * @return T|null
     */
    public function current();

    /**
     * Iterates over all elements in the current collection. Cannot break out of the loop. To do so, use the `every`
     * method instead.
     *
     * Argument $callback will receive the following arguments:
     *
     *   - mixed $value: The current element's value.
     *   - int|string $key: The current element's key.
     *   - object|null $carry: An object, which may be utilized to carry over information between iterations.
     *
     * @param object|null $carry Corresponds to the $carry argument in the $callback.
     * @throws RuntimeException
     */
    public function each(Closure $callback, ?object $carry = null): void;

    /**
     * Similar to the `each` method, in that it iterates over all elements in the current collection. However, it
     * differs from the `each` method in that the `every` methods allows for breaking out of the loop.
     *
     * Argument $callback will receive the following arguments:
     *
     *   - mixed $value: The current element's value.
     *   - int|string $key: The current element's key.
     *   - object|null $carry: An object, which may be utilized to carry over information between iterations.
     *
     * To break out of the iteration (like the `break` keyword in an ordinary loop), return `false` inside the
     * $callback. Returning nothing (void), `null`, or `true` will cause the loop to continue.
     *
     * Any other return value returned by $callback must cause an exception to be thrown.
     *
     * @param object|null $carry Corresponds to the $carry argument in the $callback.
     * @throws RuntimeException
     */
    public function every(Closure $callback, ?object $carry = null): void;

    /**
     * Will attempt to find the first element by value in the collection, using the specified callback. When nothing is
     * found, `null` is returned.
     *
     * @param Closure $callback This closure will be called with arguments `mixed $v` and `int|string $k`, where $v is
     *                          an element contained in the current collection and $k is the element's respective key.
     * @throws RuntimeException
     * @return T|null
     */
    public function find(Closure $callback);

    /**
     * Returns the first element in the collection's elements. If empty, returns `null`. Moves the array pointer.
     *
     * Corresponds to the core PHP function `reset`.
     *
     * @see https://www.php.net/manual/en/function.reset.php
     *
     * @return T|null
     */
    public function first();

    /**
     * Returns the first key – int or string – in the collection. If empty, returns `null`.
     *
     * Corresponds to the core PHP function `array_key_first`.
     *
     * @see https://www.php.net/manual/en/function.array-key-first.php
     *
     * @return int|string|null
     */
    public function firstKey();

    /**
     * Returns the array keys for the elements in the current collection.
     *
     * Corresponds to the core PHP function `array_keys`.
     *
     * @see https://www.php.net/manual/en/function.array-keys.php
     *
     * @return array<int|string>
     */
    public function getKeys(): array;

    /**
     * Returns the element at the $key position in the collection's elements. If key does not exist, returns `null`.
     * Must throw a RuntimeException when argument $key is invalid.
     *
     * @param int|string $key
     * @throws InvalidArgumentException
     * @return T|null
     */
    public function get($key);

    /**
     * Returns `true`, if the argument $key exists as an array key in the collection's elements. Otherwise, returns
     * `false`.
     * Must throw a RuntimeException when argument $key is invalid.
     *
     * @param int|string $key
     * @throws InvalidArgumentException
     */
    public function has($key): bool;

    /**
     * Returns `true`, if the argument $key exists as an array key in the collection's elements. Otherwise, returns
     * `false`.
     *
     * Corresponds to the core PHP function `array_search`.
     *
     * @see https://www.php.net/manual/en/function.array-search.php
     *
     * @param T $element
     * @throws InvalidArgumentException
     * @return int|string|null
     */
    public function indexOf($element);

    /**
     * Returns the key for the current element (array pointer) in the collection's elements. If key does not exist,
     * returns `null`.
     *
     * Corresponds to the core PHP function `key`.
     *
     * @see https://www.php.net/manual/en/function.key.php
     *
     * @return int|string|null
     */
    public function key();

    /**
     * Returns the last element in the collection's elements. If empty, returns `null`. Moves the array pointer.
     *
     * Corresponds to the core PHP function `end`.
     *
     * @see https://www.php.net/manual/en/function.end.php
     *
     * @return T|null
     */
    public function last();

    /**
     * Returns the first key – int or string – in the collection. If empty, returns `null`.
     *
     * Corresponds to the core PHP function `array_key_last`.
     *
     * @see https://www.php.net/manual/en/function.array-key-last.php
     *
     * @return int|string|null
     */
    public function lastKey();

    /**
     * Map the contents of the collection and return the mapped array.
     *
     * Corresponds largely to the core PHP function `array_map`. However, this implementation includes both value and
     * key.
     *
     * @see https://www.php.net/manual/en/function.array-map.php
     *
     * Argument $callback will receive the following arguments:
     *
     *   - mixed $value: The current element's value.
     *   - int|string $key: The current element's key.
     *
     * @return array<int|string, T>
     */
    public function map(Closure $callback): array;

    /**
     * Returns the element, which by the return value of the $callback argument, is considered to be the greatest in the
     * collection. When collection is empty, `null` is returned.
     *
     * Argument $callback will receive the following arguments:
     *
     *   - mixed $value: The current element's value.
     *   - int|string $key: The current element's key.
     *
     * Argument $callback must return an integer. Any other return value returned by $callback must cause an exception
     * to be thrown.
     *
     * @throws RuntimeException
     * @return T|null
     */
    public function maxByCallback(Closure $callback);

    /**
     * Returns the element, which by the return value of the $callback argument, is considered to be the smallest in the
     * collection. When collection is empty, `null` is returned.
     *
     * Argument $callback will receive the following arguments:
     *
     *   - mixed $value: The current element's value.
     *   - int|string $key: The current element's key.
     *
     * Argument $callback must return an integer. Any other return value returned by $callback must cause an exception
     * to be thrown.
     *
     * @throws RuntimeException
     * @return T|null
     */
    public function minByCallback(Closure $callback);

    /**
     * Returns the next element in the collection's elements. If empty, returns `null`. Moves the array pointer.
     *
     * Corresponds to the core PHP function `next`.
     *
     * @see https://www.php.net/manual/en/function.next.php
     *
     * @return T|null
     */
    public function next();

    /**
     * Return the collection's elements "as is"; keys and values intact.
     *
     * @return array<int|string, T>
     */
    public function toArray(): array;

    /**
     * Similar to `toArray`, but array keys are sequential; i.e. wrapped in `array_values`.
     *
     * @see https://www.php.net/manual/en/function.array-values.php
     *
     * @return array<int, T>
     */
    public function toArrayValues(): array;

    /**
     * Remove all elements from a clone of the current collection.
     * Must return a clone.
     *
     * @return static<T>
     */
    public function toCleared(): self;

    /**
     * Equivalent of `array_values`. Makes the contained elements in a clone of the current instance exist in a
     * sequential array, with all keys being numerical, starting from index 0.
     *
     * Must return a clone.
     *
     * @see https://www.php.net/manual/en/function.array-values.php
     *
     * @return static<T>
     */
    public function toSequential(): self;

    /**
     * Reverses the order of elements in the clone of a the current collection using `array_reverse`.
     *
     * Must return a clone.
     *
     * @param bool $isPreservingKeys Notice, this is different from the default value of $preserve_keys in
     *                               the `array_reverse` function.
     *                               When `true`, array keys are preserved. Otherwise, they are not (i.e. becoming
     *                               sequential).
     * @return static<T>
     */
    public function toReversed(bool $isPreservingKeys = true): self;

    /**
     * Sorts using the `uasort` function.
     *
     * Argument $callback will have the following parameters:
     *
     *   - mixed $a: An element A to test, which is present within the collection.
     *   - mixed $b: An element B to test, which is present within the collection.
     *
     * Argument $callback must return an integer. Any other value returned by $callback must cause an exception to be
     * thrown.
     *
     * Must return a clone.
     *
     * @throws RuntimeException
     * @return static<T>
     */
    public function toSortedByCallback(Closure $callback): self;

    /**
     * Produces a clone containing only elements which are considered to be unique, where the uniqueness is determined
     * by the string returned by the $callback argument. Preserves array keys.
     *
     * Argument $callback will have the following parameters:
     *
     *   - mixed $value: An element within the current collection.
     *   - int|string $key: An array key.
     *
     * Argument $callback must return a string. Any other value returned by $callback must cause an exception to be
     * thrown.
     *
     * Must return a clone.
     *
     * @param bool $isUsingFirstEncounteredElement
     *                                          When `true` and when two or more elements, which have produced the same
     *                                          unique string, exist, only the first element will will exist in the
     *                                          resulting collection. Otherwise, only the last element will exist in the
     *                                          resulting collection.
     * @throws RuntimeException
     * @return static<T>
     */
    public function toUniqueByCallback(Closure $callback, bool $isUsingFirstEncounteredElement = true): self;

    /**
     * Add an element to the end of a clone of the current collection.
     * Must return a clone.
     *
     * @param T $element
     * @throws RuntimeException
     * @return static<T>
     */
    public function withAdded($element): self;

    /**
     * Add multiple elements to the end of a clone of the current collection. Array keys are not preserved.
     * Must return a clone.
     *
     * @param array<int|string, T> $elements
     * @throws RuntimeException
     * @return static<T>
     */
    public function withAddedMultiple(array $elements): self;

    /**
     * Filter the elements of a clone of the current collection, using the `array_filter` function and based on logic in
     * the the $callback closure.
     * Must return a clone.
     *
     * @throws RuntimeException
     * @return static<T>
     */
    public function withFiltered(Closure $callback): self;

    /**
     * Merge (using `array_merge` logic) a clone of the collection with the elements contained in the $collection
     * argument.
     * Must return a clone.
     *
     * @param CollectionInterface<T> $collection
     * @throws RuntimeException
     * @return static<T>
     */
    public function withMerged(CollectionInterface $collection): self;

    /**
     * Remove the $key from a clone of the current collection, if the array key exists.
     * Must return a clone.
     * Must throw a RuntimeException when argument $key is invalid.
     *
     * @param int|string $key
     * @throws InvalidArgumentException
     * @return static<T>
     */
    public function withRemoved($key): self;

    /**
     * Remove the $element from a clone of the current collection, if the element exists in the collection.
     * Must return a clone.
     * Must throw a RuntimeException when the $element argument is invalid for the implementing collection class.
     *
     * @param T $element
     * @throws RuntimeException
     * @return static<T>
     */
    public function withRemovedElement($element): self;

    /**
     * Set the $element argument on a clone of the current collection, using the $key argument.
     * Must return a clone.
     * Must throw a RuntimeException when the $element argument is invalid for the implementing collection class.
     *
     * @param int|string $key
     * @param T $element
     * @throws RuntimeException
     * @return static<T>
     */
    public function withSet($key, $element): self;

    /**
     * Slice a clone of the current collection using the `array_slice` function.
     *
     * @see https://www.php.net/manual/en/function.array-slice.php
     *
     * Must return a clone.
     *
     * @return static<T>
     */
    public function withSliced(int $offset, ?int $length = null): self;

    /**
     * Returns `true` when no elements exist in the current collection. Otherwise, returns `false`.
     *
     * Corresponds to the core PHP function `empty`.
     *
     * @see https://www.php.net/manual/en/function.empty.php
     */
    public function isEmpty(): bool;
}
