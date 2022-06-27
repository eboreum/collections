<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use DateTimeImmutable;
use Eboreum\Collections\Contract\CollectionInterface\ToReindexedDuplicateKeyBehaviorEnum;
use Eboreum\Collections\Object_\DateTimeImmutableCollection;

class DateTimeImmutableCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
{
    /**
     * @dataProvider dataProvider_testMaxWorks
     *
     * @param array<int, DateTimeImmutable> $elements
     */
    public function testMaxWorks(?DateTimeImmutable $expected, array $elements): void
    {
        $dateTimeImmutableCollection = new DateTimeImmutableCollection($elements);
        $element = $dateTimeImmutableCollection->max();

        $this->assertSame($expected, $element);
    }

    /**
     * @return array<int, array{DateTimeImmutable|null, array<DateTimeImmutable>}>
     */
    public function dataProvider_testMaxWorks(): array
    {
        return [
            [
                null,
                [],
            ],
            (function(){
                $elements = [new DateTimeImmutable('2021-02-01 12:34:57')];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (function(){
                $elements = [
                    new DateTimeImmutable('2021-02-01 12:34:57'),
                    new DateTimeImmutable('2021-02-01 12:34:56'),
                ];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (function(){
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
            (function(){
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
     * @dataProvider dataProvider_testMinWorks
     *
     * @param array<int, DateTimeImmutable> $elements
     */
    public function testMinWorks(?DateTimeImmutable $expected, array $elements): void
    {
        $dateTimeImmutableCollection = new DateTimeImmutableCollection($elements);
        $element = $dateTimeImmutableCollection->min();

        $this->assertSame($expected, $element);
    }

    /**
     * @return array<int, array{DateTimeImmutable|null, array<DateTimeImmutable>}>
     */
    public function dataProvider_testMinWorks(): array
    {
        return [
            [
                null,
                [],
            ],
            (function(){
                $elements = [new DateTimeImmutable('2021-02-01 12:34:57')];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (function(){
                $elements = [
                    new DateTimeImmutable('2021-02-01 12:34:57'),
                    new DateTimeImmutable('2021-02-01 12:34:56'),
                ];

                return [
                    $elements[1],
                    $elements,
                ];
            })(),
            (function(){
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
            (function(){
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

    public function testToSortedWorks(): void
    {
        $elements = [
            new DateTimeImmutable('2021-02-01 12:34:57'),
            new DateTimeImmutable('2021-02-01 12:34:56'),
            new DateTimeImmutable('2021-02-01 12:34:55'),
            new DateTimeImmutable('2021-01-01 12:34:57'),
            new DateTimeImmutable('2021-03-01 12:34:55'),
        ];

        $dateTimeImmutableCollectionA = new DateTimeImmutableCollection($elements);
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

    public function testToUniqueWorks(): void
    {
        $elements = [
            new DateTimeImmutable('2021-02-01 12:34:57'),
            new DateTimeImmutable('2021-02-01 12:34:56'),
            new DateTimeImmutable('2021-02-01 12:34:55'),
            new DateTimeImmutable('2021-01-01 12:34:57'),
            new DateTimeImmutable('2021-03-01 12:34:55'),
            new DateTimeImmutable('2021-02-01 12:34:55'),
            new DateTimeImmutable('2021-02-01 12:34:57'),
        ];

        $dateTimeImmutableCollectionA = new DateTimeImmutableCollection($elements);
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

    /**
     * {@inheritDoc}
     */
    public function dataProvider_testToUniqueByCallbackWorks(): array
    {
        return [
            [
                'Empty collection.',
                [],
                [],
                static function (): string {
                    return '';
                },
                true,
            ],
            (static function (): array {
                $elements = [
                    0 => new DateTimeImmutable('2021-01-01 00:00:00+01:00'),
                ];

                return [
                    '1 single item collection.',
                    $elements,
                    $elements,
                    static function (DateTimeImmutable $object): string {
                        return $object->format('c');
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new DateTimeImmutable('2021-01-01 00:00:00+01:00'),
                    1 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                    2 => new DateTimeImmutable('2021-01-01 00:00:02+01:00'),
                    3 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                    4 => new DateTimeImmutable('2021-01-01 00:00:03+01:00'),
                    5 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                ];

                return [
                    'Ascending, use first encountered.',
                    [
                        0 => $elements[0],
                        1 => $elements[1],
                        2 => $elements[2],
                        4 => $elements[4],
                    ],
                    $elements,
                    static function (DateTimeImmutable $object): string {
                        return $object->format('c');
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new DateTimeImmutable('2021-01-01 00:00:00+01:00'),
                    1 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                    2 => new DateTimeImmutable('2021-01-01 00:00:02+01:00'),
                    3 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                    4 => new DateTimeImmutable('2021-01-01 00:00:03+01:00'),
                    5 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                ];

                return [
                    'Ascending, use last encountered.',
                    [
                        0 => $elements[0],
                        2 => $elements[2],
                        4 => $elements[4],
                        5 => $elements[5],
                    ],
                    $elements,
                    static function (DateTimeImmutable $object): string {
                        return $object->format('c');
                    },
                    false,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new DateTimeImmutable('2021-01-01 00:00:03+01:00'),
                    1 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                    2 => new DateTimeImmutable('2021-01-01 00:00:02+01:00'),
                    3 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                    4 => new DateTimeImmutable('2021-01-01 00:00:00+01:00'),
                    5 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                ];

                return [
                    'Descending, use first encountered.',
                    [
                        0 => $elements[0],
                        1 => $elements[1],
                        2 => $elements[2],
                        4 => $elements[4],
                    ],
                    $elements,
                    static function (DateTimeImmutable $object): string {
                        return $object->format('c');
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new DateTimeImmutable('2021-01-01 00:00:03+01:00'),
                    1 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                    2 => new DateTimeImmutable('2021-01-01 00:00:02+01:00'),
                    3 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                    4 => new DateTimeImmutable('2021-01-01 00:00:00+01:00'),
                    5 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                ];

                return [
                    'Descending, use last encountered.',
                    [
                        0 => $elements[0],
                        2 => $elements[2],
                        4 => $elements[4],
                        5 => $elements[5],
                    ],
                    $elements,
                    static function (DateTimeImmutable $object): string {
                        return $object->format('c');
                    },
                    false,
                ];
            })(),
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @return array<int, array{string, DateTimeImmutableCollection<DateTimeImmutable>, DateTimeImmutableCollection<DateTimeImmutable>, Closure: void}>
     */
    public function dataProvider_testWithMergedWorks(): array
    {
        // @phpstan-ignore-next-line Returned values are 100% correct, but phpstan still reports an error. False positive?
        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                new DateTimeImmutableCollection([0 => new DateTimeImmutable()]),
                new DateTimeImmutableCollection([0 => new DateTimeImmutable()]),
                function (
                    DateTimeImmutableCollection $collectionA,
                    DateTimeImmutableCollection $collectionB,
                    DateTimeImmutableCollection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(2, $collectionC, $message);
                    $this->assertSame($collectionA->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->first(), $collectionC->last(), $message);
                },
            ],
            [
                'Same name string keys. Will override.',
                new DateTimeImmutableCollection(['foo' => new DateTimeImmutable()]),
                new DateTimeImmutableCollection(['foo' => new DateTimeImmutable()]),
                function (
                    DateTimeImmutableCollection $collectionA,
                    DateTimeImmutableCollection $collectionB,
                    DateTimeImmutableCollection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(1, $collectionC, $message);
                    $this->assertSame(['foo'], $collectionC->getKeys(), $message);
                    $this->assertNotSame($collectionA->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->last(), $collectionC->last(), $message);
                },
            ],
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @return array<DateTimeImmutable>
     */
    protected function createMultipleElements(): array
    {
        return [
            new DateTimeImmutable(),
            'foo' => new DateTimeImmutable(),
            42 => new DateTimeImmutable(),
            new DateTimeImmutable(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function createSingleElement(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    /**
     * {@inheritDoc}
     */
    protected function getHandledCollectionClassName(): string
    {
        return DateTimeImmutableCollection::class;
    }
}
