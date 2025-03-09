<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Directory;
use Eboreum\Collections\Object_\DirectoryCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use RuntimeException;
use Test\Unit\Eboreum\Collections\AbstractCollectionTestCase;

use function assert;
use function dir;
use function dirname;
use function is_dir;
use function is_object;
use function sprintf;

/**
 * @template T of Directory
 * @template TCollection of DirectoryCollection<T>
 * @extends AbstractNamedClassOrInterfaceCollectionTestCase<T, TCollection>
 */
#[CoversClass(DirectoryCollection::class)]
class DirectoryCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
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
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => dir(__DIR__),
                    ];

                    return [
                        $elements,
                        $elements,
                    ];
                },
                static function (Directory $object): string {
                    return $object->path;
                },
                true,
            ],
            [
                'Ascending, use first encountered.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => dir(dirname(__DIR__, 1)),
                        1 => dir(__DIR__),
                        2 => dir(dirname(__DIR__, 2)),
                        3 => dir(__DIR__),
                        4 => dir(dirname(__DIR__, 3)),
                        5 => dir(__DIR__),
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
                static function (Directory $object): string {
                    return $object->path;
                },
                true,
            ],
            [
                'Ascending, use last encountered.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => dir(dirname(__DIR__, 1)),
                        1 => dir(__DIR__),
                        2 => dir(dirname(__DIR__, 2)),
                        3 => dir(__DIR__),
                        4 => dir(dirname(__DIR__, 3)),
                        5 => dir(__DIR__),
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
                static function (Directory $object): string {
                    return $object->path;
                },
                false,
            ],
            [
                'Descending, use first encountered.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => dir(dirname(__DIR__, 3)),
                        1 => dir(__DIR__),
                        2 => dir(dirname(__DIR__, 2)),
                        3 => dir(__DIR__),
                        4 => dir(dirname(__DIR__, 1)),
                        5 => dir(__DIR__),
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
                static function (Directory $object): string {
                    return $object->path;
                },
                true,
            ],
            [
                'Descending, use last encountered.',
                static function (): array {
                    /** @var array<int, T> $elements */
                    $elements = [
                        0 => dir(dirname(__DIR__, 3)),
                        1 => dir(__DIR__),
                        2 => dir(dirname(__DIR__, 2)),
                        3 => dir(__DIR__),
                        4 => dir(dirname(__DIR__, 1)),
                        5 => dir(__DIR__),
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
                static function (Directory $object): string {
                    return $object->path;
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
        $a0 = (static function (): DirectoryCollection {
            $directory = dir(__DIR__);

            static::assertInstanceOf(Directory::class, $directory);

            return new DirectoryCollection([0 => $directory]);
        })();

        /** @var TCollection<T> $b0 */
        $b0 = (static function (): DirectoryCollection {
            $directory = dir(dirname(__DIR__));

            static::assertInstanceOf(Directory::class, $directory);

            return new DirectoryCollection([0 => $directory]);
        })();

        /** @var TCollection<T> $aAssociative */
        $aAssociative = (static function (): DirectoryCollection {
            $directory = dir(__DIR__);

            static::assertInstanceOf(Directory::class, $directory);

            return new DirectoryCollection(['foo' => $directory]);
        })();

        /** @var TCollection<T> $bAssociative */
        $bAssociative = (static function (): DirectoryCollection {
            $directory = dir(dirname(__DIR__));

            static::assertInstanceOf(Directory::class, $directory);

            return new DirectoryCollection(['foo' => $directory]);
        })();

        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                $a0,
                $b0,
                static function (
                    self $self,
                    DirectoryCollection $collectionA,
                    DirectoryCollection $collectionB,
                    DirectoryCollection $collectionC,
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
                    DirectoryCollection $collectionA,
                    DirectoryCollection $collectionB,
                    DirectoryCollection $collectionC,
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
     * @return array<Directory>
     */
    protected static function createMultipleElements(AbstractCollectionTestCase $self): array
    {
        static::assertInstanceOf(self::class, $self);
        assert($self instanceof self); // @phpstan-ignore-line

        return [
            0 => $self->createDirectoryFromPath(__DIR__),
            'foo' => $self->createDirectoryFromPath(__DIR__),
            42 => $self->createDirectoryFromPath(__DIR__),
            43 => $self->createDirectoryFromPath(__DIR__),
        ];
    }

    protected static function createSingleElement(AbstractCollectionTestCase $self): Directory
    {
        static::assertInstanceOf(self::class, $self);
        assert($self instanceof self); // @phpstan-ignore-line

        return $self->createDirectoryFromPath(__DIR__);
    }

    /**
     * @return class-string<TCollection<T>>
     */
    protected static function getHandledCollectionClassName(): string
    {
        return DirectoryCollection::class;
    }

    /**
     * @throws RuntimeException
     */
    private function createDirectoryFromPath(string $path): Directory
    {
        if (false === is_dir($path)) {
            throw new RuntimeException(sprintf(
                'A directory does not exist on path: %s',
                $path,
            ));
        }

        $directory = dir($path);

        assert(is_object($directory));

        return $directory;
    }
}
