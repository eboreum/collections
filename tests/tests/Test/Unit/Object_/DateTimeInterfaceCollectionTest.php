<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Eboreum\Collections\Object_\DateTimeInterfaceCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Test\Unit\Eboreum\Collections\AbstractCollectionTestCase;

/**
 * @template T of DateTimeInterface
 * @template TCollection of DateTimeInterfaceCollection<T>
 * @extends AbstractNamedClassOrInterfaceCollectionTestCase<T, TCollection>
 */
#[CoversClass(DateTimeInterfaceCollection::class)]
class DateTimeInterfaceCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
{
    /**
     * @return array<int, array{DateTimeInterface|null, array<DateTimeInterface>}>
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
                    new DateTimeImmutable('2021-02-01 12:34:56'),
                ];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (static function () {
                $elements = [
                    new DateTime('2021-02-01 12:34:57'),
                    new DateTimeImmutable('2021-02-01 12:34:56'),
                    new DateTimeImmutable('2021-02-01 12:34:55'),
                    new DateTime('2021-02-01 12:34:57'),
                    new DateTimeImmutable('2021-02-01 12:34:55'),
                ];

                return [
                    $elements[3],
                    $elements,
                ];
            })(),
            (static function () {
                $elements = [
                    new DateTime('2021-01-01 00:00:00'),
                    new DateTimeImmutable('2020-01-01 00:00:00'),
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
     * @return array<int, array{DateTimeInterface|null, array<DateTimeInterface>}>
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
                    new DateTime('2021-02-01 12:34:56'),
                    new DateTime('2021-02-01 12:34:55'),
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
        $a0 = self::createDateTimeInterfaceCollection([0 => self::createDateTimeImmutable()]);

        /** @var TCollection<T> $b0 */
        $b0 = self::createDateTimeInterfaceCollection([0 => self::createDateTimeImmutable()]);

        /** @var TCollection<T> $aAssociative */
        $aAssociative = self::createDateTimeInterfaceCollection(['foo' => self::createDateTimeImmutable()]);

        /** @var array<T> $elements */
        $elements = ['foo' => self::createDateTimeImmutable()];

        /** @var TCollection<T> $bAssociative */
        $bAssociative = self::createDateTimeInterfaceCollection($elements);

        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                $a0,
                $b0,
                static function (
                    self $self,
                    DateTimeInterfaceCollection $collectionA,
                    DateTimeInterfaceCollection $collectionB,
                    DateTimeInterfaceCollection $collectionC,
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
                    DateTimeInterfaceCollection $collectionA,
                    DateTimeInterfaceCollection $collectionB,
                    DateTimeInterfaceCollection $collectionC,
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
                    /** @var array<T> $elements */
                    $elements = [
                        0 => self::createDateTimeImmutable('2021-01-01 00:00:00+01:00'),
                    ];

                    return [
                        $elements,
                        $elements,
                    ];
                },
                static function (DateTimeInterface $object): string {
                    return $object->format('c');
                },
                true,
            ],
            [
                'Ascending, use first encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => self::createDateTimeImmutable('2021-01-01 00:00:00+01:00'),
                        1 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
                        2 => self::createDateTimeImmutable('2021-01-01 00:00:02+01:00'),
                        3 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
                        4 => self::createDateTimeImmutable('2021-01-01 00:00:03+01:00'),
                        5 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
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
                static function (DateTimeInterface $object): string {
                    return $object->format('c');
                },
                true,
            ],
            [
                'Ascending, use last encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => self::createDateTimeImmutable('2021-01-01 00:00:00+01:00'),
                        1 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
                        2 => self::createDateTimeImmutable('2021-01-01 00:00:02+01:00'),
                        3 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
                        4 => self::createDateTimeImmutable('2021-01-01 00:00:03+01:00'),
                        5 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
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
                static function (DateTimeInterface $object): string {
                    return $object->format('c');
                },
                false,
            ],
            [
                'Descending, use first encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => self::createDateTimeImmutable('2021-01-01 00:00:03+01:00'),
                        1 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
                        2 => self::createDateTimeImmutable('2021-01-01 00:00:02+01:00'),
                        3 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
                        4 => self::createDateTimeImmutable('2021-01-01 00:00:00+01:00'),
                        5 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
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
                static function (DateTimeInterface $object): string {
                    return $object->format('c');
                },
                true,
            ],
            [
                'Descending, use last encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => self::createDateTimeImmutable('2021-01-01 00:00:03+01:00'),
                        1 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
                        2 => self::createDateTimeImmutable('2021-01-01 00:00:02+01:00'),
                        3 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
                        4 => self::createDateTimeImmutable('2021-01-01 00:00:00+01:00'),
                        5 => self::createDateTimeImmutable('2021-01-01 00:00:01+01:00'),
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
                static function (DateTimeInterface $object): string {
                    return $object->format('c');
                },
                false,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @return array<DateTimeInterface>
     */
    protected static function createMultipleElements(AbstractCollectionTestCase $self): array
    {
        return [
            0 => new DateTime(),
            'foo' => new DateTimeImmutable(),
            42 => new DateTime(),
            43 => new DateTimeImmutable(),
        ];
    }

    protected static function createSingleElement(AbstractCollectionTestCase $self): DateTimeInterface
    {
        return new DateTimeImmutable();
    }

    /**
     * @return class-string<TCollection<T>>
     */
    protected static function getHandledCollectionClassName(): string
    {
        return DateTimeInterfaceCollection::class;
    }

    /**
     * @return T&DateTimeImmutable
     */
    protected static function createDateTimeImmutable(string $timestamp = 'now'): DateTimeInterface
    {
        /** @var T&DateTimeImmutable $dateTime */
        $dateTime = new DateTimeImmutable($timestamp);

        return $dateTime;
    }

    /**
     * @param array<int|string, T> $elements
     *
     * @return TCollection<T>
     */
    protected static function createDateTimeInterfaceCollection(
        array $elements,
    ): DateTimeInterfaceCollection {
        /** @var TCollection<T> $collection */
        $collection = new DateTimeInterfaceCollection($elements);

        return $collection;
    }

    /**
     * @param array<int, DateTimeInterface> $elements
     */
    #[DataProvider('providerTestMaxWorks')]
    public function testMaxWorks(?DateTimeInterface $expected, array $elements): void
    {
        $dateTimeCollection = new DateTimeInterfaceCollection($elements);
        $element = $dateTimeCollection->max();

        $this->assertSame($expected, $element);
    }

    /**
     * @param array<int, DateTimeInterface> $elements
     */
    #[DataProvider('providerTestMinWorks')]
    public function testMinWorks(?DateTimeInterface $expected, array $elements): void
    {
        $dateTimeCollection = new DateTimeInterfaceCollection($elements);
        $element = $dateTimeCollection->min();

        $this->assertSame($expected, $element);
    }

    public function testToMicrosecondTimestampIntegerCollectionWorks(): void
    {
        /** @var array<T> $elements */
        $elements = [
            self::createDateTimeImmutable('2021-02-01T12:34:56.000001+01:00'),
            self::createDateTimeImmutable('2021-02-01T12:34:57.000002+01:00'),
            self::createDateTimeImmutable('2021-02-01T12:34:58.000003+01:00'),
        ];

        $dateTimeInterfaceCollection = self::createDateTimeInterfaceCollection($elements);
        $integerCollection = $dateTimeInterfaceCollection->toMicrosecondTimestampIntegerCollection();

        $this->assertSame($elements, $dateTimeInterfaceCollection->toArray());
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

        $dateTimeInterfaceCollectionA = self::createDateTimeInterfaceCollection($elements);
        $dateTimeInterfaceCollectionB = $dateTimeInterfaceCollectionA->toSorted();

        $this->assertNotSame($dateTimeInterfaceCollectionA, $dateTimeInterfaceCollectionB);
        $this->assertSame($elements, $dateTimeInterfaceCollectionA->toArray());
        $this->assertSame(
            [
                3 => $elements[3],
                2 => $elements[2],
                1 => $elements[1],
                0 => $elements[0],
                4 => $elements[4],
            ],
            $dateTimeInterfaceCollectionB->toArray(),
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

        $dateTimeInterfaceCollection = self::createDateTimeInterfaceCollection($elements);
        $integerCollection = $dateTimeInterfaceCollection->toTimestampIntegerCollection();

        $this->assertSame($elements, $dateTimeInterfaceCollection->toArray());
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

        $dateTimeInterfaceCollectionA = self::createDateTimeInterfaceCollection($elements);
        $dateTimeInterfaceCollectionB = $dateTimeInterfaceCollectionA->toUnique(true);
        $dateTimeInterfaceCollectionC = $dateTimeInterfaceCollectionA->toUnique(false);

        $this->assertNotSame($dateTimeInterfaceCollectionA, $dateTimeInterfaceCollectionB);
        $this->assertNotSame($dateTimeInterfaceCollectionA, $dateTimeInterfaceCollectionC);
        $this->assertNotSame($dateTimeInterfaceCollectionB, $dateTimeInterfaceCollectionC);
        $this->assertSame($elements, $dateTimeInterfaceCollectionA->toArray());
        $this->assertSame(
            [
                0 => $elements[0],
                1 => $elements[1],
                2 => $elements[2],
                3 => $elements[3],
                4 => $elements[4],
            ],
            $dateTimeInterfaceCollectionB->toArray(),
        );
        $this->assertSame(
            [
                1 => $elements[1],
                3 => $elements[3],
                4 => $elements[4],
                5 => $elements[5],
                6 => $elements[6],
            ],
            $dateTimeInterfaceCollectionC->toArray(),
        );
    }
}
