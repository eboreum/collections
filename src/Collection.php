<?php

/**
 * @codingStandardsIgnoreStart
 *
 * This file is largely based on: @see https://github.com/doctrine/collections/blob/94918256daa6ac99c7e5774720c0e76f01936bda/lib/Doctrine/Common/Collections/ArrayCollection.php
 *
 * From the LICENSE file in doctrine/collections (@see https://github.com/doctrine/collections/blob/94918256daa6ac99c7e5774720c0e76f01936bda/LICENSE):
 *
 * Copyright (c) 2006-2013 Doctrine Project
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @codingStandardsIgnoreEnd
 */

declare(strict_types=1);

namespace Eboreum\Collections;

use ArrayIterator;
use Closure;
use Eboreum\Caster\Attribute\DebugIdentifier;
use Eboreum\Caster\Contract\DebugIdentifierAttributeInterface;
use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\ToReindexedDuplicateKeyBehaviorEnum;
use Eboreum\Collections\Exception\ElementNotFoundException;
use Eboreum\Collections\Exception\KeyNotFoundException;
use Eboreum\Collections\Exception\RuntimeException;
use Eboreum\Collections\Exception\UnacceptableCollectionException;
use Eboreum\Collections\Exception\UnacceptableElementException;
use Eboreum\Exceptional\ExceptionMessageGenerator;
use ReflectionClass;
use ReflectionMethod;
use ReflectionObject;
use Throwable;

use function array_chunk;
use function array_filter;
use function array_key_exists;
use function array_key_first;
use function array_key_last;
use function array_keys;
use function array_map;
use function array_merge;
use function array_reverse;
use function array_search;
use function array_slice;
use function array_sum;
use function array_values;
use function count;
use function end;
use function func_get_args;
use function implode;
use function is_a;
use function is_bool;
use function is_int;
use function is_scalar;
use function is_string;
use function key;
use function next;
use function reset;
use function sprintf;
use function uasort;

use const ARRAY_FILTER_USE_BOTH;

/**
 * {@inheritDoc}
 *
 * @template T
 * @implements CollectionInterface<T>
 */
class Collection implements CollectionInterface, DebugIdentifierAttributeInterface
{
    public static function assertIsElementAccepted(mixed $element): void
    {
        if (false === static::isElementAccepted($element)) {
            throw new UnacceptableElementException(
                sprintf(
                    'Argument $element = %s is not accepted by %s',
                    Caster::getInstance()->castTyped($element),
                    Caster::makeNormalizedClassName(new ReflectionClass(static::class)),
                ),
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function makeInvalids(array $elements): array
    {
        $invalids = [];

        foreach ($elements as $k => $v) {
            if (false === static::isElementAccepted($v)) {
                $invalids[] = sprintf(
                    '%s => %s',
                    Caster::getInstance()->cast($k),
                    Caster::getInstance()->castTyped($v),
                );
            }
        }

        return $invalids;
    }

    public static function isElementAccepted(mixed $element): bool
    {
        return true;
    }

    /**
     * @var array<int|string, T>
     */
    #[DebugIdentifier]
    protected array $elements;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $elements = [])
    {
        try {
            $invalids = static::makeInvalids($elements);

            if ($invalids) {
                throw new RuntimeException(sprintf(
                    'In argument $elements, %d/%d elements are invalid, including: [%s]',
                    count($invalids),
                    count($elements),
                    implode(', ', $invalids),
                ));
            }

            $this->elements = $elements;
        } catch (Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new ReflectionMethod(self::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }
    }

    public function chunk(int $chunkSize): CollectionInterface
    {
        try {
            if (false === ($chunkSize >= 1)) { // @phpstan-ignore-line
                throw new RuntimeException(
                    sprintf(
                        'Argument $chunkSize = %s must be >= 1, but it is not',
                        Caster::getInstance()->castTyped($chunkSize),
                    ),
                );
            }

            $elements = [];

            if ($this->elements) {
                $elements = array_map(
                    /**
                     * @return static
                     */
                    function (array $elements) {
                        $clone = clone $this;
                        $clone->elements = $elements;

                        return $clone;
                    },
                    array_chunk($this->elements, $chunkSize, true),
                );
            }

            /** @var Collection<static<T>> $collection */
            $collection = new Collection($elements);
        } catch (Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new ReflectionMethod(self::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function contains($element): bool
    {
        static::assertIsElementAccepted($element);

        return (false !== array_search($element, $this->elements, true));
    }

    public function count(): int
    {
        return count($this->elements);
    }

    public function current(): mixed
    {
        $key = $this->key();

        if (null !== $key && array_key_exists($key, $this->elements)) {
            return $this->elements[$key];
        }

        return null;
    }

    public function each(Closure $callback, ?object $carry = null): void
    {
        try {
            foreach ($this->elements as $key => $value) {
                try {
                    $callback($value, $key, $carry);
                } catch (Throwable $t) {
                    throw new RuntimeException(sprintf(
                        'Failure when calling $callback(%s, %s, %s)',
                        Caster::getInstance()->castTyped($value),
                        Caster::getInstance()->castTyped($key),
                        Caster::getInstance()->castTyped($carry)
                    ), 0, $t);
                }
            }
        } catch (Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new ReflectionMethod(self::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }
    }

    public function every(Closure $callback, ?object $carry = null): void
    {
        try {
            foreach ($this->elements as $key => $value) {
                try {
                    $result = $callback($value, $key, $carry);
                } catch (Throwable $t) {
                    throw new RuntimeException(sprintf(
                        'Failure when calling $callback(%s, %s, %s)',
                        Caster::getInstance()->castTyped($value),
                        Caster::getInstance()->castTyped($key),
                        Caster::getInstance()->castTyped($carry)
                    ), 0, $t);
                }

                if (false === $result) {
                    break;
                }

                if (null === $result || true === $result) {
                    continue;
                }

                throw new RuntimeException(sprintf(
                    implode('', [
                        'Call $callback(%s, %s, %s) must return void, `null`, `false`, or `true`, but it did not.',
                        ' Found return value: %s',
                    ]),
                    Caster::getInstance()->castTyped($value),
                    Caster::getInstance()->castTyped($key),
                    Caster::getInstance()->castTyped($carry),
                    Caster::getInstance()->castTyped($result),
                ));
            }
        } catch (Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new ReflectionMethod(self::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }
    }

    public function find(Closure $callback): mixed
    {
        if (!$this->elements) {
            return null;
        }

        try {
            $return = $this->findOrFail($callback);
        } catch (ElementNotFoundException) {
            return null;
        }

        return $return;
    }

    public function findOrFail(Closure $callback): mixed
    {
        /** @var T|null $return */
        $return = null;

        /**
         * We need this variable, in addition to the $return variable, because someone may indeed be searching for null
         * in a collection.
         *
         * @var bool $found
         */
        $found = false;

        try {
            foreach ($this->elements as $k => $v) {
                $callbackResult = $callback($v, $k);

                if (false === is_bool($callbackResult)) {
                    throw new RuntimeException(
                        sprintf(
                            'Call $callback(%s, %s) did not return a boolean, which it must. Found return value: %s',
                            Caster::getInstance()->castTyped($v),
                            Caster::getInstance()->castTyped($k),
                            Caster::getInstance()->castTyped($callbackResult),
                        ),
                    );
                }

                if (true === $callbackResult) {
                    $return = $v;
                    $found = true;

                    break;
                }
            }
        } catch (Throwable $t) {
            throw new RuntimeException(
                ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                    $this,
                    new ReflectionMethod(self::class, __FUNCTION__),
                    func_get_args(),
                ),
                0,
                $t,
            );
        }

        if (false === $found) {
            throw new ElementNotFoundException(
                sprintf(
                    'In collection %s, an element could not be found from argument $callback = %s',
                    Caster::getInstance()->castTyped($this),
                    Caster::getInstance()->castTyped($callback),
                ),
            );
        }

        /**
         * phpstan needs the "var" definition to be here.
         * phpcs is angry, unless there is a variable reference (assignment) below.
         * Assigning the same variable into itself will get optimized away.
         *
         * @var T $return
         */
        $return = $return;

        return $return;
    }

    public function first(): mixed
    {
        reset($this->elements);

        $key = array_key_first($this->elements);

        if (null === $key) {
            return null;
        }

        return $this->elements[$key];
    }

    public function firstKey(): int|string|null
    {
        if (!$this->elements) {
            return null;
        }

        return array_key_first($this->elements);
    }

    public function firstKeyOrFail(): int|string
    {
        $key = $this->firstKey();

        if (null === $key) {
            throw new KeyNotFoundException(
                sprintf(
                    'Collection %s is empty and therefore it does not have a "first" key',
                    Caster::getInstance()->castTyped($this)
                ),
            );
        }

        return $key;
    }

    public function firstOrFail(): mixed
    {
        $first = $this->first();

        if (null === array_key_first($this->elements)) {
            throw new ElementNotFoundException(
                sprintf(
                    'Collection %s is empty and therefore it does not have a "first" element',
                    Caster::getInstance()->castTyped($this)
                ),
            );
        }

        /**
         * phpstan needs the "var" definition to be here.
         * phpcs is angry, unless there is a variable reference (assignment) below.
         * Assigning the same variable into itself will get optimized away.
         *
         * @var T $first
         */
        $first = $first;

        return $first;
    }

    public function get(int|string $key): mixed
    {
        if (array_key_exists($key, $this->elements)) {
            return $this->elements[$key];
        }

        return null;
    }

    public function getOrFail(int|string $key): mixed
    {
        if (false === array_key_exists($key, $this->elements)) {
            throw new ElementNotFoundException(
                sprintf(
                    'In collection %s, an element with $key = %s does not exist',
                    Caster::getInstance()->castTyped($this),
                    Caster::getInstance()->castTyped($key),
                ),
            );
        }

        /** @var T $return */
        $return = $this->elements[$key];

        return $return;
    }

    /**
     * {@inheritDoc}
     */
    public function getKeys(): array
    {
        return array_keys($this->elements);
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayIterator<int|string, T>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->elements);
    }

    public function has(int|string $key): bool
    {
        return array_key_exists($key, $this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function indexOf($element): int|string|null
    {
        static::assertIsElementAccepted($element);

        $key = array_search($element, $this->elements, true);

        if (false === $key) {
            return null;
        }

        return $key;
    }

    public function key(): int|string|null
    {
        return key($this->elements);
    }

    public function last(): mixed
    {
        end($this->elements);

        $key = array_key_last($this->elements);

        if (null === $key) {
            return null;
        }

        return $this->elements[$key];
    }

    public function lastKey(): int|string|null
    {
        if (!$this->elements) {
            return null;
        }

        return array_key_last($this->elements);
    }

    public function lastKeyOrFail(): int|string
    {
        $key = $this->lastKey();

        if (null === $key) {
            throw new KeyNotFoundException(
                sprintf(
                    'Collection %s is empty and therefore it does not have a "last" key',
                    Caster::getInstance()->castTyped($this)
                ),
            );
        }

        return $key;
    }

    public function lastOrFail(): mixed
    {
        /** @var T|null $last */
        $last = $this->last();

        if (null === array_key_last($this->elements)) {
            throw new ElementNotFoundException(
                sprintf(
                    'Collection %s is empty and therefore it does not have a "last" element',
                    Caster::getInstance()->castTyped($this)
                ),
            );
        }

        /**
         * phpstan needs the "var" definition to be here.
         * phpcs is angry, unless there is a variable reference (assignment) below.
         * Assigning the same variable into itself will get optimized away.
         *
         * @var T $last
         */
        $last = $last;

        return $last;
    }

    /**
     * {@inheritDoc}
     */
    public function map(Closure $callback): array
    {
        $array = [];

        foreach ($this->elements as $k => $v) {
            $array[$k] = $callback($v, $k);
        }

        return $array;
    }

    /**
     * {@inheritDoc}
     */
    public function maxByCallback(Closure $callback)
    {
        $max = null;
        $element = null;

        try {
            foreach ($this->elements as $key => $value) {
                try {
                    $result = $callback($value, $key);
                } catch (Throwable $t) {
                    throw new RuntimeException(sprintf(
                        'Failure when calling $callback(%s, %s)',
                        Caster::getInstance()->castTyped($value),
                        Caster::getInstance()->castTyped($key),
                    ), 0, $t);
                }

                if (false === is_int($result)) { // @phpstan-ignore-line We want to capture this anyway
                    throw new RuntimeException(sprintf(
                        implode('', [
                            'Call $callback(%s, %s) must return int, but it did not.',
                            ' Found return value: %s',
                        ]),
                        Caster::getInstance()->castTyped($value),
                        Caster::getInstance()->castTyped($key),
                        Caster::getInstance()->castTyped($result),
                    ));
                }

                if (null === $max) {
                    $max = $result;
                    $element = $value;
                } elseif ($result >= $max) {
                    $element = $value;
                    $max = $result;
                }
            }
        } catch (Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new ReflectionMethod(self::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $element;
    }

    /**
     * {@inheritDoc}
     */
    public function minByCallback(Closure $callback)
    {
        $min = null;
        $element = null;

        try {
            foreach ($this->elements as $key => $value) {
                try {
                    $result = $callback($value, $key);
                } catch (Throwable $t) {
                    throw new RuntimeException(sprintf(
                        'Failure when calling $callback(%s, %s)',
                        Caster::getInstance()->castTyped($value),
                        Caster::getInstance()->castTyped($key),
                    ), 0, $t);
                }

                if (false === is_int($result)) { // @phpstan-ignore-line We want to capture this anyway
                    throw new RuntimeException(sprintf(
                        implode('', [
                            'Call $callback(%s, %s) must return int, but it did not.',
                            ' Found return value: %s',
                        ]),
                        Caster::getInstance()->castTyped($value),
                        Caster::getInstance()->castTyped($key),
                        Caster::getInstance()->castTyped($result),
                    ));
                }

                if (null === $min) {
                    $min = $result;
                    $element = $value;
                } elseif ($result < $min) {
                    $element = $value;
                    $min = $result;
                }
            }
        } catch (Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new ReflectionMethod(self::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $element;
    }

    public function next(): mixed
    {
        next($this->elements);
        $key = key($this->elements);

        if (is_scalar($key) && array_key_exists($key, $this->elements)) {
            return $this->elements[$key];
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * {@inheritDoc}
     */
    public function toArrayValues(): array
    {
        return array_values($this->elements);
    }

    public function toCleared(): static
    {
        $clone = clone $this;
        $clone->elements = [];

        return $clone;
    }

    public function toReindexed(
        Closure $closure,
        ToReindexedDuplicateKeyBehaviorEnum $duplicateKeyBehavior = ToReindexedDuplicateKeyBehaviorEnum::throw_exception
    ): static {
        try {
            $clone = clone $this;

            /** @var array<T> $resultingElements */
            $resultingElements = [];

            /** @var array<string> $invalidTypeErrorMessages */
            $invalidTypeErrorMessages = [];

            /** @var array<int|string, array<int|string>> $resultingKeyToOriginalKeys */
            $resultingKeyToOriginalKeys = [];

            foreach ($clone->elements as $key => $element) {
                $resultingKey = $closure($element, $key);

                if (false === array_key_exists($resultingKey, $resultingKeyToOriginalKeys)) {
                    $resultingKeyToOriginalKeys[$resultingKey] = [];
                }

                $resultingKeyToOriginalKeys[$resultingKey][] = $key;

                if (false === is_int($resultingKey) && false === is_string($resultingKey)) { // @phpstan-ignore-line
                    $invalidTypeErrorMessages[] = sprintf(
                        '%s => %s: Resulting key is: %s',
                        Caster::getInstance()->cast($key),
                        Caster::getInstance()->castTyped($element),
                        Caster::getInstance()->castTyped($resultingKey),
                    );

                    continue;
                }

                if (array_key_exists($resultingKey, $resultingElements)) {
                    if ($duplicateKeyBehavior === ToReindexedDuplicateKeyBehaviorEnum::throw_exception) {
                        continue;
                    }

                    if ($duplicateKeyBehavior === ToReindexedDuplicateKeyBehaviorEnum::use_last_element) {
                        unset($resultingElements[$resultingKey]);
                        $resultingElements[$resultingKey] = $element;
                    }
                } else {
                    $resultingElements[$resultingKey] = $element;
                }
            }

            $messages = [];

            if ($invalidTypeErrorMessages) {
                $elementsCount = count($clone->elements);

                $messages[] = sprintf(
                    'For %d/%d %s, the $closure argument did not produce an int or string. Errors given: [%s]',
                    count($invalidTypeErrorMessages),
                    $elementsCount,
                    (
                        1 === $elementsCount
                        ? 'element'
                        : 'elements'
                    ),
                    implode(', ', $invalidTypeErrorMessages),
                );
            }

            $duplicateKeysGroups = array_filter(
                $resultingKeyToOriginalKeys,
                static function (array $group): bool {
                    return count($group) > 1;
                },
            );

            if (
                $duplicateKeysGroups
                && $duplicateKeyBehavior === ToReindexedDuplicateKeyBehaviorEnum::throw_exception
            ) {
                /** @var array<string> $groupMessages */
                $groupMessages = [];

                foreach ($duplicateKeysGroups as $resultingKey => $originalKeys) {
                    $groupMessages[] = sprintf(
                        'Resulting key %s was produced from the %d indexes: [%s]',
                        Caster::getInstance()->cast($resultingKey),
                        count($originalKeys),
                        implode(
                            ', ',
                            array_map(
                                static function (int|string $resultingKey): string {
                                    return Caster::getInstance()->cast($resultingKey);
                                },
                                $originalKeys,
                            ),
                        ),
                    );
                }

                $count = array_sum(array_map('count', $duplicateKeysGroups));
                $totalCount = count($clone->elements);

                $messages[] = sprintf(
                    'For %d/%d elements, the $closure argument produced a duplicate key, which is not allowed: %s',
                    $count,
                    $totalCount,
                    implode('. ', $groupMessages),
                );
            }

            if ($messages) {
                throw new RuntimeException(implode('. ', $messages));
            }

            $clone->elements = $resultingElements;
        } catch (Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new ReflectionMethod(self::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    public function toReversed(bool $isPreservingKeys = true): static
    {
        $clone = clone $this;
        $clone->elements = array_reverse($clone->elements, $isPreservingKeys);

        return $clone;
    }

    public function toSequential(): static
    {
        $clone = clone $this;
        $clone->elements = array_values($clone->elements);

        return $clone;
    }

    public function toSortedByCallback(Closure $callback): static
    {
        try {
            $clone = clone $this;
            uasort($clone->elements, $callback);
        } catch (Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new ReflectionMethod(self::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    public function toUniqueByCallback(Closure $callback, bool $isUsingFirstEncounteredElement = true): static
    {
        try {
            $clone = clone $this;

            /**
             * @var array<string, array{key: int|string, value: mixed}> $uniqueStringToKeyAndElement
             */
            $uniqueStringToKeyAndElement = [];

            foreach ($clone as $key => $value) {
                try {
                    $result = $callback($value, $key);
                } catch (Throwable $t) {
                    throw new RuntimeException(sprintf(
                        'Failure when calling $callback(%s, %s)',
                        Caster::getInstance()->castTyped($value),
                        Caster::getInstance()->castTyped($key),
                    ), 0, $t);
                }

                if (false === is_string($result)) { // @phpstan-ignore-line We want to capture this anyway
                    throw new RuntimeException(sprintf(
                        'Call $callback(%s, %s) must return a string, but it did not. Resulting return value: %s',
                        Caster::getInstance()->castTyped($value),
                        Caster::getInstance()->castTyped($key),
                        Caster::getInstance()->castTyped($result),
                    ));
                }

                if (false === $isUsingFirstEncounteredElement) {
                    unset($uniqueStringToKeyAndElement[$result]);
                }

                if (
                    false === $isUsingFirstEncounteredElement
                    || false === array_key_exists($result, $uniqueStringToKeyAndElement)
                ) {
                    $uniqueStringToKeyAndElement[$result] = [
                        0 => $key,
                        1 => $value,
                    ];
                }
            }

            $clone->elements = [];

            foreach ($uniqueStringToKeyAndElement as [$key, $value]) {
                $clone->elements[$key] = $value;
            }
        } catch (Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new ReflectionMethod(self::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function withAdded($element): static
    {
        try {
            static::assertIsElementAccepted($element);

            $clone = clone $this;
            $clone->elements[] = $element;
        } catch (UnacceptableElementException $e) {
            throw new UnacceptableElementException(
                sprintf(
                    'Argument $element = %s cannot be added to the current collection, %s',
                    Caster::getInstance()->castTyped($element),
                    Caster::getInstance()->castTyped($this),
                ),
                0,
                $e,
            );
        } catch (Throwable $t) {
            throw new RuntimeException(
                ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                    $this,
                    new ReflectionMethod(self::class, __FUNCTION__),
                    func_get_args(),
                ),
                0,
                $t,
            );
        }

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function withAddedMultiple(array $elements): static
    {
        try {
            $invalids = static::makeInvalids($elements);

            if ($invalids) {
                throw new UnacceptableElementException(
                    sprintf(
                        'In argument $elements = %s, %d/%d elements are invalid, including: [%s]',
                        Caster::getInstance()->castTyped($elements),
                        count($invalids),
                        count($elements),
                        implode(', ', $invalids),
                    ),
                );
            }

            $clone = clone $this;

            foreach ($elements as $element) {
                $clone->elements[] = $element;
            }
        } catch (UnacceptableElementException $e) {
            throw new UnacceptableElementException(
                sprintf(
                    'Argument $elements = %s cannot be added to the current collection, %s',
                    Caster::getInstance()->castTyped($elements),
                    Caster::getInstance()->castTyped($this),
                ),
                0,
                $e,
            );
        } catch (Throwable $t) {
            throw new RuntimeException(
                ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                    $this,
                    new ReflectionMethod(self::class, __FUNCTION__),
                    func_get_args(),
                ),
                0,
                $t,
            );
        }

        return $clone;
    }

    public function withFiltered(Closure $callback): static
    {
        try {
            $clone = clone $this;
            $clone->elements = array_filter(
                $clone->elements,
                $callback,
                ARRAY_FILTER_USE_BOTH,
            );
        } catch (Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new ReflectionMethod(self::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    public function withMerged(CollectionInterface $collection): static
    {
        try {
            if (false === is_a($collection, static::class)) {
                throw new UnacceptableCollectionException(
                    sprintf(
                        'Argument $collection = %s must be an instance of %s, but it is not',
                        Caster::getInstance()->castTyped($collection),
                        Caster::makeNormalizedClassName(new ReflectionObject($this)),
                    ),
                );
            }

            $invalids = static::makeInvalids($collection->toArray());

            if ($invalids) {
                throw new UnacceptableElementException(
                    sprintf(
                        '%d/%d elements are invalid, including: [%s]',
                        count($invalids),
                        count($collection),
                        implode(', ', $invalids),
                    ),
                );
            }

            $clone = clone $this;
            $clone->elements = array_merge(
                $clone->elements,
                $collection->elements,
            );
        } catch (UnacceptableCollectionException | UnacceptableElementException $e) {
            throw new UnacceptableCollectionException(
                sprintf(
                    'The current collection, %s, cannot be merged with argument $collection = %s',
                    Caster::getInstance()->castTyped($this),
                    Caster::getInstance()->castTyped($collection),
                ),
                0,
                $e,
            );
        } catch (Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new ReflectionMethod(self::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    public function withRemoved(int|string $key): static
    {
        $clone = clone $this;

        unset($clone->elements[$key]);

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function withRemovedElement($element): static
    {
        try {
            static::assertIsElementAccepted($element);

            $clone = clone $this;
            $key = array_search($element, $this->elements, true);

            if (false !== $key) {
                unset($clone->elements[$key]);
            }
        } catch (UnacceptableElementException $e) {
            throw new UnacceptableElementException(
                sprintf(
                    'Argument $element = %s cannot be removed from the current collection, %s',
                    Caster::getInstance()->castTyped($element),
                    Caster::getInstance()->castTyped($this),
                ),
                0,
                $e,
            );
        } catch (Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new ReflectionMethod(self::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    public function withSet(int|string $key, mixed $element): static
    {
        try {
            static::assertIsElementAccepted($element);

            $clone = clone $this;
            $clone->elements[$key] = $element;
        } catch (UnacceptableElementException $e) {
            throw new UnacceptableElementException(
                sprintf(
                    'Argument $element = %s (with $key = %s) cannot be set on the current collection, %s',
                    Caster::getInstance()->castTyped($element),
                    Caster::getInstance()->castTyped($key),
                    Caster::getInstance()->castTyped($this),
                ),
                0,
                $e,
            );
        } catch (Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new ReflectionMethod(self::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    public function withSliced(int $offset, ?int $length = null): static
    {
        $clone = clone $this;
        $clone->elements = array_slice($clone->elements, $offset, $length, true);

        return $clone;
    }

    public function isEmpty(): bool
    {
        return !$this->elements;
    }
}
