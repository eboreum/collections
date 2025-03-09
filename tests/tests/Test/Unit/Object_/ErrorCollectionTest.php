<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Eboreum\Collections\Object_\ErrorCollection;
use Error;
use PHPUnit\Framework\Attributes\CoversClass;
use Test\Unit\Eboreum\Collections\AbstractCollectionTestCase;

/**
 * @template T of Error
 * @template TCollection of ErrorCollection<T>
 * @extends AbstractNamedClassOrInterfaceCollectionTestCase<T, TCollection>
 */
#[CoversClass(ErrorCollection::class)]
class ErrorCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
{
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
                        0 => new Error('foo'),
                    ];

                    return [
                        $elements,
                        $elements,
                    ];
                },
                static function (Error $object): string {
                    return $object->getMessage();
                },
                true,
            ],
            [
                'Ascending, use first encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new Error('A'),
                        1 => new Error('B'),
                        2 => new Error('C'),
                        3 => new Error('B'),
                        4 => new Error('D'),
                        5 => new Error('B'),
                    ];

                    return [
                        [
                            0 => $elements[0],
                            1 => $elements[1],
                            2 => $elements[2],
                            4 => $elements[4],
                        ],
                        $elements,
                    ];
                },
                static function (Error $object): string {
                    return $object->getMessage();
                },
                true,
            ],
            [
                'Ascending, use last encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new Error('A'),
                        1 => new Error('B'),
                        2 => new Error('C'),
                        3 => new Error('B'),
                        4 => new Error('D'),
                        5 => new Error('B'),
                    ];

                    return [
                        [
                            0 => $elements[0],
                            2 => $elements[2],
                            4 => $elements[4],
                            5 => $elements[5],
                        ],
                        $elements,
                    ];
                },
                static function (Error $object): string {
                    return $object->getMessage();
                },
                false,
            ],
            [
                'Descending, use first encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new Error('D'),
                        1 => new Error('B'),
                        2 => new Error('C'),
                        3 => new Error('B'),
                        4 => new Error('A'),
                        5 => new Error('B'),
                    ];

                    return [
                        [
                            0 => $elements[0],
                            1 => $elements[1],
                            2 => $elements[2],
                            4 => $elements[4],
                        ],
                        $elements,
                    ];
                },
                static function (Error $object): string {
                    return $object->getMessage();
                },
                true,
            ],
            [
                'Descending, use last encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new Error('D'),
                        1 => new Error('B'),
                        2 => new Error('C'),
                        3 => new Error('B'),
                        4 => new Error('A'),
                        5 => new Error('B'),
                    ];

                    return [
                        [
                            0 => $elements[0],
                            2 => $elements[2],
                            4 => $elements[4],
                            5 => $elements[5],
                        ],
                        $elements,
                    ];
                },
                static function (Error $object): string {
                    return $object->getMessage();
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
     *   },
     * >
     */
    public static function providerTestWithMergedWorks(): array
    {
        /** @var TCollection<T> $a0 */
        $a0 = new ErrorCollection([0 => new Error()]);

        /** @var TCollection<T> $b0 */
        $b0 = new ErrorCollection([0 => new Error()]);

        /** @var TCollection<T> $aAssociative */
        $aAssociative = new ErrorCollection(['foo' => new Error()]);

        /** @var TCollection<T> $bAssociative */
        $bAssociative = new ErrorCollection(['foo' => new Error()]);

        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                $a0,
                $b0,
                static function (
                    self $self,
                    ErrorCollection $collectionA,
                    ErrorCollection $collectionB,
                    ErrorCollection $collectionC,
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
                    ErrorCollection $collectionA,
                    ErrorCollection $collectionB,
                    ErrorCollection $collectionC,
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
     *
     * @return array<Error>
     */
    protected static function createMultipleElements(AbstractCollectionTestCase $self): array
    {
        return [
            0 => new Error(),
            'foo' => new Error(),
            42 => new Error(),
            43 => new Error(),
        ];
    }

    protected static function createSingleElement(AbstractCollectionTestCase $self): Error
    {
        return new Error();
    }

    /**
     * @return class-string<TCollection<T>>
     */
    protected static function getHandledCollectionClassName(): string
    {
        return ErrorCollection::class;
    }
}
