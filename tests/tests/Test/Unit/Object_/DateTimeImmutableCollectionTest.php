<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use DateTimeImmutable;
use Eboreum\Collections\Contract\CollectionInterface\ToReindexedDuplicateKeyBehaviorEnum;
use Eboreum\Collections\Object_\DateTimeImmutableCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Test\Unit\Eboreum\Collections\AbstractCollectionTestCase;

/**
 * @template T of DateTimeImmutable
 * @template TCollection of DateTimeImmutableCollection<T>
 * @extends AbstractNamedClassOrInterfaceCollectionTestCase<T, TCollection>
 */
#[CoversClass(DateTimeImmutableCollection::class)]
class DateTimeImmutableCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
{
    /**
     * @return array<int, array{DateTimeImmutable|null, array<DateTimeImmutable>}>
     */
    public static function providerTestMaxWorks(): array
    {
        return [
            [
                null,
                [],
            ],
            (static function () {
                $elements = [new DateTimeImmutable('2021-02-01 12:34:57')];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (static function () {
                $elements = [
                    new DateTimeImmutable('2021-02-01 12:34:57'),
                    new DateTimeImmutable('2021-02-01 12:34:56'),
                ];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (static function () {
                $elements = [
                    new DateTimeImmutable('2021-02-01 12:34:57'),
                    new DateTimeImmutable('2021-02-01 12:34:56'),
                    new DateTimeImmutable('2021-02-01 12:34:55'),
                    new DateTimeImmutable('2021-02-01 12:34:57'),
                    new DateTimeImmutable('2021-02-01 12:34:55'),
                ];

                return [
                    $elements[3],
                    $elements,
                ];
            })(),
            (static function () {
                $elements = [
                    new DateTimeImmutable('2021-01-01 00:00:00'),
                    new DateTimeImmutable('2020-01-01 00:00:00'),
                    new DateTimeImmutable('2022-01-01 00:00:00'),
                ];

                return [
                    $elements[2],
                    $elements,
                ];
            })(),
        ];
    }

    /**
     * @return array<int, array{DateTimeImmutable|null, array<DateTimeImmutable>}>
     */
    public static function providerTestMinWorks(): array
    {
        return [
            [
                null,
                [],
            ],
            (static function () {
                $elements = [new DateTimeImmutable('2021-02-01 12:34:57')];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (static function () {
                $elements = [
                    new DateTimeImmutable('2021-02-01 12:34:57'),
                    new DateTimeImmutable('2021-02-01 12:34:56'),
                ];

                return [
                    $elements[1],
                    $elements,
                ];
            })(),
            (static function () {
                $elements = [
                    new DateTimeImmutable('2021-02-01 12:34:57'),
                    new DateTimeImmutable('2021-02-01 12:34:56'),
                    new DateTimeImmutable('2021-02-01 12:34:55'),
                    new DateTimeImmutable('2021-02-01 12:34:57'),
                    new DateTimeImmutable('2021-02-01 12:34:55'),
                ];

                return [
                    $elements[2],
                    $elements,
                ];
            })(),
            (static function () {
                $elements = [
                    new DateTimeImmutable('2021-01-01 00:00:00'),
                    new DateTimeImmutable('2020-01-01 00:00:00'),
                    new DateTimeImmutable('2022-01-01 00:00:00'),
                ];

                return [
                    $elements[1],
                    $elements,
                ];
            })(),
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
        $a0 = self::createDateTimeImmutableCollection([0 => self::createDateTimeImmutable()]);

        /** @var TCollection<T> $b0 */
        $b0 = self::createDateTimeImmutableCollection([0 => self::createDateTimeImmutable()]);

        /** @var TCollection<T> $aAssociative */
        $aAssociative = self::createDateTimeImmutableCollection(['foo' => self::createDateTimeImmutable()]);

        /** @var array<T> $elements */
        $elements = ['foo' => self::createDateTimeImmutable()];

        /** @var TCollection<T> $bAssociative */
        $bAssociative = self::createDateTimeImmutableCollection($elements);

        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                $a0,
                $b0,
                static function (
                    self $self,
                    DateTimeImmutableCollection $collectionA,
                    DateTimeImmutableCollection $collectionB,
                    DateTimeImmutableCollection $collectionC,
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
                    DateTimeImmutableCollection $collectionA,
                    DateTimeImmutableCollection $collectionB,
                    DateTimeImmutableCollection $collectionC,
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
                        0 => self::createDateTimeImmutable('2021-01-01 00:00:00+01:00'),
                    ];

                    return [
                        $elements,
                        $elements,
                    ];
                },
                static function (DateTimeImmutable $object): string {
                    return $object->format('c');
                },
                true,
            ],
            [
                'Ascending, use first encountered.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => self::createDateTimeImmutable('2021-01-01 00:00:00+01:00'),
                        1 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
                        2 => self::createDateTimeImmutable('2021-01-01 00:00:02+01:00'),
                        3 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
                        4 => self::createDateTimeImmutable('2021-01-01 00:00:03+01:00'),
                        5 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
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
                static function (DateTimeImmutable $object): string {
                    return $object->format('c');
                },
                true,
            ],
            [
                'Ascending, use last encountered.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => self::createDateTimeImmutable('2021-01-01 00:00:00+01:00'),
                        1 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
                        2 => self::createDateTimeImmutable('2021-01-01 00:00:02+01:00'),
                        3 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
                        4 => self::createDateTimeImmutable('2021-01-01 00:00:03+01:00'),
                        5 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
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
                static function (DateTimeImmutable $object): string {
                    return $object->format('c');
                },
                false,
            ],
            [
                'Descending, use first encountered.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => self::createDateTimeImmutable('2021-01-01 00:00:03+01:00'),
                        1 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
                        2 => self::createDateTimeImmutable('2021-01-01 00:00:02+01:00'),
                        3 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
                        4 => self::createDateTimeImmutable('2021-01-01 00:00:00+01:00'),
                        5 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
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
                static function (DateTimeImmutable $object): string {
                    return $object->format('c');
                },
                true,
            ],
            [
                'Descending, use last encountered.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => self::createDateTimeImmutable('2021-01-01 00:00:03+01:00'),
                        1 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
                        2 => self::createDateTimeImmutable('2021-01-01 00:00:02+01:00'),
                        3 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
                        4 => self::createDateTimeImmutable('2021-01-01 00:00:00+01:00'),
                        5 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
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
                static function (DateTimeImmutable $object): string {
                    return $object->format('c');
                },
                false,
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
            0 => new DateTimeImmutable(),
            'foo' => new DateTimeImmutable(),
            42 => new DateTimeImmutable(),
            43 => new DateTimeImmutable(),
        ];

        return $elements;
    }

    protected static function createSingleElement(AbstractCollectionTestCase $self): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    /**
     * @return class-string<TCollection<T>>
     */
    protected static function getHandledCollectionClassName(): string
    {
        return DateTimeImmutableCollection::class;
    }

    /**
     * @return T
     */
    protected static function createDateTimeImmutable(string $timestamp = 'now'): DateTimeImmutable
    {
        /** @var T $dateTimeImmutable */
        $dateTimeImmutable = new DateTimeImmutable($timestamp);

        return $dateTimeImmutable;
    }

    /**
     * @param array<int|string, T> $elements
     *
     * @return TCollection<T>
     */
    protected static function createDateTimeImmutableCollection(
        array $elements,
    ): DateTimeImmutableCollection {
        /** @var TCollection<T> $collection */
        $collection = new DateTimeImmutableCollection($elements);

        return $collection;
    }

    /**
     * @param array<int, DateTimeImmutable> $elements
     */
    #[DataProvider('providerTestMaxWorks')]
    public function testMaxWorks(?DateTimeImmutable $expected, array $elements): void
    {
        $dateTimeImmutableCollection = new DateTimeImmutableCollection($elements);
        $element = $dateTimeImmutableCollection->max();

        $this->assertSame($expected, $element);
    }

    /**
     * @param array<int, DateTimeImmutable> $elements
     */
    #[DataProvider('providerTestMinWorks')]
    public function testMinWorks(?DateTimeImmutable $expected, array $elements): void
    {
        $dateTimeImmutableCollection = new DateTimeImmutableCollection($elements);
        $element = $dateTimeImmutableCollection->min();

        $this->assertSame($expected, $element);
    }

    public function testToReindexedWorksWhenNoDuplicateKeysExist(): void
    {
        $elements = [
            new DateTimeImmutable('2021-02-01 12:34:57+01:00'),
            new DateTimeImmutable('2021-02-01 12:34:56+01:00'),
            new DateTimeImmutable('2021-02-01 12:34:55+01:00'),
            new DateTimeImmutable('2021-01-01 12:34:57+01:00'),
            new DateTimeImmutable('2021-03-01 12:34:55+01:00'),
        ];

        $dateTimeImmutableCollectionA = new DateTimeImmutableCollection($elements);
        $dateTimeImmutableCollectionB = $dateTimeImmutableCollectionA->toReindexed(
            static function (DateTimeImmutable $dateTime): string {
                return $dateTime->format('c');
            }
        );

        $this->assertNotSame($dateTimeImmutableCollectionA, $dateTimeImmutableCollectionB);
        $this->assertSame($elements, $dateTimeImmutableCollectionA->toArray());
        $this->assertSame(
            [
                '2021-02-01T12:34:57+01:00' => $elements[0],
                '2021-02-01T12:34:56+01:00' => $elements[1],
                '2021-02-01T12:34:55+01:00' => $elements[2],
                '2021-01-01T12:34:57+01:00' => $elements[3],
                '2021-03-01T12:34:55+01:00' => $elements[4],
            ],
            $dateTimeImmutableCollectionB->toArray(),
        );
    }

    public function testToReindexedWorksWhenFirstElementOnDuplicateKeysIsUsed(): void
    {
        $elements = [
            new DateTimeImmutable('2021-02-01 12:34:57+01:00'),
            new DateTimeImmutable('2021-02-01 12:34:56+01:00'),
            new DateTimeImmutable('2021-02-01 12:34:57+01:00'),
            new DateTimeImmutable('2021-02-01 12:34:55+01:00'),
            new DateTimeImmutable('2021-02-01 12:34:55+01:00'),
        ];

        $dateTimeImmutableCollectionA = new DateTimeImmutableCollection($elements);
        $dateTimeImmutableCollectionB = $dateTimeImmutableCollectionA->toReindexed(
            static function (DateTimeImmutable $dateTime): string {
                return $dateTime->format('c');
            },
            ToReindexedDuplicateKeyBehaviorEnum::use_first_element,
        );

        $this->assertNotSame($dateTimeImmutableCollectionA, $dateTimeImmutableCollectionB);
        $this->assertSame($elements, $dateTimeImmutableCollectionA->toArray());
        $this->assertSame(
            [
                '2021-02-01T12:34:57+01:00' => $elements[0],
                '2021-02-01T12:34:56+01:00' => $elements[1],
                '2021-02-01T12:34:55+01:00' => $elements[3],
            ],
            $dateTimeImmutableCollectionB->toArray(),
        );
    }

    public function testToReindexedWorksWhenLastElementOnDuplicateKeysIsUsed(): void
    {
        $elements = [
            new DateTimeImmutable('2021-02-01 12:34:57+01:00'),
            new DateTimeImmutable('2021-02-01 12:34:56+01:00'),
            new DateTimeImmutable('2021-02-01 12:34:57+01:00'),
            new DateTimeImmutable('2021-02-01 12:34:55+01:00'),
            new DateTimeImmutable('2021-02-01 12:34:55+01:00'),
        ];

        $dateTimeImmutableCollectionA = new DateTimeImmutableCollection($elements);
        $dateTimeImmutableCollectionB = $dateTimeImmutableCollectionA->toReindexed(
            static function (DateTimeImmutable $dateTime): string {
                return $dateTime->format('c');
            },
            ToReindexedDuplicateKeyBehaviorEnum::use_last_element,
        );

        $this->assertNotSame($dateTimeImmutableCollectionA, $dateTimeImmutableCollectionB);
        $this->assertSame($elements, $dateTimeImmutableCollectionA->toArray());
        $this->assertSame(
            [
                '2021-02-01T12:34:56+01:00' => $elements[1],
                '2021-02-01T12:34:57+01:00' => $elements[2],
                '2021-02-01T12:34:55+01:00' => $elements[4],
            ],
            $dateTimeImmutableCollectionB->toArray(),
        );
    }

    public function testToMicrosecondTimestampIntegerCollectionWorks(): void
    {
        /** @var array<T> $elements */
        $elements = [
            self::createDateTimeImmutable('2021-02-01T12:34:56.000001+01:00'),
            self::createDateTimeImmutable('2021-02-01T12:34:57.000002+01:00'),
            self::createDateTimeImmutable('2021-02-01T12:34:58.000003+01:00'),
        ];

        $dateTimeImmutableCollection = self::createDateTimeImmutableCollection($elements);
        $integerCollection = $dateTimeImmutableCollection->toMicrosecondTimestampIntegerCollection();

        $this->assertSame($elements, $dateTimeImmutableCollection->toArray());
        $this->assertSame(
            [
                1612179296000001,
                1612179297000002,
                1612179298000003,
            ],
            $integerCollection->toArray(),
        );
    }

    public function testToSortedWorks(): void
    {
        /** @var array<T> $elements */
        $elements = [
            self::createDateTimeImmutable('2021-02-01 12:34:57'),
            self::createDateTimeImmutable('2021-02-01 12:34:56'),
            self::createDateTimeImmutable('2021-02-01 12:34:55'),
            self::createDateTimeImmutable('2021-01-01 12:34:57'),
            self::createDateTimeImmutable('2021-03-01 12:34:55'),
        ];

        $dateTimeImmutableCollectionA = self::createDateTimeImmutableCollection($elements);
        $dateTimeImmutableCollectionB = $dateTimeImmutableCollectionA->toSorted();

        $this->assertNotSame($dateTimeImmutableCollectionA, $dateTimeImmutableCollectionB);
        $this->assertSame($elements, $dateTimeImmutableCollectionA->toArray());
        $this->assertSame(
            [
                3 => $elements[3],
                2 => $elements[2],
                1 => $elements[1],
                0 => $elements[0],
                4 => $elements[4],
            ],
            $dateTimeImmutableCollectionB->toArray(),
        );
    }

    public function testToTimestampIntegerCollectionWorks(): void
    {
        /** @var array<T> $elements */
        $elements = [
            self::createDateTimeImmutable('2021-02-01T12:34:56+01:00'),
            self::createDateTimeImmutable('2021-02-01T12:34:57+01:00'),
            self::createDateTimeImmutable('2021-02-01T12:34:58+01:00'),
        ];

        $dateTimeImmutableCollection = self::createDateTimeImmutableCollection($elements);
        $integerCollection = $dateTimeImmutableCollection->toTimestampIntegerCollection();

        $this->assertSame($elements, $dateTimeImmutableCollection->toArray());
        $this->assertSame(
            [
                1612179296,
                1612179297,
                1612179298,
            ],
            $integerCollection->toArray(),
        );
    }

    public function testToUniqueWorks(): void
    {
        /** @var array<T> $elements */
        $elements = [
            self::createDateTimeImmutable('2021-02-01 12:34:57'),
            self::createDateTimeImmutable('2021-02-01 12:34:56'),
            self::createDateTimeImmutable('2021-02-01 12:34:55'),
            self::createDateTimeImmutable('2021-01-01 12:34:57'),
            self::createDateTimeImmutable('2021-03-01 12:34:55'),
            self::createDateTimeImmutable('2021-02-01 12:34:55'),
            self::createDateTimeImmutable('2021-02-01 12:34:57'),
        ];

        $dateTimeImmutableCollectionA = self::createDateTimeImmutableCollection($elements);
        $dateTimeImmutableCollectionB = $dateTimeImmutableCollectionA->toUnique(true);
        $dateTimeImmutableCollectionC = $dateTimeImmutableCollectionA->toUnique(false);

        $this->assertNotSame($dateTimeImmutableCollectionA, $dateTimeImmutableCollectionB);
        $this->assertNotSame($dateTimeImmutableCollectionA, $dateTimeImmutableCollectionC);
        $this->assertNotSame($dateTimeImmutableCollectionB, $dateTimeImmutableCollectionC);
        $this->assertSame($elements, $dateTimeImmutableCollectionA->toArray());
        $this->assertSame(
            [
                0 => $elements[0],
                1 => $elements[1],
                2 => $elements[2],
                3 => $elements[3],
                4 => $elements[4],
            ],
            $dateTimeImmutableCollectionB->toArray(),
        );
        $this->assertSame(
            [
                1 => $elements[1],
                3 => $elements[3],
                4 => $elements[4],
                5 => $elements[5],
                6 => $elements[6],
            ],
            $dateTimeImmutableCollectionC->toArray(),
        );
    }
}
