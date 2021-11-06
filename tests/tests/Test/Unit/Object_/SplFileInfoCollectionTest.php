<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Eboreum\Collections\Object_\SplFileInfoCollection;

class SplFileInfoCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
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
                    0 => new \SplFileInfo(__FILE__),
                ];

                $elements[0]->var = 'a'; /** @phpstan-ignore-line */

                return [
                    '1 single item collection.',
                    $elements,
                    $elements,
                    static function (\SplFileInfo $object): string {
                        $var = $object->var; /** @phpstan-ignore-line */

                        assert(is_string($var));

                        return $var;
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $basePath = TEST_ROOT_PATH
                    . '/resources/TestResource/Unit/Object_/SplFileInfoCollectionTest'
                    . '/dataProvider_testToUniqueByCallbackWorks';
                $elements = [
                    0 => new \SplFileInfo($basePath . '/A.txt'),
                    1 => new \SplFileInfo($basePath . '/B.txt'),
                    2 => new \SplFileInfo($basePath . '/C.txt'),
                    3 => new \SplFileInfo($basePath . '/B.txt'),
                    4 => new \SplFileInfo($basePath . '/D.txt'),
                    5 => new \SplFileInfo($basePath . '/B.txt'),
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
                    static function (\SplFileInfo $object): string {
                        return $object->getFilename();
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $basePath = TEST_ROOT_PATH
                    . '/resources/TestResource/Unit/Object_/SplFileInfoCollectionTest'
                    . '/dataProvider_testToUniqueByCallbackWorks';

                $elements = [
                    0 => new \SplFileInfo($basePath . '/A.txt'),
                    1 => new \SplFileInfo($basePath . '/B.txt'),
                    2 => new \SplFileInfo($basePath . '/C.txt'),
                    3 => new \SplFileInfo($basePath . '/B.txt'),
                    4 => new \SplFileInfo($basePath . '/D.txt'),
                    5 => new \SplFileInfo($basePath . '/B.txt'),
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
                    static function (\SplFileInfo $object): string {
                        return $object->getFilename();
                    },
                    false,
                ];
            })(),
            (static function (): array {
                $basePath = TEST_ROOT_PATH
                    . '/resources/TestResource/Unit/Object_/SplFileInfoCollectionTest'
                    . '/dataProvider_testToUniqueByCallbackWorks';

                $elements = [
                    0 => new \SplFileInfo($basePath . '/D.txt'),
                    1 => new \SplFileInfo($basePath . '/B.txt'),
                    2 => new \SplFileInfo($basePath . '/C.txt'),
                    3 => new \SplFileInfo($basePath . '/B.txt'),
                    4 => new \SplFileInfo($basePath . '/A.txt'),
                    5 => new \SplFileInfo($basePath . '/B.txt'),
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
                    static function (\SplFileInfo $object): string {
                        return $object->getFilename();
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $basePath = TEST_ROOT_PATH
                    . '/resources/TestResource/Unit/Object_/SplFileInfoCollectionTest'
                    . '/dataProvider_testToUniqueByCallbackWorks';

                $elements = [
                    0 => new \SplFileInfo($basePath . '/D.txt'),
                    1 => new \SplFileInfo($basePath . '/B.txt'),
                    2 => new \SplFileInfo($basePath . '/C.txt'),
                    3 => new \SplFileInfo($basePath . '/B.txt'),
                    4 => new \SplFileInfo($basePath . '/A.txt'),
                    5 => new \SplFileInfo($basePath . '/B.txt'),
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
                    static function (\SplFileInfo $object): string {
                        return $object->getFilename();
                    },
                    false,
                ];
            })(),
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
                new SplFileInfoCollection([0 => new \SplFileInfo(__FILE__)]),
                new SplFileInfoCollection([0 => new \SplFileInfo(__FILE__)]),
                function (
                    SplFileInfoCollection $collectionA,
                    SplFileInfoCollection $collectionB,
                    SplFileInfoCollection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(2, $collectionC, $message);
                    $this->assertSame($collectionA->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->first(), $collectionC->last(), $message);
                },
            ],
            [
                'Same name string keys. Will override.',
                new SplFileInfoCollection(['foo' => new \SplFileInfo(__FILE__)]),
                new SplFileInfoCollection(['foo' => new \SplFileInfo(__FILE__)]),
                function (
                    SplFileInfoCollection $collectionA,
                    SplFileInfoCollection $collectionB,
                    SplFileInfoCollection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(1, $collectionC, $message);
                    $this->assertSame(['foo'], $collectionC->getKeys(), $message);
                    $this->assertSame($collectionB->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->last(), $collectionC->last(), $message);
                },
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getHandledCollectionClassName(): string
    {
        return SplFileInfoCollection::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getSingleElement()
    {
        return new \SplFileInfo(__FILE__);
    }

    /**
     * {@inheritDoc}
     */
    protected function getMultipleElements(): array
    {
        return [
            new \SplFileInfo(__FILE__),
            'foo' => new \SplFileInfo(__FILE__),
            42 => new \SplFileInfo(__FILE__),
            new \SplFileInfo(__FILE__),
        ];
    }
}
