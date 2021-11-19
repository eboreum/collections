<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Eboreum\Collections\Object_\ClosureCollection;

class ClosureCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
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
                    0 => static function (): string {
                        return '';
                    },
                ];

                return [
                    '1 single item collection.',
                    $elements,
                    $elements,
                    static function (\Closure $closure) {
                        return $closure();
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => static function (): string {
                        return 'a';
                    },
                    1 => static function (): string {
                        return 'b';
                    },
                    2 => static function (): string {
                        return 'c';
                    },
                    3 => static function (): string {
                        return 'b';
                    },
                    4 => static function (): string {
                        return 'd';
                    },
                    5 => static function (): string {
                        return 'b';
                    },
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
                    static function (\Closure $closure) {
                        return $closure();
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => static function (): string {
                        return 'a';
                    },
                    1 => static function (): string {
                        return 'b';
                    },
                    2 => static function (): string {
                        return 'c';
                    },
                    3 => static function (): string {
                        return 'b';
                    },
                    4 => static function (): string {
                        return 'd';
                    },
                    5 => static function (): string {
                        return 'b';
                    },
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
                    static function (\Closure $closure) {
                        return $closure();
                    },
                    false,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => static function (): string {
                        return 'd';
                    },
                    1 => static function (): string {
                        return 'b';
                    },
                    2 => static function (): string {
                        return 'c';
                    },
                    3 => static function (): string {
                        return 'b';
                    },
                    4 => static function (): string {
                        return 'a';
                    },
                    5 => static function (): string {
                        return 'b';
                    },
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
                    static function (\Closure $closure) {
                        return $closure();
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => static function (): string {
                        return 'd';
                    },
                    1 => static function (): string {
                        return 'b';
                    },
                    2 => static function (): string {
                        return 'c';
                    },
                    3 => static function (): string {
                        return 'b';
                    },
                    4 => static function (): string {
                        return 'a';
                    },
                    5 => static function (): string {
                        return 'b';
                    },
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
                    static function (\Closure $closure) {
                        return $closure();
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
                new ClosureCollection([
                    0 => static function (): void {
                        // Merely for test purposes
                    },
                ]),
                new ClosureCollection([
                    0 => static function (): void {
                        // Merely for test purposes
                    },
                ]),
                function (
                    ClosureCollection $collectionA,
                    ClosureCollection $collectionB,
                    ClosureCollection $collectionC,
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
                new ClosureCollection([
                    'foo' => static function (): void {
                        // Merely for test purposes
                    },
                ]),
                new ClosureCollection([
                    'foo' => static function (): void {
                        // Merely for test purposes
                    },
                ]),
                function (
                    ClosureCollection $collectionA,
                    ClosureCollection $collectionB,
                    ClosureCollection $collectionC,
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
     */
    protected function createMultipleElements(): array
    {
        return [
            static function (): void {
                // Merely for test purposes
            },
            'foo' => static function (): void {
                // Merely for test purposes
            },
            42 => static function (): void {
                // Merely for test purposes
            },
            static function (): void {
                // Merely for test purposes
            },
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function createSingleElement()
    {
        return static function (): void {
            // Merely for test purposes
        };
    }

    /**
     * {@inheritDoc}
     */
    protected function getHandledCollectionClassName(): string
    {
        return ClosureCollection::class;
    }
}
