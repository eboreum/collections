<?php

declare(strict_types = 1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Collection;
use Eboreum\Collections\Exception\InvalidArgumentException;
use Eboreum\Collections\Exception\RuntimeException;
use Eboreum\Collections\Object_\DateTimeCollection;
use Test\Unit\Eboreum\Collections\AbstractTypeCollectionTestCase;

class DateTimeCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
{
    public function testToSortedWorks(): void
    {
        $elements = [
            new \DateTime('2021-02-01 12:34:57'),
            new \DateTime('2021-02-01 12:34:56'),
            new \DateTime('2021-02-01 12:34:55'),
            new \DateTime('2021-01-01 12:34:57'),
            new \DateTime('2021-03-01 12:34:55'),
        ];

        $dateTimeCollectionA = new DateTimeCollection($elements);
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
            new \DateTime('2021-02-01 12:34:57'),
            new \DateTime('2021-02-01 12:34:56'),
            new \DateTime('2021-02-01 12:34:55'),
            new \DateTime('2021-01-01 12:34:57'),
            new \DateTime('2021-03-01 12:34:55'),
            new \DateTime('2021-02-01 12:34:55'),
            new \DateTime('2021-02-01 12:34:57'),
        ];

        $dateTimeCollectionA = new DateTimeCollection($elements);
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
                "Empty collection.",
                [],
                [],
                function(){
                    return "";
                },
                true,
            ],
            (function(){
                $elements = [
                    0 => new \DateTime("2021-01-01 00:00:00+01:00"),
                ];

                return [
                    "1 single item collection.",
                    $elements,
                    $elements,
                    function(\DateTime $object){
                        return $object->format("c");
                    },
                    true,
                ];
            })(),
            (function(){
                $elements = [
                    0 => new \DateTime("2021-01-01 00:00:00+01:00"),
                    1 => new \DateTime("2021-01-01 00:00:01+01:00"),
                    2 => new \DateTime("2021-01-01 00:00:02+01:00"),
                    3 => new \DateTime("2021-01-01 00:00:01+01:00"),
                    4 => new \DateTime("2021-01-01 00:00:03+01:00"),
                    5 => new \DateTime("2021-01-01 00:00:01+01:00"),
                ];

                return [
                    "Ascending, use first encountered.",
                    [
                        0 => $elements[0],
                        1 => $elements[1],
                        2 => $elements[2],
                        4 => $elements[4],
                    ],
                    $elements,
                    function(\DateTime $object){
                        return $object->format("c");
                    },
                    true,
                ];
            })(),
            (function(){
                $elements = [
                    0 => new \DateTime("2021-01-01 00:00:00+01:00"),
                    1 => new \DateTime("2021-01-01 00:00:01+01:00"),
                    2 => new \DateTime("2021-01-01 00:00:02+01:00"),
                    3 => new \DateTime("2021-01-01 00:00:01+01:00"),
                    4 => new \DateTime("2021-01-01 00:00:03+01:00"),
                    5 => new \DateTime("2021-01-01 00:00:01+01:00"),
                ];

                return [
                    "Ascending, use last encountered.",
                    [
                        0 => $elements[0],
                        2 => $elements[2],
                        4 => $elements[4],
                        5 => $elements[5],
                    ],
                    $elements,
                    function(\DateTime $object){
                        return $object->format("c");
                    },
                    false,
                ];
            })(),
            (function(){
                $elements = [
                    0 => new \DateTime("2021-01-01 00:00:03+01:00"),
                    1 => new \DateTime("2021-01-01 00:00:01+01:00"),
                    2 => new \DateTime("2021-01-01 00:00:02+01:00"),
                    3 => new \DateTime("2021-01-01 00:00:01+01:00"),
                    4 => new \DateTime("2021-01-01 00:00:00+01:00"),
                    5 => new \DateTime("2021-01-01 00:00:01+01:00"),
                ];

                return [
                    "Descending, use first encountered.",
                    [
                        0 => $elements[0],
                        1 => $elements[1],
                        2 => $elements[2],
                        4 => $elements[4],
                    ],
                    $elements,
                    function(\DateTime $object){
                        return $object->format("c");
                    },
                    true,
                ];
            })(),
            (function(){
                $elements = [
                    0 => new \DateTime("2021-01-01 00:00:03+01:00"),
                    1 => new \DateTime("2021-01-01 00:00:01+01:00"),
                    2 => new \DateTime("2021-01-01 00:00:02+01:00"),
                    3 => new \DateTime("2021-01-01 00:00:01+01:00"),
                    4 => new \DateTime("2021-01-01 00:00:00+01:00"),
                    5 => new \DateTime("2021-01-01 00:00:01+01:00"),
                ];

                return [
                    "Descending, use last encountered.",
                    [
                        0 => $elements[0],
                        2 => $elements[2],
                        4 => $elements[4],
                        5 => $elements[5],
                    ],
                    $elements,
                    function(\DateTime $object){
                        return $object->format("c");
                    },
                    false,
                ];
            })(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function dataProvider_testWithMergedWorks(): array
    {
        return [
            [
                "Integer keys. 0 in both, means #2 is appended as key 1.",
                new DateTimeCollection([0 => new \DateTime]),
                new DateTimeCollection([0 => new \DateTime]),
                function(
                    DateTimeCollection $collectionA,
                    DateTimeCollection $collectionB,
                    DateTimeCollection $collectionC,
                    string $message
                ){
                    $this->assertCount(2, $collectionC, $message);
                    $this->assertSame([0, 1], $collectionC->getKeys(), $message);
                    $this->assertSame($collectionA->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->first(), $collectionC->last(), $message);
                },
            ],
            [
                "Same name string keys. Will override.",
                new DateTimeCollection(["foo" => new \DateTime]),
                new DateTimeCollection(["foo" => new \DateTime]),
                function(
                    DateTimeCollection $collectionA,
                    DateTimeCollection $collectionB,
                    DateTimeCollection $collectionC,
                    string $message
                ){
                    $this->assertCount(1, $collectionC, $message);
                    $this->assertSame(["foo"], $collectionC->getKeys(), $message);
                    $this->assertNotSame($collectionA->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->last(), $collectionC->last(), $message);
                },
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getHandledCollectionClassName(): string
    {
        return DateTimeCollection::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getSingleElement()
    {
        return new \DateTime;
    }

    /**
     * {@inheritDoc}
     */
    protected function getMultipleElements(): array
    {
        return [
            new \DateTime,
            "foo" => new \DateTime,
            42 => new \DateTime,
            new \DateTime,
        ];
    }
}
