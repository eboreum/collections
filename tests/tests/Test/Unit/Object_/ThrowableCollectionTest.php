<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Eboreum\Collections\Object_\ThrowableCollection;
use Error;
use Exception;
use Throwable;

class ThrowableCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
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
                    0 => new Exception('foo'),
                ];

                return [
                    '1 single item collection.',
                    $elements,
                    $elements,
                    static function (Throwable $object): string {
                        return $object->getMessage();
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new Exception('A'),
                    1 => new Error('B'),
                    2 => new Error('C'),
                    3 => new Exception('B'),
                    4 => new Exception('D'),
                    5 => new Error('B'),
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
                    static function (Throwable $object): string {
                        return $object->getMessage();
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new Exception('A'),
                    1 => new Error('B'),
                    2 => new Error('C'),
                    3 => new Exception('B'),
                    4 => new Exception('D'),
                    5 => new Error('B'),
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
                    static function (Throwable $object): string {
                        return $object->getMessage();
                    },
                    false,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new Exception('D'),
                    1 => new Error('B'),
                    2 => new Error('C'),
                    3 => new Exception('B'),
                    4 => new Exception('A'),
                    5 => new Error('B'),
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
                    static function (Throwable $object): string {
                        return $object->getMessage();
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new Exception('D'),
                    1 => new Error('B'),
                    2 => new Error('C'),
                    3 => new Exception('B'),
                    4 => new Exception('A'),
                    5 => new Error('B'),
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
                    static function (Throwable $object): string {
                        return $object->getMessage();
                    },
                    false,
                ];
            })(),
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @return array<int, array{string, ThrowableCollection<Throwable>, ThrowableCollection<Throwable>, Closure: void}>
     */
    public function dataProvider_testWithMergedWorks(): array
    {
        // @phpstan-ignore-next-line Returned values are 100% correct, but phpstan still reports an error. False positive?
        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                new ThrowableCollection([0 => new Exception()]),
                new ThrowableCollection([0 => new Error()]),
                function (
                    ThrowableCollection $collectionA,
                    ThrowableCollection $collectionB,
                    ThrowableCollection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(2, $collectionC, $message);
                    $this->assertSame([0, 1], $collectionC->getKeys(), $message);
                    $this->assertSame($collectionA->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->first(), $collectionC->last(), $message);
                },
            ],
            [
                'Same name string keys. Will override.',
                new ThrowableCollection(['foo' => new Exception()]),
                new ThrowableCollection(['foo' => new Error()]),
                function (
                    ThrowableCollection $collectionA,
                    ThrowableCollection $collectionB,
                    ThrowableCollection $collectionC,
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
     * @return array<Throwable>
     */
    protected function createMultipleElements(): array
    {
        return [
            new Exception(),
            'foo' => new Error(),
            42 => new Error(),
            new Exception(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function createSingleElement(): Throwable
    {
        return new Exception();
    }

    /**
     * {@inheritDoc}
     */
    protected function getHandledCollectionClassName(): string
    {
        return ThrowableCollection::class;
    }
}
