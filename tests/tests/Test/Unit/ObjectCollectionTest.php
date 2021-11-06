<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use Eboreum\Collections\ObjectCollection;

class ObjectCollectionTest extends AbstractTypeCollectionTestCase
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
                    0 => new class
                    {
                        public function __toString(): string
                        {
                            return 'foo';
                        }
                    },
                ];

                return [
                    '1 single item collection.',
                    $elements,
                    $elements,
                    static function (object $object): string {
                        return (string)$object;
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new class
                    {
                        public function __toString(): string
                        {
                            return 'a';
                        }
                    },
                    1 => new class
                    {
                        public function __toString(): string
                        {
                            return 'b';
                        }
                    },
                    2 => new class
                    {
                        public function __toString(): string
                        {
                            return 'c';
                        }
                    },
                    3 => new class
                    {
                        public function __toString(): string
                        {
                            return 'b';
                        }
                    },
                    4 => new class
                    {
                        public function __toString(): string
                        {
                            return 'd';
                        }
                    },
                    5 => new class
                    {
                        public function __toString(): string
                        {
                            return 'b';
                        }
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
                    static function (object $object): string {
                        return (string)$object;
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new class
                    {
                        public function __toString(): string
                        {
                            return 'a';
                        }
                    },
                    1 => new class
                    {
                        public function __toString(): string
                        {
                            return 'b';
                        }
                    },
                    2 => new class
                    {
                        public function __toString(): string
                        {
                            return 'c';
                        }
                    },
                    3 => new class
                    {
                        public function __toString(): string
                        {
                            return 'b';
                        }
                    },
                    4 => new class
                    {
                        public function __toString(): string
                        {
                            return 'd';
                        }
                    },
                    5 => new class
                    {
                        public function __toString(): string
                        {
                            return 'b';
                        }
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
                    static function (object $object): string {
                        return (string)$object;
                    },
                    false,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new class
                    {
                        public function __toString(): string
                        {
                            return 'd';
                        }
                    },
                    1 => new class
                    {
                        public function __toString(): string
                        {
                            return 'a';
                        }
                    },
                    2 => new class
                    {
                        public function __toString(): string
                        {
                            return 'c';
                        }
                    },
                    3 => new class
                    {
                        public function __toString(): string
                        {
                            return 'a';
                        }
                    },
                    4 => new class
                    {
                        public function __toString(): string
                        {
                            return 'b';
                        }
                    },
                    5 => new class
                    {
                        public function __toString(): string
                        {
                            return 'a';
                        }
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
                    static function (object $object): string {
                        return (string)$object;
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new class
                    {
                        public function __toString(): string
                        {
                            return 'd';
                        }
                    },
                    1 => new class
                    {
                        public function __toString(): string
                        {
                            return 'a';
                        }
                    },
                    2 => new class
                    {
                        public function __toString(): string
                        {
                            return 'c';
                        }
                    },
                    3 => new class
                    {
                        public function __toString(): string
                        {
                            return 'a';
                        }
                    },
                    4 => new class
                    {
                        public function __toString(): string
                        {
                            return 'b';
                        }
                    },
                    5 => new class
                    {
                        public function __toString(): string
                        {
                            return 'a';
                        }
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
                    static function (object $object): string {
                        return (string)$object;
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
                new ObjectCollection([0 => new \stdClass()]),
                new ObjectCollection([0 => new \DateTimeImmutable()]),
                function (
                    ObjectCollection $collectionA,
                    ObjectCollection $collectionB,
                    ObjectCollection $collectionC,
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
                new ObjectCollection(['foo' => new \stdClass()]),
                new ObjectCollection(['foo' => new \DateTimeImmutable()]),
                function (
                    ObjectCollection $collectionA,
                    ObjectCollection $collectionB,
                    ObjectCollection $collectionC,
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
    protected function getHandledCollectionClassName(): string
    {
        return ObjectCollection::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getSingleElement()
    {
        return new class
        {
        };
    }

    /**
     * {@inheritDoc}
     */
    protected function getMultipleElements(): array
    {
        return [
            new \stdClass(),
            'foo' => new class
            {
            },
            42 => dir(__DIR__),
            new \DateTimeImmutable(),
        ];
    }
}
