<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use Closure;
use Eboreum\Collections\Caster;
use Eboreum\Collections\Collection;
use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Exception\ElementNotFoundException;
use Eboreum\Collections\Exception\KeyNotFoundException;
use Eboreum\Collections\Exception\RuntimeException;
use Eboreum\Collections\ExceptionMessageGenerator;
use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

use function array_key_first;
use function array_key_last;
use function array_keys;
use function array_map;
use function array_merge;
use function array_pop;
use function array_reverse;
use function array_slice;
use function array_values;
use function count;
use function current;
use function end;
use function explode;
use function implode;
use function preg_quote;
use function sprintf;

/**
 * @template T
 * @template TCollection of CollectionInterface<T>
 */
abstract class AbstractCollectionTestCase extends TestCase
{
    /**
     * @return array<int, array{Closure(self<T, TCollection<T>>):array{array<array<T>>, TCollection<T>}, int<1,max>}>
     */
    public static function providerTestChunkWorks(): array
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();

        return [
            [
                static function () use ($handledCollectionClassName): array {
                    $collection =  new $handledCollectionClassName();

                    return [
                        [],
                        $collection,
                    ];
                },
                1,
            ],
            [
                static function (self $self) use ($handledCollectionClassName): array {
                    /** @var array<T> $elements */
                    $elements = static::createMultipleElements($self);

                    $collection = new $handledCollectionClassName($elements);

                    return [
                        [
                            [
                                0 => $elements[0],
                            ],
                            [
                                'foo' => $elements['foo'],
                            ],
                            [
                                42 => $elements[42],
                            ],
                            [
                                43 => $elements[43],
                            ],
                        ],
                        $collection,
                    ];
                },
                1,
            ],
            [
                static function (self $self) use ($handledCollectionClassName): array {
                    $elements = static::createMultipleElements($self);
                    $collection = new $handledCollectionClassName($elements);

                    return [
                        [
                            [
                                0 => $elements[0],
                                'foo' => $elements['foo'],
                            ],
                            [
                                42 => $elements[42],
                                43 => $elements[43],
                            ],
                        ],
                        $collection,
                    ];
                },
                2,
            ],
            [
                static function (self $self) use ($handledCollectionClassName): array {
                    $elements = static::createMultipleElements($self);
                    $collection = new $handledCollectionClassName($elements);

                    return [
                        [
                            [
                                0 => $elements[0],
                                'foo' => $elements['foo'],
                                42 => $elements[42],
                            ],
                            [
                                43 => $elements[43],
                            ],
                        ],
                        $collection,
                    ];
                },
                3,
            ],
            [
                static function (self $self) use ($handledCollectionClassName): array {
                    $elements = static::createMultipleElements($self);
                    $collection = new $handledCollectionClassName($elements);

                    return [
                        [
                            [
                                0 => $elements[0],
                                'foo' => $elements['foo'],
                                42 => $elements[42],
                                43 => $elements[43],
                            ],
                        ],
                        $collection,
                    ];
                },
                4,
            ],
            [
                static function (self $self) use ($handledCollectionClassName): array {
                    $elements = static::createMultipleElements($self);
                    $collection = new $handledCollectionClassName($elements);

                    return [
                        [
                            [
                                0 => $elements[0],
                                'foo' => $elements['foo'],
                                42 => $elements[42],
                                43 => $elements[43],
                            ],
                        ],
                        $collection,
                    ];
                },
                5,
            ],
        ];
    }

    /**
     * @return array<int, array{Closure(self<T, TCollection<T>>):array{array<mixed>, array<T>, Closure}}>
     */
    public static function providerTestMapWorks(): array
    {
        return [
            [
                static function (): array {
                    return [
                        [],
                        [],
                        static function (): void {
                        },
                    ];
                },
            ],
            [
                static function (self $self): array {
                    return [
                        [
                            0 => null,
                            'foo' => null,
                            42 => null,
                            43 => null,
                        ],
                        static::createMultipleElements($self),
                        static function (): null {
                            return null;
                        },
                    ];
                },
            ],
            [
                static function (self $self): array {
                    $elements = static::createMultipleElements($self);

                    return [
                        $elements,
                        $elements,
                        static function ($v): mixed {
                            return $v;
                        },
                    ];
                },
            ],
            [
                static function (self $self): array {
                    return [
                        [
                            0 => 0,
                            'foo' => 'foo',
                            42 => 42,
                            43 => 43,
                        ],
                        static::createMultipleElements($self),
                        static function ($v, $k) {
                            return $k;
                        },
                    ];
                },
            ],
        ];
    }

    /**
     * @return array<int, array{Closure(self<T, TCollection<T>>):array{T, array<int|string, T>}}>
     */
    public static function providerTestMaxByCallbackWorks(): array
    {
        return [
            [
                static function (self $self): array {
                    $elements = array_values(static::createMultipleElements($self));

                    return [
                        $elements[0],
                        [$elements[0]],
                    ];
                },
            ],
            [
                static function (self $self): array {
                    $elements = array_values(static::createMultipleElements($self));

                    return [
                        $elements[1],
                        array_slice($elements, 0, 2),
                    ];
                },
            ],
            [
                static function (self $self): array {
                    $elements = array_values(static::createMultipleElements($self));

                    return [
                        $elements[2],
                        array_slice($elements, 0, 3),
                    ];
                },
            ],
            [
                static function (self $self): array {
                    $elements = array_values(static::createMultipleElements($self));

                    return [
                        $elements[3],
                        $elements,
                    ];
                },
            ],
            [
                static function (self $self): array {
                    $elements = array_values(static::createMultipleElements($self));

                    return [
                        $elements[3],
                        array_reverse($elements, true),
                    ];
                },
            ],
        ];
    }

    /**
     * @return array<int, array{Closure(self<T, TCollection>):array{T, array<int, T>}}>
     */
    public static function providerTestMinByCallbackWorks(): array
    {
        return [
            [
                static function (self $self): array {
                    $elements = array_values(static::createMultipleElements($self));

                    return [
                        $elements[0],
                        [$elements[0]],
                    ];
                },
            ],
            [
                static function (self $self): array {
                    $elements = array_values(static::createMultipleElements($self));

                    return [
                        $elements[0],
                        array_slice($elements, 0, 2),
                    ];
                },
            ],
            [
                static function (self $self): array {
                    $elements = array_values(static::createMultipleElements($self));

                    return [
                        $elements[0],
                        array_slice($elements, 0, 3),
                    ];
                },
            ],
            [
                static function (self $self): array {
                    $elements = array_values(static::createMultipleElements($self));

                    return [
                        $elements[0],
                        $elements,
                    ];
                },
            ],
            [
                static function (self $self): array {
                    $elements = array_values(static::createMultipleElements($self));

                    return [
                        $elements[0],
                        array_reverse($elements, true),
                    ];
                },
            ],
        ];
    }

    /**
     * @return array<
     *   int,
     *   array{
     *     string,
     *     Closure(self<T, TCollection<T>>):array{array<int, T>, array<int, T>},
     *     Closure(T, int|string):string,
     *     bool,
     *   },
     * >
     */
    abstract public static function providerTestToUniqueByCallbackWorks(): array;

    /**
     * @return array<array{string, TCollection<T>, TCollection<T>, Closure: void}>
     */
    abstract public static function providerTestWithMergedWorks(): array;

    /**
     * The name of the collection class being handled, including namespace.
     *
     * @return class-string<TCollection<T>>
     */
    abstract protected static function getHandledCollectionClassName(): string;

    /**
     * @param self<T, TCollection<T>> $self
     *
     * @return array{0: T, foo: T, 42: T, 43: T}
     */
    abstract protected static function createMultipleElements(self $self): array;

    /**
     * @param self<T, TCollection<T>> $self
     */
    abstract protected static function createSingleElement(self $self): mixed;

    public function testBasics(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(4, $collection);
        $this->assertSame([0, 'foo', 42, 43], $collection->getKeys());
        $this->assertSame($elements, $collection->toArray());
        $this->assertSame(array_values($elements), $collection->toArrayValues());
    }

    /**
     * @param Closure(self<T, TCollection<T>>):array{array<T>, TCollection<T>} $factory
     * @param int<1, max> $chunkSize
     */
    #[DataProvider('providerTestChunkWorks')]
    public function testChunkWorks(Closure $factory, int $chunkSize): void
    {
        [$expected, $collection] = $factory($this);

        $chunkedCollection = $collection->chunk($chunkSize);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertSame(count($expected), count($chunkedCollection));
        $this->assertNotSame($collection, $chunkedCollection);

        foreach ($chunkedCollection as $subcollection) {
            $this->assertNotSame($collection, $subcollection);
        }

        $found = array_map(
            function ($child): array {
                $this->assertInstanceOf(CollectionInterface::class, $child);

                return $child->toArray();
            },
            $chunkedCollection->toArray(),
        );

        $this->assertSame($expected, $found);
    }

    public function testChunkThrowsExceptionWhenArgumentChunkSizeIsOutOfBounds(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->assertInstanceOf(Collection::class, $collection);

        try {
            $collection->chunk(-1); // @phpstan-ignore-line
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertSame(
                ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                    $collection,
                    new ReflectionMethod($collection, 'chunk'),
                    [-1],
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'Argument $chunkSize = %s must be >= 1, but it is not',
                    Caster::getInstance()->castTyped(-1),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testContainsWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collectionA = new $handledCollectionClassName();

        $this->assertInstanceOf(Collection::class, $collectionA);
        $this->assertFalse($collectionA->contains($elements[0]));
        $this->assertFalse($collectionA->contains($elements['foo']));
        $this->assertFalse($collectionA->contains($elements[42]));
        $this->assertFalse($collectionA->contains($elements[43]));

        $collectionB = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collectionB);
        $this->assertTrue($collectionB->contains($elements[0]));
        $this->assertTrue($collectionB->contains($elements['foo']));
        $this->assertTrue($collectionB->contains($elements[42]));
        $this->assertTrue($collectionB->contains($elements[43]));
    }

    public function testCurrentWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = array_values(static::createMultipleElements($this));
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertSame($elements[0], $collection->current());
        $this->assertSame($elements[1], $collection->next());
        $this->assertSame($elements[1], $collection->current());
    }

    public function testCurrentReturnsNullWhenThereAreNoElementsInCollection(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertNull($collection->current());
    }

    public function testFindWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertSame($elements[0], $collection->find(static function ($v, $k): bool {
            return 0 === $k;
        }));
        $this->assertSame($elements['foo'], $collection->find(static function ($v, $k): bool {
            return 'foo' === $k;
        }));
        $this->assertSame($elements[42], $collection->find(static function ($v, $k): bool {
            return 42 === $k;
        }));
        $this->assertSame($elements[43], $collection->find(static function ($v, $k): bool {
            return 43 === $k;
        }));
        $this->assertNull($collection->find(static function ($v, $k): bool {
            return false;
        }));
    }

    public function testFindReturnsNullWhenThereAreNoElementsInCollection(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertNull($collection->find(static function ($v, $k) {
            return true;
        }));
    }

    public function testFirstWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertSame($elements[0], $collection->first());
        $this->assertSame($elements['foo'], $collection->next());
        $this->assertSame($elements['foo'], $collection->current());
        $this->assertSame($elements[0], $collection->first());
        $this->assertSame($elements[0], $collection->current());
        $this->assertSame($elements[43], $collection->last());
        $this->assertSame($elements[43], $collection->current());
        $this->assertSame($elements[0], $collection->first());
        $this->assertSame($elements[0], $collection->current());
    }

    public function testFirstKeyWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);

        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertSame(0, $collection->firstKey());

        $elements = [
            'bar' => static::createSingleElement($this),
            'foo' => static::createSingleElement($this),
        ];

        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertSame('bar', $collection->firstKey());
    }

    public function testFirstKeyReturnsNullWhenThereAreNoElementsInCollection(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertNull($collection->firstKey());
    }

    public function testFirstKeyOrFailThrowsKeyNotFoundExceptionWhenCollectionIsEmpty(): void
    {
        $collection = new Collection();

        $this->expectException(KeyNotFoundException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Collection %s is empty and therefore it does not have a "first" key',
                Caster::getInstance()->castTyped($collection)
            ),
        );

        $collection->firstKeyOrFail();
    }

    public function testFirstKeyOrFailWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertSame(0, $collection->firstKeyOrFail());
    }

    public function testFirstOrFailThrowsElementNotFoundExceptionWhenCollectionIsEmpty(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->expectException(ElementNotFoundException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Collection %s is empty and therefore it does not have a "first" element',
                Caster::getInstance()->castTyped($collection),
            ),
        );

        $collection->firstOrFail();
    }

    public function testFirstOrFailWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertSame($elements[array_key_first($elements)] ?? null, $collection->firstOrFail());
    }

    public function testGetWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertNull($collection->get(-1));
        $this->assertSame($elements[0], $collection->get(0));
        $this->assertSame($elements['foo'], $collection->get('foo'));
        $this->assertSame($elements[42], $collection->get(42));
        $this->assertSame($elements[43], $collection->get(43));
    }

    public function testGetIteratorWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        foreach ([$collection, (array)$collection->getIterator()] as $c) {
            foreach ($c as $k => $v) {
                $this->assertSame(
                    $elements[$k],
                    $v,
                );
            }
        }
    }

    public function testGetKeysWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertSame([0, 'foo', 42, 43], $collection->getKeys());
    }

    public function testGetOrFailThrowsElementNotFoundExceptionWhenCollectionDoesNotContainElement(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->expectException(ElementNotFoundException::class);
        $this->expectExceptionMessage(
            sprintf(
                'In collection %s, an element with $key = %s does not exist',
                Caster::getInstance()->castTyped($collection),
                Caster::getInstance()->castTyped('bar'),
            ),
        );

        $collection->getOrFail('bar');
    }

    public function testGetOrFailWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertSame($elements['foo'] ?? null, $collection->getOrFail('foo'));
    }

    public function testHasWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertFalse($collection->has(-1));
        $this->assertTrue($collection->has(0));
        $this->assertTrue($collection->has('foo'));
        $this->assertTrue($collection->has(42));
        $this->assertTrue($collection->has(43));
        $this->assertFalse($collection->has(44));
    }

    public function testIndexOfWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $element = array_pop($elements);
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertNull($collection->indexOf($element));
        $this->assertSame(0, $collection->indexOf($elements[0]));
        $this->assertSame('foo', $collection->indexOf($elements['foo']));
        $this->assertSame(42, $collection->indexOf($elements[42]));
    }

    public function testLastWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertSame($elements[43], $collection->last());
        $this->assertSame(null, $collection->next());
        $this->assertSame(null, $collection->current());
        $this->assertSame($elements[43], $collection->last());
        $this->assertSame($elements[43], $collection->current());
    }

    public function testLastKeyWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);

        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertSame(43, $collection->lastKey());

        $elements = [
            'bar' => static::createSingleElement($this),
            'foo' => static::createSingleElement($this),
        ];

        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertSame('foo', $collection->lastKey());
    }

    public function testLastKeyReturnsNullWhenThereAreNoElementsInCollection(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertNull($collection->lastKey());
    }

    public function testLastKeyOrFailThrowsKeyNotFoundExceptionWhenCollectionIsEmpty(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->assertInstanceOf(Collection::class, $collection);

        $this->expectException(KeyNotFoundException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Collection %s is empty and therefore it does not have a "last" key',
                Caster::getInstance()->castTyped($collection)
            ),
        );

        $collection->lastKeyOrFail();
    }

    public function testLastKeyOrFailWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertSame(43, $collection->lastKeyOrFail());
    }

    public function testLastOrFailThrowsElementNotFoundExceptionWhenCollectionIsEmpty(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->assertInstanceOf(Collection::class, $collection);

        $this->expectException(ElementNotFoundException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Collection %s is empty and therefore it does not have a "last" element',
                Caster::getInstance()->castTyped($collection),
            ),
        );

        $collection->lastOrFail();
    }

    public function testLastOrFailWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertSame($elements[array_key_last($elements)] ?? null, $collection->lastOrFail());
    }

    /**
     * @param Closure(self<T, TCollection<T>>):array{array<T>, array<T>, Closure} $factory
     */
    #[DataProvider('providerTestMapWorks')]
    public function testMapWorks(Closure $factory): void
    {
        [$expected, $elements, $callback] = $factory($this);
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertSame($expected, $collection->map($callback));
    }

    /**
     * @param Closure(self<T, TCollection<T>>):array{T, array<int, T>} $factory
     */
    #[DataProvider('providerTestMaxByCallbackWorks')]
    public function testMaxByCallbackWorks(Closure $factory): void
    {
        [$expected, $elements] = $factory($this);

        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertSame(
            $expected,
            $collection->maxByCallback(
                // @phpstan-ignore-next-line
                static function ($v, $k): mixed {
                    return $k;
                },
            ),
        );

        $collection = new $handledCollectionClassName();

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertNull(
            $collection->maxByCallback(
                static function () {
                    return 0;
                },
            ),
        );
    }

    /**
     * @param Closure(self<T, TCollection<T>>):array{T, array<int, T>} $factory
     */
    #[DataProvider('providerTestMinByCallbackWorks')]
    public function testMinByCallbackWorks(Closure $factory): void
    {
        [$expected, $elements] = $factory($this);

        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertSame(
            $expected,
            $collection->minByCallback(
                // @phpstan-ignore-next-line
                static function ($v, $k): mixed {
                    return $k;
                },
            ),
        );

        $collection = new $handledCollectionClassName();

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertNull($collection->minByCallback(static function () {
            return 0;
        }));
    }

    public function testNextWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertSame($elements[0], $collection->current());
        $this->assertSame($elements['foo'], $collection->next());
        $this->assertSame($elements['foo'], $collection->current());
        $this->assertSame($elements[42], $collection->next());
        $this->assertSame($elements[42], $collection->current());
        $this->assertSame($elements[43], $collection->next());
        $this->assertSame($elements[43], $collection->current());
    }

    public function testToClearedWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collectionA = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collectionA);

        $collectionB = $collectionA->toCleared();

        $this->assertInstanceOf(Collection::class, $collectionB);

        $collectionC = $collectionA->toCleared();

        $this->assertInstanceOf(Collection::class, $collectionC);

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertNotSame($collectionA, $collectionC);
        $this->assertNotSame($collectionB, $collectionC);
        $this->assertFalse($collectionA->isEmpty());
        $this->assertTrue($collectionB->isEmpty());
        $this->assertTrue($collectionC->isEmpty());
    }

    public function testToReversedWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collectionA = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collectionA);

        $collectionB = $collectionA->toReversed(true);

        $this->assertInstanceOf(Collection::class, $collectionB);

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elements, $collectionA->toArray());
        $this->assertSame(array_reverse($elements, true), $collectionB->toArray());

        $collectionC = $collectionA->toReversed(false);

        $this->assertInstanceOf(Collection::class, $collectionC);

        $this->assertNotSame($collectionA, $collectionC);
        $this->assertSame($elements, $collectionA->toArray());
        $this->assertSame(array_reverse($elements, false), $collectionC->toArray());
    }

    public function testToSequentialWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collectionA = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collectionA);

        $collectionB = $collectionA->toSequential();

        $this->assertInstanceOf(Collection::class, $collectionB);

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elements, $collectionA->toArray());
        $this->assertSame(array_values($elements), $collectionB->toArray());
    }

    public function testToSortedByCallbackWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collectionA = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collectionA);

        $collectionB = $collectionA->toSortedByCallback(static function () {
            return 1;
        });

        $this->assertInstanceOf(Collection::class, $collectionB);

        $expected = [
            43 => $elements[43],
            'foo' => $elements['foo'],
            42 => $elements[42],
            0 => $elements[0],
        ];

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elements, $collectionA->toArray());
        $this->assertSame($expected, $collectionB->toArray());
    }

    public function testToSortedByCallbackHandlesExceptionGracefullyWhenAFailureOccursInsideTheCallback(): void
    {
        $collection = new Collection([null, null]);
        $exception = new Exception();

        $callback = static function () use ($exception): void {
            throw $exception;
        };

        try {
            $collection->toSortedByCallback($callback);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertSame(
                ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                    $collection,
                    new ReflectionMethod($collection, 'toSortedByCallback'),
                    [$callback],
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame($exception, $currentException);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    /**
     * @param Closure(self<T, TCollection<T>>):array{array<int, T>, array<int, T>} $elementsFactory
     * @param Closure(T, int|string):string $callback
     */
    #[DataProvider('providerTestToUniqueByCallbackWorks')]
    public function testToUniqueByCallbackWorks(
        string $message,
        Closure $elementsFactory,
        Closure $callback,
        bool $isUsingFirstEncounteredElement,
    ): void {
        [$expected, $elements] = $elementsFactory($this);

        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collectionA = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collectionA);

        $collectionB = $collectionA->toUniqueByCallback(
            $callback, // @phpstan-ignore-line
            $isUsingFirstEncounteredElement,
        );

        $this->assertInstanceOf(Collection::class, $collectionB);

        $this->assertNotSame($collectionA, $collectionB, $message);
        $this->assertSame($elements, $collectionA->toArray(), $message);
        $this->assertSame($expected, $collectionB->toArray(), $message);
    }

    public function testToUniqueByCallbackHandlesExceptionGracefullyWhenAFailureInTheCallbackOccurs(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        $exception = new Exception();

        $callback = static function () use ($exception): void {
            throw $exception;
        };

        try {
            $collection->toUniqueByCallback($callback);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertSame(
                ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                    $collection,
                    new ReflectionMethod($collection, 'toUniqueByCallback'),
                    [$callback],
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    'Failure when calling \$callback\(.+, .+\)',
                    '$',
                    '/',
                ]),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame($exception, $currentException);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testToUniqueByCallbackThrowsExceptionWhenCallbackDoesNotReturnAString(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        $callback = static function (): null {
            return null;
        };

        try {
            $collection->toUniqueByCallback($callback); // @phpstan-ignore-line
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertSame(
                ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                    $collection,
                    new ReflectionMethod($collection, 'toUniqueByCallback'),
                    [$callback],
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    '/^Call \$callback\(.+, .+\) must return a string, but it did not\. Resulting return value\: %s$/',
                    preg_quote(Caster::getInstance()->castTyped(null), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithAddedWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);

        $collectionA = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collectionA);

        $element = static::createSingleElement($this);
        $collectionB = $collectionA->withAdded($element);

        $this->assertInstanceOf(Collection::class, $collectionB);

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elements, $collectionA->toArray());
        $this->assertCount(5, $collectionB);
        $this->assertSame(
            $elements,
            array_slice($collectionB->toArray(), 0, 4, true),
        );
        $this->assertSame(
            $element,
            $collectionB->last(),
        );
    }

    public function testWithAddedMultipleWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();

        $elementsA = array_slice(static::createMultipleElements($this), 0, 2);
        $elementsAdded = array_slice(static::createMultipleElements($this), 2, 2);
        $collectionA = new $handledCollectionClassName($elementsA);

        $this->assertInstanceOf(Collection::class, $collectionA);

        $collectionB = $collectionA->withAddedMultiple($elementsAdded);

        $this->assertInstanceOf(Collection::class, $collectionB);

        $expectedElementsB = array_merge(
            $elementsA,
            array_values($elementsAdded), // @phpstan-ignore-line
        );

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elementsA, $collectionA->toArray());
        $this->assertSame($expectedElementsB, $collectionB->toArray());
    }

    public function testWithFilteredWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);

        $collectionA = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collectionA);

        $index = -1;

        $collectionB = $collectionA->withFiltered(function ($v, $k) use ($elements, &$index): bool {
            $index++;

            $this->assertSame(array_keys($elements)[$index], $k);
            $this->assertSame(array_values($elements)[$index], $v);

            return 0 !== $k;
        });

        $this->assertInstanceOf(Collection::class, $collectionB);

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elements, $collectionA->toArray());
        $this->assertSame(array_slice($elements, 1, null, true), $collectionB->toArray());
    }

    /**
     * @param TCollection<T> $collectionA
     * @param TCollection<T> $collectionB
     */
    #[DataProvider('providerTestWithMergedWorks')]
    public function testWithMergedWorks(
        string $message,
        CollectionInterface $collectionA,
        CollectionInterface $collectionB,
        Closure $callback
    ): void {
        $collectionC = $collectionA->withMerged($collectionB);

        $this->assertNotSame($collectionA, $collectionC, $message);
        $this->assertNotSame($collectionB, $collectionC, $message);

        $callback($this, $collectionA, $collectionB, $collectionC, $message);
    }

    public function testWithRemovedWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);

        $collectionA = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collectionA);

        $collectionB = $collectionA->withRemoved(0);

        $this->assertInstanceOf(Collection::class, $collectionB);

        $collectionC = $collectionB->withRemoved(-1);

        $this->assertInstanceOf(Collection::class, $collectionC);

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertNotSame($collectionA, $collectionC);
        $this->assertNotSame($collectionB, $collectionC);
        $this->assertSame($elements, $collectionA->toArray());
        unset($elements[0]);
        $this->assertSame($elements, $collectionB->toArray());
        $this->assertSame($elements, $collectionC->toArray());
    }

    public function testWithRemovedElementWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $element = current($elements);

        $collectionA = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collectionA);

        $collectionB = $collectionA->withRemovedElement($element);

        $this->assertInstanceOf(Collection::class, $collectionB);

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elements, $collectionA->toArray());
        unset($elements[0]);
        $this->assertSame($elements, $collectionB->toArray());
    }

    public function testWithSetWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);

        $collectionA = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collectionA);

        $collectionB = $collectionA->withSet('foo', $elements[0]);

        $this->assertInstanceOf(Collection::class, $collectionB);

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elements, $collectionA->toArray());
        $expectedElementsB = $elements;
        $expectedElementsB['foo'] = $elements[0];
        $this->assertSame($expectedElementsB, $collectionB->toArray());
    }

    public function testWithSlicedWorks(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);
        $collectionA = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collectionA);

        $collectionB = $collectionA->withSliced(0);

        $this->assertInstanceOf(Collection::class, $collectionB);

        $collectionC = $collectionB->withSliced(1, 2);

        $this->assertInstanceOf(Collection::class, $collectionC);

        $collectionD = $collectionC->withSliced(0, 1);

        $this->assertInstanceOf(Collection::class, $collectionD);

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertNotSame($collectionA, $collectionC);
        $this->assertNotSame($collectionA, $collectionD);
        $this->assertNotSame($collectionB, $collectionC);
        $this->assertNotSame($collectionB, $collectionD);
        $this->assertNotSame($collectionC, $collectionD);
        $this->assertSame($elements, $collectionA->toArray());
        $this->assertSame($elements, $collectionB->toArray());
        $this->assertSame(array_slice($elements, 1, 2, true), $collectionC->toArray());
        $this->assertSame(array_slice($elements, 1, 1, true), $collectionD->toArray());
    }

    /**
     * The name of the collection class being handled, without namespace.
     */
    protected function getHandledCollectionClassNameShort(): string
    {
        $split = explode('\\', static::getHandledCollectionClassName());

        return end($split);
    }
}
