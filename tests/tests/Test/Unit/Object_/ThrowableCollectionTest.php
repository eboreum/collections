<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Eboreum\Collections\Object_\ThrowableCollection;
use Error;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use Test\Unit\Eboreum\Collections\AbstractCollectionTestCase;
use Throwable;

/**
 * @template T of Throwable
 * @template TCollection of ThrowableCollection<T>
 * @extends AbstractNamedClassOrInterfaceCollectionTestCase<T, TCollection>
 */
#[CoversClass(ThrowableCollection::class)]
class ThrowableCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
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
                        0 => new Exception('foo'),
                    ];

                    return [
                        $elements,
                        $elements,
                    ];
                },
                static function (Throwable $object): string {
                    return $object->getMessage();
                },
                true,
            ],
            [
                'Ascending, use first encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new Exception('A'),
                        1 => new Error('B'),
                        2 => new Error('C'),
                        3 => new Exception('B'),
                        4 => new Exception('D'),
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
                static function (Throwable $object): string {
                    return $object->getMessage();
                },
                true,
            ],
            [
                'Ascending, use last encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new Exception('A'),
                        1 => new Error('B'),
                        2 => new Error('C'),
                        3 => new Exception('B'),
                        4 => new Exception('D'),
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
                static function (Throwable $object): string {
                    return $object->getMessage();
                },
                false,
            ],
            [
                'Descending, use first encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new Exception('D'),
                        1 => new Error('B'),
                        2 => new Error('C'),
                        3 => new Exception('B'),
                        4 => new Exception('A'),
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
                static function (Throwable $object): string {
                    return $object->getMessage();
                },
                true,
            ],
            [
                'Descending, use last encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new Exception('D'),
                        1 => new Error('B'),
                        2 => new Error('C'),
                        3 => new Exception('B'),
                        4 => new Exception('A'),
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
                static function (Throwable $object): string {
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
        $a0 = new ThrowableCollection([0 => new Exception()]);

        /** @var TCollection<T> $b0 */
        $b0 = new ThrowableCollection([0 => new Error()]);

        /** @var TCollection<T> $aAssociative */
        $aAssociative = new ThrowableCollection(['foo' => new Exception()]);

        /** @var TCollection<T> $bAssociative */
        $bAssociative = new ThrowableCollection(['foo' => new Error()]);

        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                $a0,
                $b0,
                static function (
                    self $self,
                    ThrowableCollection $collectionA,
                    ThrowableCollection $collectionB,
                    ThrowableCollection $collectionC,
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
                    ThrowableCollection $collectionA,
                    ThrowableCollection $collectionB,
                    ThrowableCollection $collectionC,
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
     * @return array<Throwable>
     */
    protected static function createMultipleElements(AbstractCollectionTestCase $self): array
    {
        return [
            0 => new Exception(),
            'foo' => new Error(),
            42 => new Error(),
            43 => new Exception(),
        ];
    }

    protected static function createSingleElement(AbstractCollectionTestCase $self): Throwable
    {
        return new Exception();
    }

    /**
     * @return class-string<TCollection<T>>
     */
    protected static function getHandledCollectionClassName(): string
    {
        return ThrowableCollection::class;
    }
}
