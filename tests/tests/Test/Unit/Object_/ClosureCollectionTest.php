<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Closure;
use Eboreum\Collections\Object_\ClosureCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use Test\Unit\Eboreum\Collections\AbstractCollectionTestCase;

/**
 * @template T of Closure
 * @template TCollection of ClosureCollection<T>
 * @extends AbstractNamedClassOrInterfaceCollectionTestCase<T, TCollection>
 */
#[CoversClass(ClosureCollection::class)]
class ClosureCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
{
    /**
     * {@inheritDoc}
     */
    public static function providerTestToUniqueByCallbackWorks(): array
    {
        return [
            [
                'Empty collection.',
                static function (): array {
                    return [
                        [],
                        [],
                    ];
                },
                static function (): string {
                    return '';
                },
                true,
            ],
            [
                '1 single item collection.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => static function (): string {
                            return '';
                        },
                    ];

                    return [
                        $elements,
                        $elements,
                    ];
                },
                static function (Closure $closure): string {
                    $result = $closure();

                    static::assertIsString($result);

                    return $result;
                },
                true,
            ],
            [
                'Ascending, use first encountered.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => static function (): string {
                            return 'a';
                        },
                        1 => static function (): string {
                            return 'b';
                        },
                        2 => static function (): string {
                            return 'c';
                        },
                        3 => static function (): string {
                            return 'b';
                        },
                        4 => static function (): string {
                            return 'd';
                        },
                        5 => static function (): string {
                            return 'b';
                        },
                    ];

                    /** @var array<int, T> $expected */
                    $expected = [
                        0 => $elements[0],
                        1 => $elements[1],
                        2 => $elements[2],
                        4 => $elements[4],
                    ];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (Closure $closure): string {
                    $result = $closure();

                    static::assertIsString($result);

                    return $result;
                },
                true,
            ],
            [
                'Ascending, use last encountered.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => static function (): string {
                            return 'a';
                        },
                        1 => static function (): string {
                            return 'b';
                        },
                        2 => static function (): string {
                            return 'c';
                        },
                        3 => static function (): string {
                            return 'b';
                        },
                        4 => static function (): string {
                            return 'd';
                        },
                        5 => static function (): string {
                            return 'b';
                        },
                    ];

                    /** @var array<int, T> $expected */
                    $expected = [
                        0 => $elements[0],
                        2 => $elements[2],
                        4 => $elements[4],
                        5 => $elements[5],
                    ];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (Closure $closure): string {
                    $result = $closure();

                    static::assertIsString($result);

                    return $result;
                },
                false,
            ],
            [
                'Descending, use first encountered.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => static function (): string {
                            return 'd';
                        },
                        1 => static function (): string {
                            return 'b';
                        },
                        2 => static function (): string {
                            return 'c';
                        },
                        3 => static function (): string {
                            return 'b';
                        },
                        4 => static function (): string {
                            return 'a';
                        },
                        5 => static function (): string {
                            return 'b';
                        },
                    ];

                    /** @var array<int, T> $expected */
                    $expected = [
                        0 => $elements[0],
                        1 => $elements[1],
                        2 => $elements[2],
                        4 => $elements[4],
                    ];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (Closure $closure): string {
                    $result = $closure();

                    static::assertIsString($result);

                    return $result;
                },
                true,
            ],
            [
                'Descending, use last encountered.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => static function (): string {
                            return 'd';
                        },
                        1 => static function (): string {
                            return 'b';
                        },
                        2 => static function (): string {
                            return 'c';
                        },
                        3 => static function (): string {
                            return 'b';
                        },
                        4 => static function (): string {
                            return 'a';
                        },
                        5 => static function (): string {
                            return 'b';
                        },
                    ];

                    /** @var array<int, T> $expected */
                    $expected = [
                        0 => $elements[0],
                        2 => $elements[2],
                        4 => $elements[4],
                        5 => $elements[5],
                    ];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (Closure $closure): string {
                    $result = $closure();

                    static::assertIsString($result);

                    return $result;
                },
                false,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @return array<
     *   int,
     *   array{
     *     string,
     *     TCollection<T>,
     *     TCollection<T>,
     *     Closure(self<T, TCollection<T>>, TCollection<T>, TCollection<T>, TCollection<T>, string): void,
     *   },
     * >
     */
    public static function providerTestWithMergedWorks(): array
    {
        /** @var TCollection<T> $a0 */
        $a0 = new ClosureCollection([
            0 => static function (): void {
                // Merely for test purposes
            },
        ]);

        /** @var TCollection<T> $b0 */
        $b0 = new ClosureCollection([
            0 => static function (): void {
                // Merely for test purposes
            },
        ]);

        /** @var TCollection<T> $aAssociative */
        $aAssociative = new ClosureCollection([
            'foo' => static function (): void {
                // Merely for test purposes
            },
        ]);

        /** @var TCollection<T> $bAssociative */
        $bAssociative = new ClosureCollection([
            'foo' => static function (): void {
                // Merely for test purposes
            },
        ]);

        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                $a0,
                $b0,
                static function (
                    self $self,
                    ClosureCollection $collectionA,
                    ClosureCollection $collectionB,
                    ClosureCollection $collectionC,
                    string $message
                ): void {
                    $self->assertCount(2, $collectionC, $message);
                    $self->assertSame([0, 1], $collectionC->getKeys(), $message);
                    $self->assertSame($collectionA->first(), $collectionC->first(), $message);
                    $self->assertSame($collectionB->first(), $collectionC->last(), $message);
                },
            ],
            [
                'Same name string keys. Will override.',
                $aAssociative,
                $bAssociative,
                static function (
                    self $self,
                    ClosureCollection $collectionA,
                    ClosureCollection $collectionB,
                    ClosureCollection $collectionC,
                    string $message
                ): void {
                    $self->assertCount(1, $collectionC, $message);
                    $self->assertSame(['foo'], $collectionC->getKeys(), $message);
                    $self->assertNotSame($collectionA->first(), $collectionC->first(), $message);
                    $self->assertSame($collectionB->first(), $collectionC->first(), $message);
                    $self->assertSame($collectionB->last(), $collectionC->last(), $message);
                },
            ],
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @return array<Closure>
     */
    protected static function createMultipleElements(AbstractCollectionTestCase $self): array
    {
        /** @var array{0: T, foo: T, 42: T, 43: T} $elements */
        $elements = [
            static function (): void {
                // Merely for test purposes
            },
            'foo' => static function (): void {
                // Merely for test purposes
            },
            42 => static function (): void {
                // Merely for test purposes
            },
            static function (): void {
                // Merely for test purposes
            },
        ];

        return $elements;
    }

    protected static function createSingleElement(AbstractCollectionTestCase $self): Closure
    {
        return static function (): void {
            // Merely for test purposes
        };
    }

    /**
     * @return class-string<TCollection<T>>
     */
    protected static function getHandledCollectionClassName(): string
    {
        return ClosureCollection::class;
    }
}
