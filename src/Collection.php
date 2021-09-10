<?php
/**
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
 */

declare(strict_types=1);

namespace Eboreum\Collections;

use Closure;
use Eboreum\Caster\Annotation\DebugIdentifier;
use Eboreum\Caster\Contract\DebugIdentifierAnnotationInterface;
use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Exception\InvalidArgumentException;
use Eboreum\Collections\Exception\RuntimeException;
use Eboreum\Exceptional\ExceptionMessageGenerator;

/**
 * {@inheritDoc}
 */
class Collection implements CollectionInterface, DebugIdentifierAnnotationInterface
{
    /**
     * @DebugIdentifier
     * @var array<int|string, mixed>
     */
    protected array $elements;

    /**
     * @param array<int|string, mixed> $elements
     * @throws RuntimeException
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
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function contains($element): bool
    {
        static::assertIsElementAccepted($element);

        return (false !== array_search($element, $this->elements, true));
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        $key = $this->key();

        if (null !== $key && array_key_exists($key, $this->elements)) {
            return $this->elements[$key];
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function each(Closure $callback, ?object $carry = null): void
    {
        try {
            foreach ($this->elements as $key => $value) {
                try {
                    $callback($value, $key, $carry);
                } catch (\Throwable $t) {
                    throw new RuntimeException(sprintf(
                        'Failure when calling $callback(%s, %s, %s)',
                        Caster::getInstance()->castTyped($value),
                        Caster::getInstance()->castTyped($key),
                        Caster::getInstance()->castTyped($carry)
                    ), 0, $t);
                }
            }
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function every(Closure $callback, ?object $carry = null): void
    {
        try {
            foreach ($this->elements as $key => $value) {
                try {
                    $result = $callback($value, $key, $carry);
                } catch (\Throwable $t) {
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
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function find(Closure $callback)
    {
        if (!$this->elements) {
            return null;
        }

        $return = null;

        try {
            foreach ($this->elements as $k => $v) {
                $callbackResult = $callback($v, $k);

                if (false === is_bool($callbackResult)) {
                    throw new RuntimeException(sprintf(
                        'Call $callback(%s, %s) did not return a boolean, which it must. Found return value: %s',
                        Caster::getInstance()->castTyped($v),
                        Caster::getInstance()->castTyped($k),
                        Caster::getInstance()->castTyped($callbackResult),
                    ));
                }

                if (true === $callbackResult) {
                    $return = $v;

                    break;
                }
            }
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $return;
    }

    /**
     * {@inheritDoc}
     */
    public function first()
    {
        reset($this->elements);

        $key = array_key_first($this->elements);

        if (null === $key) {
            return null;
        }

        return $this->elements[$key];
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        if (false === is_int($key) && false === is_string($key)) { /** @phpstan-ignore-line */
            throw new InvalidArgumentException(sprintf(
                'Argument $key must be int or string, but it is not. Found: %s',
                Caster::getInstance()->castTyped($key),
            ));
        }

        if (array_key_exists($key, $this->elements)) {
            return $this->elements[$key];
        }

        return null;
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
     * @return \ArrayIterator<int|string, mixed>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function has($key): bool
    {
        if (false === is_int($key) && false === is_string($key)) { /** @phpstan-ignore-line */
            throw new InvalidArgumentException(sprintf(
                'Argument $key must be int or string, but it is not. Found: %s',
                Caster::getInstance()->castTyped($key),
            ));
        }

        return array_key_exists($key, $this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function indexOf($element)
    {
        static::assertIsElementAccepted($element);

        $key = array_search($element, $this->elements, true);

        if (false === $key) {
            return null;
        }

        return $key;
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        end($this->elements);

        $key = array_key_last($this->elements);

        if (null === $key) {
            return null;
        }

        return $this->elements[$key];
    }

    /**
     * {@inheritDoc}
     */
    public function max(Closure $callback)
    {
        $max = null;
        $element = null;

        try {
            foreach ($this->elements as $key => $value) {
                try {
                    $result = $callback($value, $key);
                } catch (\Throwable $t) {
                    throw new RuntimeException(sprintf(
                        'Failure when calling $callback(%s, %s)',
                        Caster::getInstance()->castTyped($value),
                        Caster::getInstance()->castTyped($key),
                    ), 0, $t);
                }

                if (false === is_int($result)) {
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
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $element;
    }

    /**
     * {@inheritDoc}
     */
    public function min(Closure $callback)
    {
        $min = null;
        $element = null;

        try {
            foreach ($this->elements as $key => $value) {
                try {
                    $result = $callback($value, $key);
                } catch (\Throwable $t) {
                    throw new RuntimeException(sprintf(
                        'Failure when calling $callback(%s, %s)',
                        Caster::getInstance()->castTyped($value),
                        Caster::getInstance()->castTyped($key),
                    ), 0, $t);
                }

                if (false === is_int($result)) {
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
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $element;
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        next($this->elements);
        $key = key($this->elements);

        if (array_key_exists($key, $this->elements)) {
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

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, mixed>
     */
    public function toCleared(): self
    {
        $clone = clone $this;
        $clone->elements = [];

        return $clone;
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, mixed>
     */
    public function toReversed(bool $isPreservingKeys = true): self
    {
        $clone = clone $this;
        $clone->elements = array_reverse($clone->elements, $isPreservingKeys);

        return $clone;
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, mixed>
     */
    public function toSequential(): self
    {
        $clone = clone $this;
        $clone->elements = array_values($clone->elements);

        return $clone;
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, mixed>
     */
    public function toSortedByCallback(Closure $callback): self
    {
        try {
            $clone = clone $this;
            uasort($clone->elements, $callback);
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, mixed>
     */
    public function toUniqueByCallback(Closure $callback, bool $isUsingFirstEncounteredElement = true): self
    {
        try {
            $clone = clone $this;

            /**
             * @var array<string, array{key: int|string, value: mixed}>
             */
            $uniqueStringToKeyAndElement = [];

            foreach ($clone as $key => $value) {
                try {
                    $result = $callback($value, $key);
                } catch (\Throwable $t) {
                    throw new RuntimeException(sprintf(
                        'Failure when calling $callback(%s, %s)',
                        Caster::getInstance()->castTyped($value),
                        Caster::getInstance()->castTyped($key),
                    ), 0, $t);
                }

                if (false === is_string($result)) {
                    throw new RuntimeException(sprintf(
                        implode('', [
                            'Call $callback(%s, %s) must return string, but it did not.',
                            ' Found return value: %s',
                        ]),
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
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, mixed>
     */
    public function withAdded($element): self
    {
        try {
            static::assertIsElementAccepted($element);

            $clone = clone $this;
            $clone->elements[] = $element;
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, mixed>
     */
    public function withAddedMultiple(array $elements): self
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

            $clone = clone $this;

            foreach ($elements as $element) {
                $clone->elements[] = $element;
            }
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, mixed>
     */
    public function withFiltered(Closure $callback): self
    {
        try {
            $clone = clone $this;
            $clone->elements = array_filter(
                $clone->elements,
                $callback,
                ARRAY_FILTER_USE_BOTH,
            );
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, mixed>
     */
    public function withMerged(CollectionInterface $collection): self
    {
        try {
            if (false === is_a($collection, static::class)) {
                throw new RuntimeException(sprintf(
                    'Argument $collection must be an instance of %s, but it is not. Found: %s',
                    Caster::makeNormalizedClassName(new \ReflectionObject($this)),
                    Caster::getInstance()->castTyped($collection),
                ));
            }

            $invalids = static::makeInvalids($collection->toArray());

            if ($invalids) {
                throw new RuntimeException(sprintf(
                    implode('', [
                        'Argument $collection cannot be merged into the current collection',
                        ', because %d/%d elements are invalid, including: [%s]',
                    ]),
                    count($invalids),
                    count($collection),
                    implode(', ', $invalids),
                ));
            }

            $clone = clone $this;
            $clone->elements = array_merge(
                $clone->elements,
                $collection->elements,
            );
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, mixed>
     */
    public function withRemoved($key): self
    {
        if (false === is_int($key) && false === is_string($key)) { /** @phpstan-ignore-line */
            throw new InvalidArgumentException(sprintf(
                'Argument $key must be int or string, but it is not. Found: %s',
                Caster::getInstance()->castTyped($key),
            ));
        }

        $clone = clone $this;

        unset($clone->elements[$key]);

        return $clone;
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, mixed>
     */
    public function withRemovedElement($element): self
    {
        try {
            static::assertIsElementAccepted($element);

            $clone = clone $this;
            $key = array_search($element, $this->elements, true);

            if (false !== $key) {
                unset($clone->elements[$key]);
            }
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, mixed>
     */
    public function withSet($key, $element): self
    {
        try {
            $errorMessages = [];

            if (false === is_int($key) && false === is_string($key)) { /** @phpstan-ignore-line */
                $errorMessages[] = sprintf(
                    'Argument $key must be int or string, but it is not. Found: %s',
                    Caster::getInstance()->castTyped($key),
                );
            }

            if (false === static::isElementAccepted($element)) {
                $errorMessages[] = sprintf(
                    'Argument $element is not accepted by %s. Found: %s',
                    Caster::makeNormalizedClassName(new \ReflectionClass(static::class)),
                    Caster::getInstance()->castTyped($element),
                );
            }

            if ($errorMessages) {
                throw new RuntimeException(implode('. ', $errorMessages));
            }

            $clone = clone $this;
            $clone->elements[$key] = $element;
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    /**
     * {@inheritDoc}
     *
     * @return static<int|string, mixed>
     */
    public function withSliced(int $offset, ?int $length = null): self
    {
        $clone = clone $this;
        $clone->elements = array_slice($clone->elements, $offset, $length, true);

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool
    {
        return !$this->elements;
    }

    /**
     * {@inheritDoc}
     */
    public static function assertIsElementAccepted($element): void
    {
        if (false === static::isElementAccepted($element)) {
            throw new InvalidArgumentException(sprintf(
                'Argument $element is not accepted by %s. Found: %s',
                Caster::makeNormalizedClassName(new \ReflectionClass(static::class)),
                Caster::getInstance()->castTyped($element),
            ));
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

    /**
     * {@inheritDoc}
     */
    public static function isElementAccepted($element): bool
    {
        return true;
    }
}
