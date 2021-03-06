<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Eboreum\Collections\Object_\DateTimeInterfaceCollection;

class DateTimeInterfaceCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
{
    /**
     * @dataProvider dataProvider_testMaxWorks
     *
     * @param array<int, DateTimeInterface> $elements
     */
    public function testMaxWorks(?DateTimeInterface $expected, array $elements): void
    {
        $dateTimeCollection = new DateTimeInterfaceCollection($elements);
        $element = $dateTimeCollection->max();

        $this->assertSame($expected, $element);
    }

    /**
     * @return array<int, array{DateTimeInterface|null, array<DateTimeInterface>}>
     */
    public function dataProvider_testMaxWorks(): array
    {
        return [
            [
                null,
                [],
            ],
            (function(){
                $elements = [new DateTime('2021-02-01 12:34:57')];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (function(){
                $elements = [
                    new DateTime('2021-02-01 12:34:57'),
                    new DateTimeImmutable('2021-02-01 12:34:56'),
                ];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (function(){
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
            (function(){
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
     * @dataProvider dataProvider_testMinWorks
     *
     * @param array<int, DateTimeInterface> $elements
     */
    public function testMinWorks(?DateTimeInterface $expected, array $elements): void
    {
        $dateTimeCollection = new DateTimeInterfaceCollection($elements);
        $element = $dateTimeCollection->min();

        $this->assertSame($expected, $element);
    }

    /**
     * @return array<int, array{DateTimeInterface|null, array<DateTimeInterface>}>
     */
    public function dataProvider_testMinWorks(): array
    {
        return [
            [
                null,
                [],
            ],
            (function(){
                $elements = [new DateTime('2021-02-01 12:34:57')];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (function(){
                $elements = [
                    new DateTime('2021-02-01 12:34:57'),
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
            (function(){
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

    public function testToSortedWorks(): void
    {
        $elements = [
            new DateTime('2021-02-01 12:34:57'),
            new DateTimeImmutable('2021-02-01 12:34:56'),
            new DateTime('2021-02-01 12:34:55'),
            new DateTime('2021-01-01 12:34:57'),
            new DateTimeImmutable('2021-03-01 12:34:55'),
        ];

        $dateTimeCollectionA = new DateTimeInterfaceCollection($elements);
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

    public function testToUniqueWorks(): void
    {
        $elements = [
            new DateTime('2021-02-01 12:34:57'),
            new DateTimeImmutable('2021-02-01 12:34:56'),
            new DateTime('2021-02-01 12:34:55'),
            new DateTime('2021-01-01 12:34:57'),
            new DateTimeImmutable('2021-03-01 12:34:55'),
            new DateTime('2021-02-01 12:34:55'),
            new DateTimeImmutable('2021-02-01 12:34:57'),
        ];

        $dateTimeCollectionA = new DateTimeInterfaceCollection($elements);
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
                    0 => new DateTime('2021-01-01 00:00:00+01:00'),
                ];

                return [
                    '1 single item collection.',
                    $elements,
                    $elements,
                    static function (DateTimeInterface $object): string {
                        return $object->format('c');
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new DateTime('2021-01-01 00:00:00+01:00'),
                    1 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                    2 => new DateTime('2021-01-01 00:00:02+01:00'),
                    3 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                    4 => new DateTime('2021-01-01 00:00:03+01:00'),
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
                    static function (DateTimeInterface $object): string {
                        return $object->format('c');
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new DateTime('2021-01-01 00:00:00+01:00'),
                    1 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                    2 => new DateTime('2021-01-01 00:00:02+01:00'),
                    3 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                    4 => new DateTime('2021-01-01 00:00:03+01:00'),
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
                    static function (DateTimeInterface $object): string {
                        return $object->format('c');
                    },
                    false,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new DateTime('2021-01-01 00:00:03+01:00'),
                    1 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                    2 => new DateTime('2021-01-01 00:00:02+01:00'),
                    3 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                    4 => new DateTime('2021-01-01 00:00:00+01:00'),
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
                    static function (DateTimeInterface $object): string {
                        return $object->format('c');
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new DateTime('2021-01-01 00:00:03+01:00'),
                    1 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                    2 => new DateTime('2021-01-01 00:00:02+01:00'),
                    3 => new DateTimeImmutable('2021-01-01 00:00:01+01:00'),
                    4 => new DateTime('2021-01-01 00:00:00+01:00'),
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
                    static function (DateTimeInterface $object): string {
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
     * @return array<int, array{string, DateTimeInterfaceCollection<DateTimeInterface>, DateTimeInterfaceCollection<DateTimeInterface>, Closure: void}>
     */
    public function dataProvider_testWithMergedWorks(): array
    {
        // @phpstan-ignore-next-line Returned values are 100% correct, but phpstan still reports an error. False positive?
        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                new DateTimeInterfaceCollection([0 => new DateTime()]),
                new DateTimeInterfaceCollection([0 => new DateTimeImmutable()]),
                function (
                    DateTimeInterfaceCollection $collectionA,
                    DateTimeInterfaceCollection $collectionB,
                    DateTimeInterfaceCollection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(2, $collectionC, $message);
                    $this->assertSame($collectionA->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->first(), $collectionC->last(), $message);
                },
            ],
            [
                'Same name string keys. Will override.',
                new DateTimeInterfaceCollection(['foo' => new DateTime()]),
                new DateTimeInterfaceCollection(['foo' => new DateTimeImmutable()]),
                function (
                    DateTimeInterfaceCollection $collectionA,
                    DateTimeInterfaceCollection $collectionB,
                    DateTimeInterfaceCollection $collectionC,
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
     * @return array<DateTimeInterface>
     */
    protected function createMultipleElements(): array
    {
        return [
            new DateTime(),
            'foo' => new DateTimeImmutable(),
            42 => new DateTime(),
            new DateTimeImmutable(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function createSingleElement(): DateTimeInterface
    {
        return new DateTimeImmutable();
    }

    /**
     * {@inheritDoc}
     */
    protected function getHandledCollectionClassName(): string
    {
        return DateTimeInterfaceCollection::class;
    }
}
