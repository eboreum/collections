<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use Closure;
use Eboreum\Collections\IntegerCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

use function strval;

/**
 * @template T of int
 * @template TCollection of IntegerCollection<T>
 * @extends AbstractTypeCollectionTestCase<T, TCollection>
 */
#[CoversClass(IntegerCollection::class)]
class IntegerCollectionTest extends AbstractTypeCollectionTestCase
{
    /**
     * @return array<int, array{int|null, array<int>}>
     */
    public static function providerTestMaxWorks(): array
    {
        return [
            [
                null,
                [],
            ],
            (static function () {
                $elements = [42];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (static function () {
                $elements = [
                    43,
                    42,
                ];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (static function () {
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
            (static function () {
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
     * @return array<int, array{int|null, array<int>}>
     */
    public static function providerTestMinWorks(): array
    {
        return [
            [
                null,
                [],
            ],
            (static function () {
                $elements = [42];

                return [
                    $elements[0],
                    $elements,
                ];
            })(),
            (static function () {
                $elements = [
                    43,
                    42,
                ];

                return [
                    $elements[1],
                    $elements,
                ];
            })(),
            (static function () {
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
            (static function () {
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
                static function (AbstractCollectionTestCase $self): array {
                    /** @var array<T> $expected */
                    $expected = [self::createSingleElement($self)];

                    return [
                        $expected,
                        $expected,
                    ];
                },
                static function (): string {
                    return '';
                },
                true,
            ],
            [
                'Integer item collection, ascending, use first encountered.',
                static function (): array {
                    /** @var array<T> $expected */
                    $expected = [0 => 1, 1 => 2, 3 => 3, 5 => 4];

                    /** @var array<T> $elements */
                    $elements = [1, 2, 1, 3, 1, 4];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (int $value): string {
                    return strval($value);
                },
                true,
            ],
            [
                'Integer item collection, ascending, use last encountered.',
                static function (): array {
                    /** @var array<T> $expected */
                    $expected = [1 => 2, 3 => 3, 4 => 1, 5 => 4];

                    /** @var array<T> $elements */
                    $elements = [1, 2, 1, 3, 1, 4];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (int $value): string {
                    return strval($value);
                },
                false,
            ],
            [
                'Integer item collection, descending, use first encountered.',
                static function (): array {
                    /** @var array<T> $expected */
                    $expected = [0 => 4, 1 => 1, 2 => 3, 4 => 2];

                    /** @var array<T> $elements */
                    $elements = [4, 1, 3, 1, 2, 1];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (int $value): string {
                    return strval($value);
                },
                true,
            ],
            [
                'Integer item collection, descending, use last encountered.',
                static function (): array {
                    /** @var array<T> $expected */
                    $expected = [0 => 4, 2 => 3, 4 => 2, 5 => 1];

                    /** @var array<T> $elements */
                    $elements = [4, 1, 3, 1, 2, 1];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (int $value): string {
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
        $a0 = new IntegerCollection([0 => 999]);

        /** @var TCollection<T> $b0 */
        $b0 = new IntegerCollection([0 => -999]);

        /** @var TCollection<T> $aAssociative */
        $aAssociative = new IntegerCollection(['foo' => 999]);

        /** @var TCollection<T> $bAssociative */
        $bAssociative = new IntegerCollection(['foo' => -999]);

        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                $a0,
                $b0,
                static function (
                    self $self,
                    IntegerCollection $collectionA,
                    IntegerCollection $collectionB,
                    IntegerCollection $collectionC,
                    string $message
                ): void {
                    $self->assertCount(2, $collectionC, $message);
                    $self->assertSame([0 => 999, 1 => -999], $collectionC->toArray(), $message);
                },
            ],
            [
                'Same name string keys. Will override.',
                $aAssociative,
                $bAssociative,
                static function (
                    self $self,
                    IntegerCollection $collectionA,
                    IntegerCollection $collectionB,
                    IntegerCollection $collectionC,
                    string $message
                ): void {
                    $self->assertCount(1, $collectionC, $message);
                    $self->assertSame(['foo' => -999], $collectionC->toArray(), $message);
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
            0 => -1,
            'foo' => 2,
            42 => -3,
            43 => 42,
        ];

        return $elements;
    }

    protected static function createSingleElement(AbstractCollectionTestCase $self): int
    {
        return 42;
    }

    protected static function getHandledCollectionClassName(): string
    {
        return IntegerCollection::class;
    }

    /**
     * @param array<int, int> $elements
     */
    #[DataProvider('providerTestMaxWorks')]
    public function testMaxWorks(?int $expected, array $elements): void
    {
        $integerCollection = new IntegerCollection($elements);
        $element = $integerCollection->max();

        $this->assertSame($expected, $element);
    }

    /**
     * @param array<int, int> $elements
     */
    #[DataProvider('providerTestMinWorks')]
    public function testMinWorks(?int $expected, array $elements): void
    {
        $integerCollection = new IntegerCollection($elements);
        $element = $integerCollection->min();

        $this->assertSame($expected, $element);
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

    #[DataProvider('providerTestToUniqueByCallbackWorks')]
    public function testToUniqueWorks(
        string $message,
        Closure $elementsFactory,
        Closure $callback,
        bool $isUsingFirstEncounteredElement,
    ): void {
        [$expected, $elements] = $elementsFactory($this);

        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collectionA = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(IntegerCollection::class, $collectionA);

        $collectionB = $collectionA->toUnique($isUsingFirstEncounteredElement);

        $this->assertInstanceOf(IntegerCollection::class, $collectionB);
        $this->assertNotSame($collectionA, $collectionB, $message);
        $this->assertSame($elements, $collectionA->toArray(), $message);
        $this->assertSame($expected, $collectionB->toArray(), $message);
    }
}
