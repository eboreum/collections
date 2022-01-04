<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Directory;
use Eboreum\Collections\Object_\DirectoryCollection;
use RuntimeException;

class DirectoryCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
{
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
            (static function (): array {
                $elements = [
                    0 => dir(__DIR__),
                ];

                return [
                    '1 single item collection.',
                    $elements,
                    $elements,
                    static function (\Directory $object): string {
                        return $object->path;
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => dir(dirname(__DIR__, 1)),
                    1 => dir(__DIR__),
                    2 => dir(dirname(__DIR__, 2)),
                    3 => dir(__DIR__),
                    4 => dir(dirname(__DIR__, 3)),
                    5 => dir(__DIR__),
                ];

                return [
                    'Ascending, use first encountered.',
                    [
                        0 => $elements[0],
                        1 => $elements[1],
                        2 => $elements[2],
                        4 => $elements[4],
                    ],
                    $elements,
                    static function (\Directory $object): string {
                        return $object->path;
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => dir(dirname(__DIR__, 1)),
                    1 => dir(__DIR__),
                    2 => dir(dirname(__DIR__, 2)),
                    3 => dir(__DIR__),
                    4 => dir(dirname(__DIR__, 3)),
                    5 => dir(__DIR__),
                ];

                return [
                    'Ascending, use last encountered.',
                    [
                        0 => $elements[0],
                        2 => $elements[2],
                        4 => $elements[4],
                        5 => $elements[5],
                    ],
                    $elements,
                    static function (\Directory $object): string {
                        return $object->path;
                    },
                    false,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => dir(dirname(__DIR__, 3)),
                    1 => dir(__DIR__),
                    2 => dir(dirname(__DIR__, 2)),
                    3 => dir(__DIR__),
                    4 => dir(dirname(__DIR__, 1)),
                    5 => dir(__DIR__),
                ];

                return [
                    'Descending, use first encountered.',
                    [
                        0 => $elements[0],
                        1 => $elements[1],
                        2 => $elements[2],
                        4 => $elements[4],
                    ],
                    $elements,
                    static function (\Directory $object): string {
                        return $object->path;
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => dir(dirname(__DIR__, 3)),
                    1 => dir(__DIR__),
                    2 => dir(dirname(__DIR__, 2)),
                    3 => dir(__DIR__),
                    4 => dir(dirname(__DIR__, 1)),
                    5 => dir(__DIR__),
                ];

                return [
                    'Descending, use last encountered.',
                    [
                        0 => $elements[0],
                        2 => $elements[2],
                        4 => $elements[4],
                        5 => $elements[5],
                    ],
                    $elements,
                    static function (\Directory $object): string {
                        return $object->path;
                    },
                    false,
                ];
            })(),
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @return array<int, array{string, DirectoryCollection<Directory>, DirectoryCollection<Directory>, Closure: void}>
     */
    public function dataProvider_testWithMergedWorks(): array
    {
        // @phpstan-ignore-next-line Returned values are 100% correct, but phpstan still reports an error. False positive?
        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                (static function (): DirectoryCollection {
                    $directory = dir(__DIR__);

                    assert($directory instanceof \Directory);

                    return new DirectoryCollection([0 => $directory]);
                })(),
                (static function (): DirectoryCollection {
                    $directory = dir(dirname(__DIR__));

                    assert($directory instanceof \Directory);

                    return new DirectoryCollection([0 => $directory]);
                })(),
                function (
                    DirectoryCollection $collectionA,
                    DirectoryCollection $collectionB,
                    DirectoryCollection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(2, $collectionC, $message);
                    $this->assertSame($collectionA->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->first(), $collectionC->last(), $message);
                },
            ],
            [
                'Same name string keys. Will override.',
                (static function (): DirectoryCollection {
                    $directory = dir(__DIR__);

                    assert($directory instanceof \Directory);

                    return new DirectoryCollection(['foo' => $directory]);
                })(),
                (static function (): DirectoryCollection {
                    $directory = dir(dirname(__DIR__));

                    assert($directory instanceof \Directory);

                    return new DirectoryCollection(['foo' => $directory]);
                })(),
                function (
                    DirectoryCollection $collectionA,
                    DirectoryCollection $collectionB,
                    DirectoryCollection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(1, $collectionC, $message);
                    $this->assertSame(['foo'], $collectionC->getKeys(), $message);
                    $this->assertNotSame($collectionA->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->last(), $collectionC->last(), $message);
                },
            ],
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @return array<Directory>
     */
    protected function createMultipleElements(): array
    {
        return [
            $this->createDirectoryFromPath(__DIR__),
            'foo' => $this->createDirectoryFromPath(__DIR__),
            42 => $this->createDirectoryFromPath(__DIR__),
            $this->createDirectoryFromPath(__DIR__),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function createSingleElement(): Directory
    {
        return $this->createDirectoryFromPath(__DIR__);
    }

    /**
     * {@inheritDoc}
     */
    protected function getHandledCollectionClassName(): string
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
