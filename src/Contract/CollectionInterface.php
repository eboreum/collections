<?php

declare(strict_types=1);

namespace Eboreum\Collections\Contract;

use Closure;
use Countable;
use Eboreum\Collections\Contract\CollectionInterface\ToReindexedDuplicateKeyBehaviorEnum;
use Eboreum\Collections\Exception\ElementNotFoundException;
use Eboreum\Collections\Exception\InvalidClosureReturnValueException;
use Eboreum\Collections\Exception\KeyNotFoundException;
use Eboreum\Collections\Exception\RuntimeException;
use Eboreum\Collections\Exception\UnacceptableCollectionException;
use Eboreum\Collections\Exception\UnacceptableElementException;
use IteratorAggregate;

/**
 * {@inheritDoc}
 *
 * The implementing class must be immutable, but changing the array pointer is allowed.
 *
 * Do NOT extend this interface with \ArrayAccess. This library exists to combat arbitrary arrays and this interface
 * is immutable, which \ArrayAccess would break.
 *
 * @template T
 * @extends IteratorAggregate<int|string, T>
 */
interface CollectionInterface extends ImmutableObjectInterface, Countable, IteratorAggregate
{
    /**
     * Do nothing if the provided $element argument is accepted in the implementing collection class. Otherwise,
     * must throw an UnacceptableElementException.
     *
     * @throws UnacceptableElementException
     */
    public static function assertIsElementAccepted(mixed $element): void;

    /**
     * Returns a human readable array with strings describing the sequence of invalid elements.
     *
     * @param array<int|string, mixed> $elements
     *
     * @return array<string>
     */
    public static function makeInvalids(array $elements): array;

    /**
     * Returns `true` when the $element argument is accepted in the implementing collection class. Otherwise, returns
     * `false`.
     */
    public static function isElementAccepted(mixed $element): bool;

    /**
     * @param array<int|string, T> $elements Must throw a UnacceptableElementException when one or more elements are not
     *                                       accepted by the implementing collection class.
     *
     * @throws UnacceptableElementException
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
     *
     * @return CollectionInterface<static<T>>
     *
     * @throws RuntimeException
     */
    public function chunk(int $chunkSize): CollectionInterface;

    /**
     * Returns `true` when the collection contains the $element argument. Otherwise, returns `false`.
     * Must throw an UnacceptableElementException when the $element argument is invalid for the implementing collection
     * class.
     *
     * @param T $element
     *
     * @throws UnacceptableElementException
     */
    public function contains(mixed $element): bool;

    /**
     * Returns the current element for the array pointer in the collection's elements. If empty, returns `null`.
     *
     * Corresponds to the core PHP function `current`.
     *
     * @see https://www.php.net/manual/en/function.current.php
     *
     * @return T|null
     */
    public function current(): mixed;

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
     * @param Closure(mixed, int|string, object|null):void $callback
     * @param object|null $carry Corresponds to the $carry argument in the $callback.
     *
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
     * Any other return value returned by $callback must cause an InvalidClosureReturnValueException to be thrown.
     *
     * @param Closure(mixed, int|string, object|null):(bool|void|null) $callback
     * @param object|null $carry Corresponds to the $carry argument in the $callback.
     *
     * @throws InvalidClosureReturnValueException|RuntimeException
     */
    public function every(Closure $callback, ?object $carry = null): void;

    /**
     * Will attempt to find the first element by value in the collection, using the specified callback. When nothing is
     * found, `null` is returned.
     *
     * @param Closure(T, int|string):bool $callback This closure will be called with arguments `mixed $v` and
     *                                              `int|string $k`, where $v is an element contained in the current
     *                                              collection and $k is the element's respective key.
     *
     * @return T|null
     *
     * @throws ElementNotFoundException
     */
    public function find(Closure $callback): mixed;

    /**
     * Same as the "find" method above. However, when an element is NOT found, an exception is thrown instead of null
     * being returned.
     *
     * Inspired by: https://laravel.com/docs/12.x/collections#method-first-or-fail
     *
     * @return T
     *
     * @throws ElementNotFoundException
     */
    public function findOrFail(Closure $callback): mixed;

    /**
     * Returns the first element in the collection's elements. If empty, returns `null`. Moves the array pointer.
     *
     * Corresponds to the core PHP function `reset`.
     *
     * @see https://www.php.net/manual/en/function.reset.php
     *
     * @return T|null
     */
    public function first(): mixed;

    /**
     * Returns the first key – int or string – in the collection. If empty, returns `null`.
     *
     * Corresponds to the core PHP function `array_key_first`.
     *
     * @see https://www.php.net/manual/en/function.array-key-first.php
     */
    public function firstKey(): int|string|null;

    /**
     * Same as the "firstKey" method above. However, when collection is empty, an exception is thrown instead of null
     * being returned.
     *
     * Inspired by: https://laravel.com/docs/12.x/collections#method-first-or-fail
     *
     * @return T
     *
     * @throws KeyNotFoundException
     */
    public function firstKeyOrFail(): mixed;

    /**
     * Same as the "first" method above. However, when collection is empty, an exception is thrown instead of null being
     * returned.
     *
     * Inspired by: https://laravel.com/docs/12.x/collections#method-first-or-fail
     *
     * @return T
     *
     * @throws ElementNotFoundException
     */
    public function firstOrFail(): mixed;

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
     *
     * @return T|null
     */
    public function get(int|string $key): mixed;

    /**
     * Same as the "get" method above. However, when the targeted element does not exist in the collection, an exception
     * is thrown instead of null being returned.
     *
     * Inspired by: https://laravel.com/docs/12.x/collections#method-first-or-fail
     *
     * @return T
     *
     * @throws ElementNotFoundException
     */
    public function getOrFail(int|string $key): mixed;

    /**
     * Implementing method must guard two things:
     *
     *   1. That the $collection argument has the same class or is a subclass of $this.
     *   1. That $this accepts all the elements in the $collection argument.
     *
     * @param self<T> $collection
     *
     * @throws UnacceptableCollectionException|UnacceptableElementException
     */
    public function guardCollectionInheritanceAndAcceptedElements(self $collection): void;

    /**
     * Returns `true`, if the argument $key exists as an array key in the collection's elements. Otherwise, returns
     * `false`.
     */
    public function has(int|string $key): bool;

    /**
     * Returns `true`, if the argument $key exists as an array key in the collection's elements. Otherwise, returns
     * `false`.
     *
     * Corresponds to the core PHP function `array_search`.
     *
     * @see https://www.php.net/manual/en/function.array-search.php
     *
     * @param T $element
     *
     * @throws UnacceptableElementException
     */
    public function indexOf(mixed $element): int|string|null;

    /**
     * Returns the key for the current element (array pointer) in the collection's elements. If key does not exist,
     * returns `null`.
     *
     * Corresponds to the core PHP function `key`.
     *
     * @see https://www.php.net/manual/en/function.key.php
     */
    public function key(): int|string|null;

    /**
     * Returns the last element in the collection's elements. If empty, returns `null`. Moves the array pointer.
     *
     * Corresponds to the core PHP function `end`.
     *
     * @see https://www.php.net/manual/en/function.end.php
     *
     * @return T|null
     */
    public function last(): mixed;

    /**
     * Returns the first key – int or string – in the collection. If empty, returns `null`.
     *
     * Corresponds to the core PHP function `array_key_last`.
     *
     * @see https://www.php.net/manual/en/function.array-key-last.php
     */
    public function lastKey(): int|string|null;

    /**
     * Same as the "lastKey" method above. However, when collection is empty, an exception is thrown instead of null
     * being returned.
     *
     * Inspired by: https://laravel.com/docs/12.x/collections#method-first-or-fail
     *
     * @return T
     *
     * @throws KeyNotFoundException
     */
    public function lastKeyOrFail(): mixed;

    /**
     * Same as the "last" method above. However, when collection is empty, an exception is thrown instead of null being
     * returned.
     *
     * Inspired by: https://laravel.com/docs/12.x/collections#method-first-or-fail
     *
     * @return T
     *
     * @throws ElementNotFoundException
     */
    public function lastOrFail(): mixed;

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
     *   - T $value: The current element's value.
     *   - int|string $key: The current element's key.
     *
     * @param Closure(T, int|string):mixed $callback
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
     * Argument $callback must return an integer. Any other return value returned by $callback must cause an
     * InvalidClosureReturnValueException to be thrown.
     *
     * @param Closure(T, int|string):int $callback
     *
     * @return T|null
     *
     * @throws InvalidClosureReturnValueException|RuntimeException
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
     * Argument $callback must return an integer. Any other return value returned by $callback must cause an
     * InvalidClosureReturnValueException to be thrown.
     *
     * @param Closure(T, int|string):int $callback
     *
     * @return T|null
     *
     * @throws InvalidClosureReturnValueException|RuntimeException
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
    public function next(): mixed;

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
     */
    public function toCleared(): static;

    /**
     * Equivalent of `array_diff`.
     *
     * Must return a clone. The resulting clone must contain only the VALUES in this current collections, which are
     * different from the VALUES in the $other collection.
     *
     * @see https://www.php.net/manual/en/function.array-diff.php
     *
     * @param CollectionInterface<T> $collection
     * @param bool $isBidirectional When false, only elements in the current collection ($this) will be included. When
     *                              true, all the different elements from both collections ($this and $collection) will
     *                              be included.
     *
     * @throws UnacceptableCollectionException|UnacceptableElementException
     */
    public function toDifference(CollectionInterface $collection, bool $isBidirectional = false): static;

    /**
     * Equivalent of `array_diff_key`.
     *
     * Must return a clone. The resulting clone must contain only the KEYS in this current collections, which are
     * different from the KEYS in the $other collection.
     *
     * @see https://www.php.net/manual/en/function.array-diff-key.php
     *
     * @param CollectionInterface<T> $collection
     * @param bool $isBidirectional When false, only key in the current collection ($this) will be included. When true,
     *                              all the different keys from both collections ($this and $collection) will be
     *                              included.
     *
     * @throws UnacceptableCollectionException|UnacceptableElementException
     */
    public function toDifferenceByKey(CollectionInterface $collection, bool $isBidirectional = false): static;

    /**
     * Equivalent of `array_intersect`.
     *
     * Must return a clone. The resulting clone must contain only the intersection of VALUES between the two
     * collections.
     *
     * @see https://www.php.net/manual/en/function.array-intersect.php
     *
     * @param CollectionInterface<T> $collection
     *
     * @throws UnacceptableCollectionException|UnacceptableElementException
     */
    public function toIntersection(CollectionInterface $collection): static;

    /**
     * Equivalent of `array_intersect_key`.
     *
     * Must return a clone. The resulting clone must contain only the intersection of KEYS between the two collections.
     *
     * @see https://www.php.net/manual/en/function.array-intersect-key.php
     *
     * @param CollectionInterface<T> $collection
     *
     * @throws UnacceptableCollectionException|UnacceptableElementException
     */
    public function toIntersectionByKey(CollectionInterface $collection): static;

    /**
     * Equivalent of `array_values`. Makes the contained elements in a clone of the current instance exist in a
     * sequential array, with all keys being numerical, starting from index 0.
     *
     * Must return a clone.
     *
     * @see https://www.php.net/manual/en/function.array-values.php
     */
    public function toSequential(): static;

    /**
     * Reindex the elements in a clone of the current collection, using the value returned by the $closure argument.
     *
     * Must throw a InvalidClosureReturnValueException when the $closure argument does not return an int or string.
     *
     * Must throw a RuntimeException when argument $duplicateKeyBehavior is
     * ToReindexedDuplicateKeyBehaviorEnum::throw_exception and one or more duplicate keys are found.
     *
     * Must return said clone.
     *
     * @param Closure(T,int|string):(int|string) $closure The returned value will be used as the key in the resulting
     *                                                    collection.
     *
     * @throws RuntimeException
     */
    public function toReindexed(
        Closure $closure,
        ToReindexedDuplicateKeyBehaviorEnum $duplicateKeyBehavior = ToReindexedDuplicateKeyBehaviorEnum::throw_exception
    ): static;

    /**
     * Reverses the order of elements in the clone of a the current collection using `array_reverse`.
     *
     * Must return a clone.
     *
     * @param bool $isPreservingKeys Notice, this is different from the default value of $preserve_keys in
     *                               the `array_reverse` function.
     *                               When `true`, array keys are preserved. Otherwise, they are not (i.e. becoming
     *                               sequential).
     */
    public function toReversed(bool $isPreservingKeys = true): static;

    /**
     * Sorts using the `uasort` function.
     *
     * Argument $callback will have the following parameters:
     *
     *   - T $a: An element A to test, which is present within the collection.
     *   - T $b: An element B to test, which is present within the collection.
     *
     * Argument $callback must return an integer. Any other value returned by $callback must cause an exception to be
     * thrown.
     *
     * Must return a clone.
     *
     * @param Closure(T, T):int $callback
     *
     * @throws RuntimeException
     */
    public function toSortedByCallback(Closure $callback): static;

    /**
     * Produces a clone containing only elements which are considered to be unique, where the uniqueness is determined
     * by the string returned by the $callback argument. Preserves array keys.
     *
     * Argument $callback will have the following parameters:
     *
     *   - T $value: An element within the current collection.
     *   - int|string $key: An array key.
     *
     * Argument $callback must return a string. Any other value than string returned by $callback must cause an
     * InvalidClosureReturnValueException to be thrown.
     *
     * Must return a clone.
     *
     * @param Closure(T, int|string):string $callback
     * @param bool $isUsingFirstEncounteredElement
     *                                          When `true` and when two or more elements, which have produced the same
     *                                          unique string, exist, only the first element will will exist in the
     *                                          resulting collection. Otherwise, only the last element will exist in the
     *                                          resulting collection.
     *
     * @throws InvalidClosureReturnValueException|RuntimeException
     */
    public function toUniqueByCallback(Closure $callback, bool $isUsingFirstEncounteredElement = true): static;

    /**
     * Add an element to the end of a clone of the current collection.
     * Must return a clone.
     *
     * @param T $element
     *
     * @throws UnacceptableElementException|RuntimeException
     */
    public function withAdded(mixed $element): static;

    /**
     * Add multiple elements to the end of a clone of the current collection. Array keys are not preserved.
     * Must return a clone.
     *
     * @param array<int|string, T> $elements
     *
     * @throws UnacceptableElementException|RuntimeException
     */
    public function withAddedMultiple(array $elements): static;

    /**
     * Filter the elements of a clone of the current collection, using the `array_filter` function and based on logic in
     * the the $callback closure.
     * Must return a clone.
     *
     * @param Closure(T, int|string):bool $callback
     *
     * @throws RuntimeException
     */
    public function withFiltered(Closure $callback): static;

    /**
     * Merge (using `array_merge` logic) a clone of the collection with the elements contained in the $collection
     * argument.
     * Must return a clone.
     *
     * @param CollectionInterface<T> $collection
     *
     * @throws UnacceptableCollectionException|UnacceptableElementException|RuntimeException
     */
    public function withMerged(CollectionInterface $collection): static;

    /**
     * Remove the $key from a clone of the current collection, if the array key exists.
     * Must return a clone.
     */
    public function withRemoved(int|string $key): static;

    /**
     * Remove the $element from a clone of the current collection, if the element exists in the collection.
     * Must return a clone.
     * Must throw a UnacceptableElementException when the $element argument is invalid for the implementing collection
     * class.
     *
     * @param T $element
     *
     * @throws UnacceptableElementException|RuntimeException
     */
    public function withRemovedElement(mixed $element): static;

    /**
     * Set the $element argument on a clone of the current collection, using the $key argument.
     * Must return a clone.
     * Must throw a RuntimeException when the $element argument is invalid for the implementing collection class.
     *
     * @param T $element
     *
     * @throws UnacceptableElementException|RuntimeException
     */
    public function withSet(int|string $key, $element): static;

    /**
     * Slice a clone of the current collection using the `array_slice` function.
     *
     * @see https://www.php.net/manual/en/function.array-slice.php
     *
     * Must return a clone.
     */
    public function withSliced(int $offset, ?int $length = null): static;

    /**
     * Returns `true` when no elements exist in the current collection. Otherwise, returns `false`.
     *
     * Corresponds to the core PHP function `empty`.
     *
     * @see https://www.php.net/manual/en/function.empty.php
     */
    public function isEmpty(): bool;
}
