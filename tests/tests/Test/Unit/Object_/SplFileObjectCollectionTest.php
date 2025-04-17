<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Eboreum\Collections\Object_\SplFileObjectCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use SplFileObject;
use Test\Unit\Eboreum\Collections\AbstractCollectionTestCase;

use function spl_object_hash;
use function sprintf;

/**
 * @template T of SplFileObject
 * @template TCollection of SplFileObjectCollection<T>
 * @extends AbstractNamedClassOrInterfaceCollectionTestCase<T, TCollection>
 */
#[CoversClass(SplFileObjectCollection::class)]
class SplFileObjectCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
{
    /**
     * {@inheritDoc}
     */
    public static function providerTestToUniqueByCallbackWorks(): array
    {
        $basePath = sprintf(
            '%s/resources/TestResource/Unit/Object_/SplFileObjectCollectionTest/providerTestToUniqueByCallbackWorks',
            TEST_ROOT_PATH,
        );

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
                    $elements = [
                        0 => new SplFileObject(__FILE__),
                    ];

                    return [
                        $elements,
                        $elements,
                    ];
                },
                static function (SplFileObject $splFileObject): string {
                    return spl_object_hash($splFileObject);
                },
                true,
            ],
            [
                'Ascending, use first encountered.',
                static function () use ($basePath): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => new SplFileObject($basePath . '/A.txt'),
                        1 => new SplFileObject($basePath . '/B.txt'),
                        2 => new SplFileObject($basePath . '/C.txt'),
                        3 => new SplFileObject($basePath . '/B.txt'),
                        4 => new SplFileObject($basePath . '/D.txt'),
                        5 => new SplFileObject($basePath . '/B.txt'),
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
                static function (SplFileObject $object): string {
                    return $object->getFilename();
                },
                true,
            ],
            [
                'Ascending, use last encountered.',
                static function () use ($basePath): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => new SplFileObject($basePath . '/A.txt'),
                        1 => new SplFileObject($basePath . '/B.txt'),
                        2 => new SplFileObject($basePath . '/C.txt'),
                        3 => new SplFileObject($basePath . '/B.txt'),
                        4 => new SplFileObject($basePath . '/D.txt'),
                        5 => new SplFileObject($basePath . '/B.txt'),
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
                static function (SplFileObject $object): string {
                    return $object->getFilename();
                },
                false,
            ],
            [
                'Descending, use first encountered.',
                static function () use ($basePath): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => new SplFileObject($basePath . '/D.txt'),
                        1 => new SplFileObject($basePath . '/B.txt'),
                        2 => new SplFileObject($basePath . '/C.txt'),
                        3 => new SplFileObject($basePath . '/B.txt'),
                        4 => new SplFileObject($basePath . '/A.txt'),
                        5 => new SplFileObject($basePath . '/B.txt'),
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
                static function (SplFileObject $object): string {
                    return $object->getFilename();
                },
                true,
            ],
            [
                'Descending, use last encountered.',
                static function () use ($basePath): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => new SplFileObject($basePath . '/D.txt'),
                        1 => new SplFileObject($basePath . '/B.txt'),
                        2 => new SplFileObject($basePath . '/C.txt'),
                        3 => new SplFileObject($basePath . '/B.txt'),
                        4 => new SplFileObject($basePath . '/A.txt'),
                        5 => new SplFileObject($basePath . '/B.txt'),
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
                static function (SplFileObject $object): string {
                    return $object->getFilename();
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
        $a0 = new SplFileObjectCollection([0 => new SplFileObject(__FILE__)]);

        /** @var TCollection<T> $b0 */
        $b0 = new SplFileObjectCollection([0 => new SplFileObject(__FILE__)]);

        /** @var TCollection<T> $aAssociative */
        $aAssociative = new SplFileObjectCollection(['foo' => new SplFileObject(__FILE__)]);

        /** @var TCollection<T> $bAssociative */
        $bAssociative = new SplFileObjectCollection(['foo' => new SplFileObject(__FILE__)]);

        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                $a0,
                $b0,
                static function (
                    self $self,
                    SplFileObjectCollection $collectionA,
                    SplFileObjectCollection $collectionB,
                    SplFileObjectCollection $collectionC,
                    string $message
                ): void {
                    $self->assertCount(2, $collectionC, $message);
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
                    SplFileObjectCollection $collectionA,
                    SplFileObjectCollection $collectionB,
                    SplFileObjectCollection $collectionC,
                    string $message
                ): void {
                    $self->assertCount(1, $collectionC, $message);
                    $self->assertSame(['foo'], $collectionC->getKeys(), $message);
                    $self->assertSame($collectionB->first(), $collectionC->first(), $message);
                    $self->assertSame($collectionB->last(), $collectionC->last(), $message);
                },
            ],
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @return array<SplFileObject>
     */
    protected static function createMultipleElements(AbstractCollectionTestCase $self): array
    {
        return [
            0 => new SplFileObject(__FILE__),
            'foo' => new SplFileObject(__FILE__),
            42 => new SplFileObject(__FILE__),
            43 => new SplFileObject(__FILE__),
        ];
    }

    protected static function createSingleElement(AbstractCollectionTestCase $self): SplFileObject
    {
        return new SplFileObject(__FILE__);
    }

    /**
     * @return class-string<TCollection<T>>
     */
    protected static function getHandledCollectionClassName(): string
    {
        return SplFileObjectCollection::class;
    }
}
