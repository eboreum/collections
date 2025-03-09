<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use DateTimeImmutable;
use Eboreum\Collections\ObjectCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use stdClass;
use Stringable;

use function dir;

/**
 * @template T of object
 * @template TCollection of ObjectCollection<T>
 * @extends AbstractTypeCollectionTestCase<T, TCollection>
 */
#[CoversClass(ObjectCollection::class)]
class ObjectCollectionTest extends AbstractTypeCollectionTestCase
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
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new class
                        {
                            public function __toString(): string
                            {
                                return 'foo';
                            }
                        },
                    ];

                    return [
                        $elements,
                        $elements,
                    ];
                },
                static function (object $object): string {
                    static::assertInstanceOf(Stringable::class, $object);

                    return (string)$object;
                },
                true,
            ],
            [
                'Ascending, use first encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new class
                        {
                            public function __toString(): string
                            {
                                return 'a';
                            }
                        },
                        1 => new class
                        {
                            public function __toString(): string
                            {
                                return 'b';
                            }
                        },
                        2 => new class
                        {
                            public function __toString(): string
                            {
                                return 'c';
                            }
                        },
                        3 => new class
                        {
                            public function __toString(): string
                            {
                                return 'b';
                            }
                        },
                        4 => new class
                        {
                            public function __toString(): string
                            {
                                return 'd';
                            }
                        },
                        5 => new class
                        {
                            public function __toString(): string
                            {
                                return 'b';
                            }
                        },
                    ];

                    /** @var array<T> $expected */
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
                static function (object $object): string {
                    static::assertInstanceOf(Stringable::class, $object);

                    return (string) $object;
                },
                true,
            ],
            [
                'Ascending, use last encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new class
                        {
                            public function __toString(): string
                            {
                                return 'a';
                            }
                        },
                        1 => new class
                        {
                            public function __toString(): string
                            {
                                return 'b';
                            }
                        },
                        2 => new class
                        {
                            public function __toString(): string
                            {
                                return 'c';
                            }
                        },
                        3 => new class
                        {
                            public function __toString(): string
                            {
                                return 'b';
                            }
                        },
                        4 => new class
                        {
                            public function __toString(): string
                            {
                                return 'd';
                            }
                        },
                        5 => new class
                        {
                            public function __toString(): string
                            {
                                return 'b';
                            }
                        },
                    ];

                    /** @var array<T> $expected */
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
                static function (object $object): string {
                    static::assertInstanceOf(Stringable::class, $object);

                    return (string) $object;
                },
                false,
            ],
            [
                'Descending, use first encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new class
                        {
                            public function __toString(): string
                            {
                                return 'd';
                            }
                        },
                        1 => new class
                        {
                            public function __toString(): string
                            {
                                return 'a';
                            }
                        },
                        2 => new class
                        {
                            public function __toString(): string
                            {
                                return 'c';
                            }
                        },
                        3 => new class
                        {
                            public function __toString(): string
                            {
                                return 'a';
                            }
                        },
                        4 => new class
                        {
                            public function __toString(): string
                            {
                                return 'b';
                            }
                        },
                        5 => new class
                        {
                            public function __toString(): string
                            {
                                return 'a';
                            }
                        },
                    ];

                    /** @var array<T> $expected */
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
                static function (object $object): string {
                    static::assertInstanceOf(Stringable::class, $object);

                    return (string) $object;
                },
                true,
            ],
            [
                'Descending, use last encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new class
                        {
                            public function __toString(): string
                            {
                                return 'd';
                            }
                        },
                        1 => new class
                        {
                            public function __toString(): string
                            {
                                return 'a';
                            }
                        },
                        2 => new class
                        {
                            public function __toString(): string
                            {
                                return 'c';
                            }
                        },
                        3 => new class
                        {
                            public function __toString(): string
                            {
                                return 'a';
                            }
                        },
                        4 => new class
                        {
                            public function __toString(): string
                            {
                                return 'b';
                            }
                        },
                        5 => new class
                        {
                            public function __toString(): string
                            {
                                return 'a';
                            }
                        },
                    ];

                    /** @var array<T> $expected */
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
                static function (object $object): string {
                    static::assertInstanceOf(Stringable::class, $object);

                    return (string) $object;
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
     *   },
     * >
     */
    public static function providerTestWithMergedWorks(): array
    {
        /** @var TCollection<T> $a0 */
        $a0 = new ObjectCollection([0 => new stdClass()]);

        /** @var TCollection<T> $b0 */
        $b0 = new ObjectCollection([0 => new DateTimeImmutable()]);

        /** @var TCollection<T> $aAssociative */
        $aAssociative = new ObjectCollection(['foo' => new stdClass()]);

        /** @var TCollection<T> $bAssociative */
        $bAssociative = new ObjectCollection(['foo' => new DateTimeImmutable()]);

        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                $a0,
                $b0,
                static function (
                    self $self,
                    ObjectCollection $collectionA,
                    ObjectCollection $collectionB,
                    ObjectCollection $collectionC,
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
                    ObjectCollection $collectionA,
                    ObjectCollection $collectionB,
                    ObjectCollection $collectionC,
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
     */
    protected static function createMultipleElements(AbstractCollectionTestCase $self): array
    {
        /** @var array{0: T, foo: T, 42: T, 43: T} $elements */
        $elements = [
            0 => new stdClass(),
            'foo' => new class
            {
            },
            42 => dir(__DIR__),
            43 => new DateTimeImmutable(),
        ];

        return $elements;
    }

    protected static function createSingleElement(AbstractCollectionTestCase $self): object
    {
        return new class
        {
        };
    }

    /**
     * @return class-string<TCollection<T>>
     */
    protected static function getHandledCollectionClassName(): string
    {
        return ObjectCollection::class;
    }
}
