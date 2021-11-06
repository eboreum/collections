<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use Eboreum\Collections\FloatCollection;

class FloatCollectionTest extends AbstractTypeCollectionTestCase
{
    public function testToSortedWorks(): void
    {
        $elements = [
            7.2,
            6.1,
            -5.9,
            2.0,
            6.1,
        ];

        $floatCollectionA = new FloatCollection($elements);
        $floatCollectionB = $floatCollectionA->toSorted();

        $this->assertNotSame($floatCollectionA, $floatCollectionB);
        $this->assertSame($elements, $floatCollectionA->toArray());
        $this->assertSame(
            [
                2 => $elements[2],
                3 => $elements[3],
                1 => $elements[1],
                4 => $elements[4],
                0 => $elements[0],
            ],
            $floatCollectionB->toArray(),
        );
    }

    /**
     * @dataProvider dataProvider_testToUniqueByCallbackWorks
     *
     * @param array<int, float> $expected
     * @param array<int, float> $elements
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
                [3.1415],
                [3.1415],
                static function (): string {
                    return '';
                },
                true,
            ],
            [
                'Ascending, use first encountered.',
                [0 => 0.0, 1 => 3.1415, 3 => -1.0, 5 => 2.7183],
                [0.0,3.1415,0.0,-1.0,0.0,2.7183],
                static function (float $value): string {
                    return strval($value);
                },
                true,
            ],
            [
                'Ascending, use last encountered.',
                [1 => 3.1415, 3 => -1.0, 4 => 0.0, 5 => 2.7183],
                [0.0,3.1415,0.0,-1.0,0.0,2.7183],
                static function (float $value): string {
                    return strval($value);
                },
                false,
            ],
            [
                'Descending, use first encountered.',
                [0 => 2.7183, 1 => 0.0, 2 => -1.0, 4 => 3.1415],
                [2.7183,0.0,-1.0,0.0,3.1415,0.0],
                static function (float $value): string {
                    return strval($value);
                },
                true,
            ],
            [
                'Descending, use last encountered.',
                [0 => 2.7183, 2 => -1.0, 4 => 3.1415, 5 => 0.0],
                [2.7183,0.0,-1.0,0.0,3.1415,0.0],
                static function (float $value): string {
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
                new FloatCollection([0 => 3.1415]),
                new FloatCollection([0 => 2.7182]),
                function (
                    FloatCollection $collectionA,
                    FloatCollection $collectionB,
                    FloatCollection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(2, $collectionC, $message);
                    $this->assertSame([0 => 3.1415, 1 => 2.7182], $collectionC->toArray(), $message);
                },
            ],
            [
                'Same name string keys. Will override.',
                new FloatCollection(['foo' => 3.1415]),
                new FloatCollection(['foo' => 2.7182]),
                function (
                    FloatCollection $collectionA,
                    FloatCollection $collectionB,
                    FloatCollection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(1, $collectionC, $message);
                    $this->assertSame(['foo' => 2.7182], $collectionC->toArray(), $message);
                },
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getHandledCollectionClassName(): string
    {
        return FloatCollection::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getSingleElement()
    {
        return 3.1415;
    }

    /**
     * {@inheritDoc}
     */
    protected function getMultipleElements(): array
    {
        return [
            -0.9999,
            'foo' => 3.1415,
            42 => 2.7182,
            -7.7,
        ];
    }
}
