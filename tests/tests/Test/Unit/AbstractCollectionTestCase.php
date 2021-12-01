<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use Eboreum\Collections\Collection;
use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

abstract class AbstractCollectionTestCase extends TestCase
{
    public function testBasics(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collection = new $handledCollectionClassName($elements);

        $this->assertCount(4, $collection);
        $this->assertSame([0, 'foo', 42, 43], $collection->getKeys());
        $this->assertSame($elements, $collection->toArray());
        $this->assertSame(array_values($elements), $collection->toArrayValues());
    }

    /**
     * @dataProvider dataProvider_testChunkWorks
     *
     * @param array<mixed> $expected
     * @param array<mixed> $elements
     */
    public function testChunkWorks(array $expected, array $elements, int $chunkSize): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $collection = new $handledCollectionClassName($elements);
        $chunkedCollection = $collection->chunk($chunkSize);

        $this->assertSame(count($expected), count($chunkedCollection));
        $this->assertNotSame(get_class($collection), $chunkedCollection);

        foreach ($chunkedCollection as $subcollection) {
            $this->assertNotSame($collection, $subcollection);
        }

        $found = array_map(
            static function (CollectionInterface $child) {
                return $child->toArray();
            },
            $chunkedCollection->toArray(),
        );

        $this->assertSame($expected, $found);
    }

    /**
     * @return array<int, array{array<mixed>, array<mixed>, int}>
     */
    public function dataProvider_testChunkWorks(): array
    {
        return [
            [
                [],
                [],
                1,
            ],
            (function () {
                $elements = $this->createMultipleElements();

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
                    $elements,
                    1,
                ];
            })(),
            (function () {
                $elements = $this->createMultipleElements();

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
                    $elements,
                    2,
                ];
            })(),
            (function () {
                $elements = $this->createMultipleElements();

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
                    $elements,
                    3,
                ];
            })(),
            (function () {
                $elements = $this->createMultipleElements();

                return [
                    [
                        [
                            0 => $elements[0],
                            'foo' => $elements['foo'],
                            42 => $elements[42],
                            43 => $elements[43],
                        ],
                    ],
                    $elements,
                    4,
                ];
            })(),
            (function () {
                $elements = $this->createMultipleElements();

                return [
                    [
                        [
                            0 => $elements[0],
                            'foo' => $elements['foo'],
                            42 => $elements[42],
                            43 => $elements[43],
                        ],
                    ],
                    $elements,
                    5,
                ];
            })(),
        ];
    }

    public function testContainsWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collectionA = new $handledCollectionClassName();

        $this->assertFalse($collectionA->contains($elements[0]));
        $this->assertFalse($collectionA->contains($elements['foo']));
        $this->assertFalse($collectionA->contains($elements[42]));
        $this->assertFalse($collectionA->contains($elements[43]));

        $collectionB = new $handledCollectionClassName($elements);

        $this->assertTrue($collectionB->contains($elements[0]));
        $this->assertTrue($collectionB->contains($elements['foo']));
        $this->assertTrue($collectionB->contains($elements[42]));
        $this->assertTrue($collectionB->contains($elements[43]));
    }

    public function testCurrentWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = array_values($this->createMultipleElements());
        $collection = new $handledCollectionClassName($elements);

        $this->assertSame($elements[0], $collection->current());
        $this->assertSame($elements[1], $collection->next());
        $this->assertSame($elements[1], $collection->current());
    }

    public function testCurrentReturnsNullWhenThereAreNoElementsInCollection(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->assertNull($collection->current());
    }

    public function testFindWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collection = new $handledCollectionClassName($elements);

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
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->assertNull($collection->find(static function ($v, $k) {
            return true;
        }));
    }

    public function testFirstWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collection = new $handledCollectionClassName($elements);

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

    public function testFirstReturnsNullWhenThereAreNoElementsInCollection(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->assertNull($collection->first());
    }

    public function testFirstKeyWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();

        $this->assertSame(0, (new $handledCollectionClassName($elements))->firstKey());

        $elements = [
            'bar' => $this->createSingleElement(),
            'foo' => $this->createSingleElement(),
        ];

        $this->assertSame('bar', (new $handledCollectionClassName($elements))->firstKey());
    }

    public function testFirstKeyReturnsNullWhenThereAreNoElementsInCollection(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->assertNull($collection->firstKey());
    }

    public function testGetWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collection = new $handledCollectionClassName($elements);

        $this->assertNull($collection->get(-1));
        $this->assertSame($elements[0], $collection->get(0));
        $this->assertSame($elements['foo'], $collection->get('foo'));
        $this->assertSame($elements[42], $collection->get(42));
        $this->assertSame($elements[43], $collection->get(43));
    }

    public function testGetIteratorWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collection = new $handledCollectionClassName($elements);

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
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collection = new $handledCollectionClassName($elements);

        $this->assertSame([0, 'foo', 42, 43], $collection->getKeys());
    }

    public function testHasWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collection = new $handledCollectionClassName($elements);

        $this->assertFalse($collection->has(-1));
        $this->assertTrue($collection->has(0));
        $this->assertTrue($collection->has('foo'));
        $this->assertTrue($collection->has(42));
        $this->assertTrue($collection->has(43));
        $this->assertFalse($collection->has(44));
    }

    public function testIndexOfWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $element = array_pop($elements);
        $collection = new $handledCollectionClassName($elements);

        $this->assertNull($collection->indexOf($element));
        $this->assertSame(0, $collection->indexOf($elements[0]));
        $this->assertSame('foo', $collection->indexOf($elements['foo']));
        $this->assertSame(42, $collection->indexOf($elements[42]));
    }

    public function testLastWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collection = new $handledCollectionClassName($elements);

        $this->assertSame($elements[43], $collection->last());
        $this->assertSame(null, $collection->next());
        $this->assertSame(null, $collection->current());
        $this->assertSame($elements[43], $collection->last());
        $this->assertSame($elements[43], $collection->current());
    }

    public function testLastKeyWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();

        $this->assertSame(43, (new $handledCollectionClassName($elements))->lastKey());

        $elements = [
            'bar' => $this->createSingleElement(),
            'foo' => $this->createSingleElement(),
        ];

        $this->assertSame('foo', (new $handledCollectionClassName($elements))->lastKey());
    }

    public function testLastKeyReturnsNullWhenThereAreNoElementsInCollection(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->assertNull($collection->lastKey());
    }

    /**
     * @dataProvider dataProvider_testMapWorks
     *
     * @param array<mixed> $expected
     * @param array<mixed> $elements
     */
    public function testMapWorks(array $expected, array $elements, \Closure $callback): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $collection = new $handledCollectionClassName($elements);

        $this->assertSame($expected, $collection->map($callback));
    }

    /**
     * @return array<int, array{array<mixed>, array<mixed>, \Closure}>
     */
    public function dataProvider_testMapWorks(): array
    {
        return [
            [
                [],
                [],
                static function (): never {},
            ],
            [
                [
                    0 => null,
                    'foo' => null,
                    42 => null,
                    43 => null,
                ],
                $this->createMultipleElements(),
                static function () {
                    return null;
                },
            ],
            (function () {
                $elements = $this->createMultipleElements();

                return [
                    $elements,
                    $elements,
                    static function ($v, $k) {
                        return $v;
                    },
                ];
            })(),
            [
                [
                    0 => 0,
                    'foo' => 'foo',
                    42 => 42,
                    43 => 43,
                ],
                $this->createMultipleElements(),
                static function ($v, $k) {
                    return $k;
                },
            ],
        ];
    }

    /**
     * @dataProvider dataProvider_testMaxByCallbackWorks
     * @param mixed $expectedMaxByCallback
     * @param array<int, mixed> $elements
     */
    public function testMaxByCallbackWorks($expectedMaxByCallback, array $elements): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $collection = new $handledCollectionClassName($elements);

        $this->assertSame($expectedMaxByCallback, $collection->maxByCallback(static function ($v, $k) {
            return $k;
        }));

        $collection = new $handledCollectionClassName();

        $this->assertNull($collection->maxByCallback(static function () {
            return 0;
        }));
    }

    /**
     * @return array<int, array{mixed, array<int, mixed>}>
     */
    public function dataProvider_testMaxByCallbackWorks(): array
    {
        return [
            (function (): array {
                $elements = array_values($this->createMultipleElements());

                return [
                    $elements[0],
                    [$elements[0]],
                ];
            })(),
            (function (): array {
                $elements = array_values($this->createMultipleElements());

                return [
                    $elements[1],
                    array_slice($elements, 0, 2),
                ];
            })(),
            (function (): array {
                $elements = array_values($this->createMultipleElements());

                return [
                    $elements[2],
                    array_slice($elements, 0, 3),
                ];
            })(),
            (function (): array {
                $elements = array_values($this->createMultipleElements());

                return [
                    $elements[3],
                    $elements,
                ];
            })(),
            (function (): array {
                $elements = array_values($this->createMultipleElements());

                return [
                    $elements[3],
                    array_reverse($elements, true),
                ];
            })(),
        ];
    }

    /**
     * @dataProvider dataProvider_testMinByCallbackWorks
     * @param mixed $expectedMinByCallback
     * @param array<int, mixed> $elements
     */
    public function testMinByCallbackWorks($expectedMinByCallback, array $elements): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $collection = new $handledCollectionClassName($elements);

        $this->assertSame($expectedMinByCallback, $collection->minByCallback(static function ($v, $k) {
            return $k;
        }));

        $collection = new $handledCollectionClassName();

        $this->assertNull($collection->minByCallback(static function () {
            return 0;
        }));
    }

    /**
     * @return array<int, array{mixed, array<int, mixed>}>
     */
    public function dataProvider_testMinByCallbackWorks(): array
    {
        return [
            (function (): array {
                $elements = array_values($this->createMultipleElements());

                return [
                    $elements[0],
                    [$elements[0]],
                ];
            })(),
            (function (): array {
                $elements = array_values($this->createMultipleElements());

                return [
                    $elements[0],
                    array_slice($elements, 0, 2),
                ];
            })(),
            (function (): array {
                $elements = array_values($this->createMultipleElements());

                return [
                    $elements[0],
                    array_slice($elements, 0, 3),
                ];
            })(),
            (function (): array {
                $elements = array_values($this->createMultipleElements());

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (function (): array {
                $elements = array_values($this->createMultipleElements());

                return [
                    $elements[0],
                    array_reverse($elements, true),
                ];
            })(),
        ];
    }

    public function testNextWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collection = new $handledCollectionClassName($elements);

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
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collectionA = new $handledCollectionClassName($elements);

        $collectionB = $collectionA->toCleared();
        $collectionC = $collectionA->toCleared();

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertNotSame($collectionA, $collectionC);
        $this->assertNotSame($collectionB, $collectionC);
        $this->assertFalse($collectionA->isEmpty());
        $this->assertTrue($collectionB->isEmpty());
        $this->assertTrue($collectionC->isEmpty());
    }

    public function testToReversedWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collectionA = new $handledCollectionClassName($elements);

        $collectionB = $collectionA->toReversed(true);

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elements, $collectionA->toArray());
        $this->assertSame(array_reverse($elements, true), $collectionB->toArray());

        $collectionC = $collectionA->toReversed(false);

        $this->assertNotSame($collectionA, $collectionC);
        $this->assertSame($elements, $collectionA->toArray());
        $this->assertSame(array_reverse($elements, false), $collectionC->toArray());
    }

    public function testToSequentialWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collectionA = new $handledCollectionClassName($elements);

        $collectionB = $collectionA->toSequential(true);

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elements, $collectionA->toArray());
        $this->assertSame(array_values($elements), $collectionB->toArray());
    }

    public function testToSortedByCallbackWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collectionA = new $handledCollectionClassName($elements);

        $collectionB = $collectionA->toSortedByCallback(static function () {
            return 1;
        });

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
        $exception = new \Exception();

        try {
            $collection->toSortedByCallback(static function () use ($exception): void {
                throw $exception;
            });
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>toSortedByCallback\(',
                            '\$callback = \(object\) \\\\Closure\(\)',
                        '\) inside \(object\) \\\\%s \{',
                            '\$elements = \(array\(2\)\) \[.+\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame($exception, $currentException);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    /**
     * @dataProvider dataProvider_testToUniqueByCallbackWorks
     * @param array<int, mixed> $expected
     * @param array<int, mixed> $elements
     */
    public function testToUniqueByCallbackWorks(
        string $message,
        array $expected,
        array $elements,
        \Closure $callback,
        bool $isUsingFirstEncounteredElement
    ): void {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $collectionA = new $handledCollectionClassName($elements);

        $collectionB = $collectionA->toUniqueByCallback(
            $callback,
            $isUsingFirstEncounteredElement,
        );

        $this->assertNotSame($collectionA, $collectionB, $message);
        $this->assertSame($elements, $collectionA->toArray(), $message);
        $this->assertSame($expected, $collectionB->toArray(), $message);
    }

    public function testToUniqueByCallbackHandlesExceptionGracefullyWhenAFailureInTheCallbackOccurs(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collection = new $handledCollectionClassName($elements);
        $exception = new \Exception();

        try {
            $collection->toUniqueByCallback(static function () use ($exception): void {
                throw $exception;
            });
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>toUniqueByCallback\(',
                            '\$callback = \(object\) \\\\Closure\(\)',
                            ', \$isUsingFirstEncounteredElement = \(bool\) true',
                        '\) inside \(object\) \\\\%s \{',
                            '%s\$elements = \(array\(4\)\) \[.+\] \(sample\)',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote($handledCollectionClassName, '/'),
                    preg_quote($handledCollectionClassName, '/'),
                    (
                        Collection::class !== $handledCollectionClassName
                        ? preg_quote('\\' . Collection::class . '->', '/')
                        : ''
                    ),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            $this->assertSame($exception, $currentException);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testToUniqueByCallbackThrowsExceptionWhenCallbackDoesNotReturnAString(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collection = new $handledCollectionClassName($elements);

        try {
            $collection->toUniqueByCallback(static function () {
                return null;
            });
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>toUniqueByCallback\(',
                            '\$callback = \(object\) \\\\Closure\(\)',
                            ', \$isUsingFirstEncounteredElement = \(bool\) true',
                        '\) inside \(object\) \\\\%s \{',
                            '%s\$elements = \(array\(4\)\) \[.+\] \(sample\)',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote($handledCollectionClassName, '/'),
                    preg_quote($handledCollectionClassName, '/'),
                    (
                        Collection::class !== $handledCollectionClassName
                        ? preg_quote('\\' . Collection::class . '->', '/')
                        : ''
                    ),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    'Call \$callback\(.+, .+\) must return string, but it did not\.',
                    ' Found return value\: \(null\) null',
                    '$',
                    '/',
                ]),
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
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();

        $collectionA = new $handledCollectionClassName($elements);

        $element = $this->createSingleElement();
        $collectionB = $collectionA->withAdded($element);

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
        $handledCollectionClassName = $this->getHandledCollectionClassName();

        $elementsA = array_slice($this->createMultipleElements(), 0, 2);
        $elementsAdded = array_slice($this->createMultipleElements(), 2, 2);
        $collectionA = new $handledCollectionClassName($elementsA);

        $collectionB = $collectionA->withAddedMultiple($elementsAdded);
        $expectedElementsB = array_merge(
            $elementsA,
            array_values($elementsAdded),
        );

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elementsA, $collectionA->toArray());
        $this->assertSame($expectedElementsB, $collectionB->toArray());
    }

    public function testWithFilteredWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();

        $collectionA = new $handledCollectionClassName($elements);

        $index = -1;

        $collectionB = $collectionA->withFiltered(function ($v, $k) use ($elements, &$index): bool {
            $index++;

            $this->assertSame(array_keys($elements)[$index], $k);
            $this->assertSame(array_values($elements)[$index], $v);

            return 0 !== $k;
        });

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elements, $collectionA->toArray());
        $this->assertSame(array_slice($elements, 1, null, true), $collectionB->toArray());
    }

    /**
     * @dataProvider dataProvider_testWithMergedWorks
     * @param CollectionInterface<int|string, mixed> $collectionA
     * @param CollectionInterface<int|string, mixed> $collectionB
     */
    public function testWithMergedWorks(
        string $message,
        CollectionInterface $collectionA,
        CollectionInterface $collectionB,
        \Closure $callback
    ): void {
        $collectionC = $collectionA->withMerged($collectionB);

        $this->assertNotSame($collectionA, $collectionC, $message);
        $this->assertNotSame($collectionB, $collectionC, $message);

        $callback($collectionA, $collectionB, $collectionC, $message);
    }

    public function testWithRemovedWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();

        $collectionA = new $handledCollectionClassName($elements);

        $collectionB = $collectionA->withRemoved(0);
        $collectionC = $collectionB->withRemoved(-1);

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
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $element = current($elements);

        $collectionA = new $handledCollectionClassName($elements);

        $collectionB = $collectionA->withRemovedElement($element);

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elements, $collectionA->toArray());
        unset($elements[0]);
        $this->assertSame($elements, $collectionB->toArray());
    }

    public function testWithSetWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();

        $collectionA = new $handledCollectionClassName($elements);

        $collectionB = $collectionA->withSet('foo', $elements[0]);

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elements, $collectionA->toArray());
        $expectedElementsB = $elements;
        $expectedElementsB['foo'] = $elements[0];
        $this->assertSame($expectedElementsB, $collectionB->toArray());
    }

    public function testWithSlicedWorks(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();
        $collectionA = new $handledCollectionClassName($elements);

        $collectionB = $collectionA->withSliced(0);
        $collectionC = $collectionB->withSliced(1, 2);
        $collectionD = $collectionC->withSliced(0, 1);

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
        $split = explode('\\', $this->getHandledCollectionClassName());

        return end($split);
    }

    /**
     * @return array<int, array{string, array<int, mixed>, array<int, mixed>, \Closure, bool}>
     */
    abstract public function dataProvider_testToUniqueByCallbackWorks(): array;

    /**
     * @return array<int, array{0: string, 1: CollectionInterface, 2: CollectionInterface, 3: \Closure}>
     */
    abstract public function dataProvider_testWithMergedWorks(): array;

    /**
     * The name of the collection class being handled, including namespace.
     */
    abstract protected function getHandledCollectionClassName(): string;

    /**
     * @return mixed
     */
    abstract protected function createSingleElement();

    /**
     * @return array{0: mixed, foo: mixed, 42: mixed, 43: mixed}
     */
    abstract protected function createMultipleElements(): array;
}
