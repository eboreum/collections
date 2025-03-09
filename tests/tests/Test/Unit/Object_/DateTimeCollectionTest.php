<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use DateTime;
use Eboreum\Collections\Object_\DateTimeCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Test\Unit\Eboreum\Collections\AbstractCollectionTestCase;

/**
 * @template T of DateTime
 * @template TCollection of DateTimeCollection<T>
 * @extends AbstractNamedClassOrInterfaceCollectionTestCase<T, TCollection>
 */
#[CoversClass(DateTimeCollection::class)]
class DateTimeCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
{
    /**
     * @return array<int, array{DateTime|null, array<DateTime>}>
     */
    public static function providerTestMaxWorks(): array
    {
        return [
            [
                null,
                [],
            ],
            (static function () {
                $elements = [new DateTime('2021-02-01 12:34:57')];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (static function () {
                $elements = [
                    new DateTime('2021-02-01 12:34:57'),
                    new DateTime('2021-02-01 12:34:56'),
                ];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (static function () {
                $elements = [
                    new DateTime('2021-02-01 12:34:57'),
                    new DateTime('2021-02-01 12:34:56'),
                    new DateTime('2021-02-01 12:34:55'),
                    new DateTime('2021-02-01 12:34:57'),
                    new DateTime('2021-02-01 12:34:55'),
                ];

                return [
                    $elements[3],
                    $elements,
                ];
            })(),
            (static function () {
                $elements = [
                    new DateTime('2021-01-01 00:00:00'),
                    new DateTime('2020-01-01 00:00:00'),
                    new DateTime('2022-01-01 00:00:00'),
                ];

                return [
                    $elements[2],
                    $elements,
                ];
            })(),
        ];
    }

    /**
     * @return array<int, array{DateTime|null, array<DateTime>}>
     */
    public static function providerTestMinWorks(): array
    {
        return [
            [
                null,
                [],
            ],
            (static function () {
                $elements = [new DateTime('2021-02-01 12:34:57')];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (static function () {
                $elements = [
                    new DateTime('2021-02-01 12:34:57'),
                    new DateTime('2021-02-01 12:34:56'),
                ];

                return [
                    $elements[1],
                    $elements,
                ];
            })(),
            (static function () {
                $elements = [
                    new DateTime('2021-02-01 12:34:57'),
                    new DateTime('2021-02-01 12:34:56'),
                    new DateTime('2021-02-01 12:34:55'),
                    new DateTime('2021-02-01 12:34:57'),
                    new DateTime('2021-02-01 12:34:55'),
                ];

                return [
                    $elements[2],
                    $elements,
                ];
            })(),
            (static function () {
                $elements = [
                    new DateTime('2021-01-01 00:00:00'),
                    new DateTime('2020-01-01 00:00:00'),
                    new DateTime('2022-01-01 00:00:00'),
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
        $a0 = self::createDateTimeCollection([0 => self::createDateTime()]);

        /** @var TCollection<T> $b0 */
        $b0 = self::createDateTimeCollection([0 => self::createDateTime()]);

        /** @var TCollection<T> $aAssociative */
        $aAssociative = self::createDateTimeCollection(['foo' => self::createDateTime()]);

        /** @var array<T> $elements */
        $elements = ['foo' => self::createDateTime()];

        /** @var TCollection<T> $bAssociative */
        $bAssociative = self::createDateTimeCollection($elements);

        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                $a0,
                $b0,
                static function (
                    self $self,
                    DateTimeCollection $collectionA,
                    DateTimeCollection $collectionB,
                    DateTimeCollection $collectionC,
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
                    DateTimeCollection $collectionA,
                    DateTimeCollection $collectionB,
                    DateTimeCollection $collectionC,
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
                        0 => self::createDateTime('2021-01-01 00:00:00+01:00'),
                    ];

                    return [
                        $elements,
                        $elements,
                    ];
                },
                static function (DateTime $object): string {
                    return $object->format('c');
                },
                true,
            ],
            [
                'Ascending, use first encountered.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => self::createDateTime('2021-01-01 00:00:00+01:00'),
                        1 => self::createDateTime('2021-01-01 00:00:01+01:00'),
                        2 => self::createDateTime('2021-01-01 00:00:02+01:00'),
                        3 => self::createDateTime('2021-01-01 00:00:01+01:00'),
                        4 => self::createDateTime('2021-01-01 00:00:03+01:00'),
                        5 => self::createDateTime('2021-01-01 00:00:01+01:00'),
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
                static function (DateTime $object): string {
                    return $object->format('c');
                },
                true,
            ],
            [
                'Ascending, use last encountered.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => self::createDateTime('2021-01-01 00:00:00+01:00'),
                        1 => self::createDateTime('2021-01-01 00:00:01+01:00'),
                        2 => self::createDateTime('2021-01-01 00:00:02+01:00'),
                        3 => self::createDateTime('2021-01-01 00:00:01+01:00'),
                        4 => self::createDateTime('2021-01-01 00:00:03+01:00'),
                        5 => self::createDateTime('2021-01-01 00:00:01+01:00'),
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
                static function (DateTime $object): string {
                    return $object->format('c');
                },
                false,
            ],
            [
                'Descending, use first encountered.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => self::createDateTime('2021-01-01 00:00:03+01:00'),
                        1 => self::createDateTime('2021-01-01 00:00:01+01:00'),
                        2 => self::createDateTime('2021-01-01 00:00:02+01:00'),
                        3 => self::createDateTime('2021-01-01 00:00:01+01:00'),
                        4 => self::createDateTime('2021-01-01 00:00:00+01:00'),
                        5 => self::createDateTime('2021-01-01 00:00:01+01:00'),
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
                static function (DateTime $object): string {
                    return $object->format('c');
                },
                true,
            ],
            [
                'Descending, use last encountered.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => self::createDateTime('2021-01-01 00:00:03+01:00'),
                        1 => self::createDateTime('2021-01-01 00:00:01+01:00'),
                        2 => self::createDateTime('2021-01-01 00:00:02+01:00'),
                        3 => self::createDateTime('2021-01-01 00:00:01+01:00'),
                        4 => self::createDateTime('2021-01-01 00:00:00+01:00'),
                        5 => self::createDateTime('2021-01-01 00:00:01+01:00'),
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
                static function (DateTime $object): string {
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
            0 => new DateTime(),
            'foo' => new DateTime(),
            42 => new DateTime(),
            43 => new DateTime(),
        ];

        return $elements;
    }

    protected static function createSingleElement(AbstractCollectionTestCase $self): DateTime
    {
        return new DateTime();
    }

    /**
     * @return class-string<TCollection<T>>
     */
    protected static function getHandledCollectionClassName(): string
    {
        return DateTimeCollection::class;
    }

    /**
     * @return T
     */
    protected static function createDateTime(string $timestamp = 'now'): DateTime
    {
        /** @var T $dateTime */
        $dateTime = new DateTime($timestamp);

        return $dateTime;
    }

    /**
     * @param array<int|string, T> $elements
     *
     * @return TCollection<T>
     */
    protected static function createDateTimeCollection(
        array $elements,
    ): DateTimeCollection {
        /** @var TCollection<T> $collection */
        $collection = new DateTimeCollection($elements);

        return $collection;
    }

    /**
     * @param array<int, DateTime> $elements
     */
    #[DataProvider('providerTestMaxWorks')]
    public function testMaxWorks(?DateTime $expected, array $elements): void
    {
        $dateTimeCollection = new DateTimeCollection($elements);
        $element = $dateTimeCollection->max();

        $this->assertSame($expected, $element);
    }

    /**
     * @param array<int, DateTime> $elements
     */
    #[DataProvider('providerTestMinWorks')]
    public function testMinWorks(?DateTime $expected, array $elements): void
    {
        $dateTimeCollection = new DateTimeCollection($elements);
        $element = $dateTimeCollection->min();

        $this->assertSame($expected, $element);
    }

    public function testToMicrosecondTimestampIntegerCollectionWorks(): void
    {
        /** @var array<T> $elements */
        $elements = [
            self::createDateTime('2021-02-01T12:34:56.000001+01:00'),
            self::createDateTime('2021-02-01T12:34:57.000002+01:00'),
            self::createDateTime('2021-02-01T12:34:58.000003+01:00'),
        ];

        $dateTimeCollection = self::createDateTimeCollection($elements);
        $integerCollection = $dateTimeCollection->toMicrosecondTimestampIntegerCollection();

        $this->assertSame($elements, $dateTimeCollection->toArray());
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
            self::createDateTime('2021-02-01 12:34:57'),
            self::createDateTime('2021-02-01 12:34:56'),
            self::createDateTime('2021-02-01 12:34:55'),
            self::createDateTime('2021-01-01 12:34:57'),
            self::createDateTime('2021-03-01 12:34:55'),
        ];

        $dateTimeCollectionA = self::createDateTimeCollection($elements);
        $dateTimeCollectionB = $dateTimeCollectionA->toSorted();

        $this->assertNotSame($dateTimeCollectionA, $dateTimeCollectionB);
        $this->assertSame($elements, $dateTimeCollectionA->toArray());
        $this->assertSame(
            [
                3 => $elements[3],
                2 => $elements[2],
                1 => $elements[1],
                0 => $elements[0],
                4 => $elements[4],
            ],
            $dateTimeCollectionB->toArray(),
        );
    }

    public function testToTimestampIntegerCollectionWorks(): void
    {
        /** @var array<T> $elements */
        $elements = [
            self::createDateTime('2021-02-01T12:34:56+01:00'),
            self::createDateTime('2021-02-01T12:34:57+01:00'),
            self::createDateTime('2021-02-01T12:34:58+01:00'),
        ];

        $dateTimeCollection = self::createDateTimeCollection($elements);
        $integerCollection = $dateTimeCollection->toTimestampIntegerCollection();

        $this->assertSame($elements, $dateTimeCollection->toArray());
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
            self::createDateTime('2021-02-01 12:34:57'),
            self::createDateTime('2021-02-01 12:34:56'),
            self::createDateTime('2021-02-01 12:34:55'),
            self::createDateTime('2021-01-01 12:34:57'),
            self::createDateTime('2021-03-01 12:34:55'),
            self::createDateTime('2021-02-01 12:34:55'),
            self::createDateTime('2021-02-01 12:34:57'),
        ];

        $dateTimeCollectionA = self::createDateTimeCollection($elements);
        $dateTimeCollectionB = $dateTimeCollectionA->toUnique(true);
        $dateTimeCollectionC = $dateTimeCollectionA->toUnique(false);

        $this->assertNotSame($dateTimeCollectionA, $dateTimeCollectionB);
        $this->assertNotSame($dateTimeCollectionA, $dateTimeCollectionC);
        $this->assertNotSame($dateTimeCollectionB, $dateTimeCollectionC);
        $this->assertSame($elements, $dateTimeCollectionA->toArray());
        $this->assertSame(
            [
                0 => $elements[0],
                1 => $elements[1],
                2 => $elements[2],
                3 => $elements[3],
                4 => $elements[4],
            ],
            $dateTimeCollectionB->toArray(),
        );
        $this->assertSame(
            [
                1 => $elements[1],
                3 => $elements[3],
                4 => $elements[4],
                5 => $elements[5],
                6 => $elements[6],
            ],
            $dateTimeCollectionC->toArray(),
        );
    }
}
