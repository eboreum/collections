<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use Closure;
use Eboreum\Collections\FloatCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

use function strval;

/**
 * @template T of float
 * @template TCollection of FloatCollection<T>
 * @extends AbstractTypeCollectionTestCase<T, TCollection>
 */
#[CoversClass(FloatCollection::class)]
class FloatCollectionTest extends AbstractTypeCollectionTestCase
{
    /**
     * @return array<int, array{float|null, array<float>}>
     */
    public static function providerTestMaxWorks(): array
    {
        return [
            [
                null,
                [],
            ],
            [
                3.14,
                [3.14],
            ],
            [
                3.15,
                [
                    3.15,
                    3.14,
                ],
            ],
            [
                3.14,
                [
                    3.14,
                    3.13,
                    3.12,
                    3.14,
                    3.12,
                ],
            ],
            [
                3.14,
                [
                    3.13,
                    3.12,
                    3.14,
                ],
            ],
        ];
    }

    /**
     * @return array<int, array{float|null, array<float>}>
     */
    public static function providerTestMinWorks(): array
    {
        return [
            [
                null,
                [],
            ],
            [
                3.14,
                [3.14],
            ],
            [
                3.14,
                [
                    3.15,
                    3.14,
                ],
            ],
            [
                3.14,
                [
                    3.16,
                    3.15,
                    3.14,
                    3.16,
                    3.14,
                ],
            ],
            [
                3.14,
                [
                    3.15,
                    3.14,
                    3.16,
                ],
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
                    $elements = [3.1415];

                    return [
                        $elements,
                        $elements,
                    ];
                },
                static function (): string {
                    return '';
                },
                true,
            ],
            [
                'Ascending, use first encountered.',
                static function (): array {
                    /** @var array<int, T> $expected */
                    $expected = [0 => 0.0, 1 => 3.1415, 3 => -1.0, 5 => 2.7183];

                    /** @var array<int, T> $elements */
                    $elements = [0.0, 3.1415, 0.0, -1.0, 0.0, 2.7183];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (float $value): string {
                    return strval($value);
                },
                true,
            ],
            [
                'Ascending, use last encountered.',
                static function (): array {
                    /** @var array<int, T> $expected */
                    $expected = [1 => 3.1415, 3 => -1.0, 4 => 0.0, 5 => 2.7183];

                    /** @var array<int, T> $elements */
                    $elements = [0.0, 3.1415, 0.0, -1.0, 0.0, 2.7183];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (float $value): string {
                    return strval($value);
                },
                false,
            ],
            [
                'Descending, use first encountered.',
                static function (): array {
                    /** @var array<int, T> $expected */
                    $expected = [0 => 2.7183, 1 => 0.0, 2 => -1.0, 4 => 3.1415];

                    /** @var array<int, T> $elements */
                    $elements = [2.7183, 0.0, -1.0, 0.0, 3.1415, 0.0];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (float $value): string {
                    return strval($value);
                },
                true,
            ],
            [
                'Descending, use last encountered.',
                static function (): array {
                    /** @var array<int, T> $expected */
                    $expected = [0 => 2.7183, 2 => -1.0, 4 => 3.1415, 5 => 0.0];

                    /** @var array<int, T> $elements */
                    $elements = [2.7183, 0.0, -1.0, 0.0, 3.1415, 0.0];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (float $value): string {
                    return strval($value);
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
        $a0 = new FloatCollection([0 => 3.1415]);

        /** @var TCollection<T> $b0 */
        $b0 = new FloatCollection([0 => 2.7182]);

        /** @var TCollection<T> $aAssociative */
        $aAssociative = new FloatCollection(['foo' => 3.1415]);

        /** @var TCollection<T> $bAssociative */
        $bAssociative = new FloatCollection(['foo' => 2.7182]);

        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                $a0,
                $b0,
                static function (
                    self $self,
                    FloatCollection $collectionA,
                    FloatCollection $collectionB,
                    FloatCollection $collectionC,
                    string $message
                ): void {
                    $self->assertCount(2, $collectionC, $message);
                    $self->assertSame([0 => 3.1415, 1 => 2.7182], $collectionC->toArray(), $message);
                },
            ],
            [
                'Same name string keys. Will override.',
                $aAssociative,
                $bAssociative,
                static function (
                    self $self,
                    FloatCollection $collectionA,
                    FloatCollection $collectionB,
                    FloatCollection $collectionC,
                    string $message
                ): void {
                    $self->assertCount(1, $collectionC, $message);
                    $self->assertSame(['foo' => 2.7182], $collectionC->toArray(), $message);
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
            0 => -0.9999,
            'foo' => 3.1415,
            42 => 2.7182,
            43 => -7.7,
        ];

        return $elements;
    }

    protected static function createSingleElement(AbstractCollectionTestCase $self): float
    {
        return 3.1415;
    }

    protected static function getHandledCollectionClassName(): string
    {
        return FloatCollection::class;
    }

    /**
     * @param array<int, float> $elements
     */
    #[DataProvider('providerTestMaxWorks')]
    public function testMaxWorks(?float $expected, array $elements): void
    {
        $floatCollection = new FloatCollection($elements);
        $element = $floatCollection->max();

        $this->assertSame($expected, $element);
    }

    /**
     * @param array<int, float> $elements
     */
    #[DataProvider('providerTestMinWorks')]
    public function testMinWorks(?float $expected, array $elements): void
    {
        $floatCollection = new FloatCollection($elements);
        $element = $floatCollection->min();

        $this->assertSame($expected, $element);
    }

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

    #[DataProvider('providerTestToUniqueByCallbackWorks')]
    public function testToUniqueWorks(
        string $message,
        Closure $elementsFactory,
        Closure $callback,
        bool $isUsingFirstEncounteredElement,
    ): void {
        $data = $elementsFactory($this);

        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertArrayHasKey(0, $data);
        $this->assertArrayHasKey(1, $data);

        /**
         * @var array<mixed> $expected
         * @var array<mixed> $elements
         */
        [$expected, $elements] = $data;

        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collectionA = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(FloatCollection::class, $collectionA);

        $collectionB = $collectionA->toUnique($isUsingFirstEncounteredElement);

        $this->assertInstanceOf(FloatCollection::class, $collectionB);

        $this->assertNotSame($collectionA, $collectionB, $message);
        $this->assertSame($elements, $collectionA->toArray(), $message);
        $this->assertSame($expected, $collectionB->toArray(), $message);
    }
}
