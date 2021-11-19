<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use Eboreum\Collections\IntegerCollection;

class IntegerCollectionTest extends AbstractTypeCollectionTestCase
{
    /**
     * @dataProvider dataProvider_testMaxWorks
     *
     * @param array<int, int> $elements
     */
    public function testMaxWorks(?int $expected, array $elements): void
    {
        $integerCollection = new IntegerCollection($elements);
        $element = $integerCollection->max();

        $this->assertSame($expected, $element);
    }

    public function dataProvider_testMaxWorks(): array
    {
        return [
            [
                null,
                [],
            ],
            (function(){
                $elements = [42];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (function(){
                $elements = [
                    43,
                    42,
                ];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (function(){
                $elements = [
                    42,
                    41,
                    40,
                    42,
                    40,
                ];

                return [
                    $elements[3],
                    $elements,
                ];
            })(),
            (function(){
                $elements = [
                    41,
                    40,
                    42,
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
     * @param array<int, int> $elements
     */
    public function testMinWorks(?int $expected, array $elements): void
    {
        $integerCollection = new IntegerCollection($elements);
        $element = $integerCollection->min();

        $this->assertSame($expected, $element);
    }

    public function dataProvider_testMinWorks(): array
    {
        return [
            [
                null,
                [],
            ],
            (function(){
                $elements = [42];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (function(){
                $elements = [
                    43,
                    42,
                ];

                return [
                    $elements[1],
                    $elements,
                ];
            })(),
            (function(){
                $elements = [
                    44,
                    43,
                    42,
                    44,
                    42,
                ];

                return [
                    $elements[2],
                    $elements,
                ];
            })(),
            (function(){
                $elements = [
                    43,
                    42,
                    44,
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
            7,
            6,
            -5,
            2,
            6,
        ];

        $integerCollectionA = new IntegerCollection($elements);
        $integerCollectionB = $integerCollectionA->toSorted();

        $this->assertNotSame($integerCollectionA, $integerCollectionB);
        $this->assertSame($elements, $integerCollectionA->toArray());
        $this->assertSame(
            [
                2 => $elements[2],
                3 => $elements[3],
                1 => $elements[1],
                4 => $elements[4],
                0 => $elements[0],
            ],
            $integerCollectionB->toArray(),
        );
    }

    /**
     * @dataProvider dataProvider_testToUniqueByCallbackWorks
     *
     * @param array<int, int> $expected
     * @param array<int, int> $elements
     */
    public function testToUniqueWorks(
        string $message,
        array $expected,
        array $elements,
        \Closure $callback,
        bool $isUsingFirstEncounteredElement
    ): void {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $collectionA = new $handledCollectionClassName($elements);

        $collectionB = $collectionA->toUnique($isUsingFirstEncounteredElement);

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elements, $collectionA->toArray());
        $this->assertSame($expected, $collectionB->toArray());
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
            [
                '1 single item collection.',
                [$this->getSingleElement()],
                [$this->getSingleElement()],
                static function (): string {
                    return '';
                },
                true,
            ],
            [
                'Integer item collection, ascending, use first encountered.',
                [0 => 1, 1 => 2, 3 => 3, 5 => 4],
                [1, 2, 1, 3, 1, 4],
                static function (int $value): string {
                    return strval($value);
                },
                true,
            ],
            [
                'Integer item collection, ascending, use last encountered.',
                [1 => 2, 3 => 3, 4 => 1, 5 => 4],
                [1, 2, 1, 3, 1, 4],
                static function (int $value): string {
                    return strval($value);
                },
                false,
            ],
            [
                'Integer item collection, descending, use first encountered.',
                [0 => 4, 1 => 1, 2 => 3, 4 => 2],
                [4, 1, 3, 1, 2, 1],
                static function (int $value): string {
                    return strval($value);
                },
                true,
            ],
            [
                'Integer item collection, descending, use last encountered.',
                [0 => 4, 2 => 3, 4 => 2, 5 => 1],
                [4, 1, 3, 1, 2, 1],
                static function (int $value): string {
                    return strval($value);
                },
                false,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function dataProvider_testWithMergedWorks(): array
    {
        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                new IntegerCollection([0 => 999]),
                new IntegerCollection([0 => -999]),
                function (
                    IntegerCollection $collectionA,
                    IntegerCollection $collectionB,
                    IntegerCollection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(2, $collectionC, $message);
                    $this->assertSame([0 => 999, 1 => -999], $collectionC->toArray(), $message);
                },
            ],
            [
                'Same name string keys. Will override.',
                new IntegerCollection(['foo' => 999]),
                new IntegerCollection(['foo' => -999]),
                function (
                    IntegerCollection $collectionA,
                    IntegerCollection $collectionB,
                    IntegerCollection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(1, $collectionC, $message);
                    $this->assertSame(['foo' => -999], $collectionC->toArray(), $message);
                },
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getHandledCollectionClassName(): string
    {
        return IntegerCollection::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getSingleElement()
    {
        return 42;
    }

    /**
     * {@inheritDoc}
     */
    protected function getMultipleElements(): array
    {
        return [
            -1,
            'foo' => 2,
            42 => -3,
            42,
        ];
    }
}
