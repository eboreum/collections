<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Eboreum\Collections\Object_\SplFileInfoCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use SplFileInfo;
use Test\Unit\Eboreum\Collections\AbstractCollectionTestCase;

use function spl_object_hash;
use function sprintf;

/**
 * @template T of SplFileInfo
 * @template TCollection of SplFileInfoCollection<T>
 * @extends AbstractNamedClassOrInterfaceCollectionTestCase<T, TCollection>
 */
#[CoversClass(SplFileInfoCollection::class)]
class SplFileInfoCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
{
    /**
     * {@inheritDoc}
     */
    public static function providerTestToUniqueByCallbackWorks(): array
    {
        $basePath = sprintf(
            '%s/resources/TestResource/Unit/Object_/SplFileInfoCollectionTest/providerTestToUniqueByCallbackWorks',
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
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new SplFileInfo(__FILE__),
                    ];

                    return [
                        $elements,
                        $elements,
                    ];
                },
                static function (SplFileInfo $splFileInfo): string {
                    return spl_object_hash($splFileInfo);
                },
                true,
            ],
            [
                'Ascending, use first encountered.',
                static function () use ($basePath): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new SplFileInfo($basePath . '/A.txt'),
                        1 => new SplFileInfo($basePath . '/B.txt'),
                        2 => new SplFileInfo($basePath . '/C.txt'),
                        3 => new SplFileInfo($basePath . '/B.txt'),
                        4 => new SplFileInfo($basePath . '/D.txt'),
                        5 => new SplFileInfo($basePath . '/B.txt'),
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
                static function (SplFileInfo $object): string {
                    return $object->getFilename();
                },
                true,
            ],
            [
                'Ascending, use last encountered.',
                static function () use ($basePath): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new SplFileInfo($basePath . '/A.txt'),
                        1 => new SplFileInfo($basePath . '/B.txt'),
                        2 => new SplFileInfo($basePath . '/C.txt'),
                        3 => new SplFileInfo($basePath . '/B.txt'),
                        4 => new SplFileInfo($basePath . '/D.txt'),
                        5 => new SplFileInfo($basePath . '/B.txt'),
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
                static function (SplFileInfo $object): string {
                    return $object->getFilename();
                },
                false,
            ],
            [
                'Descending, use first encountered.',
                static function () use ($basePath): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new SplFileInfo($basePath . '/D.txt'),
                        1 => new SplFileInfo($basePath . '/B.txt'),
                        2 => new SplFileInfo($basePath . '/C.txt'),
                        3 => new SplFileInfo($basePath . '/B.txt'),
                        4 => new SplFileInfo($basePath . '/A.txt'),
                        5 => new SplFileInfo($basePath . '/B.txt'),
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
                static function (SplFileInfo $object): string {
                    return $object->getFilename();
                },
                true,
            ],
            [
                'Descending, use last encountered.',
                static function () use ($basePath): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new SplFileInfo($basePath . '/D.txt'),
                        1 => new SplFileInfo($basePath . '/B.txt'),
                        2 => new SplFileInfo($basePath . '/C.txt'),
                        3 => new SplFileInfo($basePath . '/B.txt'),
                        4 => new SplFileInfo($basePath . '/A.txt'),
                        5 => new SplFileInfo($basePath . '/B.txt'),
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
                static function (SplFileInfo $object): string {
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
        $a0 = new SplFileInfoCollection([0 => new SplFileInfo(__FILE__)]);

        /** @var TCollection<T> $b0 */
        $b0 = new SplFileInfoCollection([0 => new SplFileInfo(__FILE__)]);

        /** @var TCollection<T> $aAssociative */
        $aAssociative = new SplFileInfoCollection(['foo' => new SplFileInfo(__FILE__)]);

        /** @var TCollection<T> $bAssociative */
        $bAssociative = new SplFileInfoCollection(['foo' => new SplFileInfo(__FILE__)]);

        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                $a0,
                $b0,
                static function (
                    self $self,
                    SplFileInfoCollection $collectionA,
                    SplFileInfoCollection $collectionB,
                    SplFileInfoCollection $collectionC,
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
                    SplFileInfoCollection $collectionA,
                    SplFileInfoCollection $collectionB,
                    SplFileInfoCollection $collectionC,
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
     * @return array<SplFileInfo>
     */
    protected static function createMultipleElements(AbstractCollectionTestCase $self): array
    {
        return [
            0 => new SplFileInfo(__FILE__),
            'foo' => new SplFileInfo(__FILE__),
            42 => new SplFileInfo(__FILE__),
            43 => new SplFileInfo(__FILE__),
        ];
    }

    protected static function createSingleElement(AbstractCollectionTestCase $self): SplFileInfo
    {
        return new SplFileInfo(__FILE__);
    }

    /**
     * @return class-string<TCollection<T>>
     */
    protected static function getHandledCollectionClassName(): string
    {
        return SplFileInfoCollection::class;
    }
}
