<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Eboreum\Collections\Object_\stdClassCollection;

class stdClassCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
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
                static function () {
                    return '';
                },
                true,
            ],
            (static function (): array {
                $elements = [
                    0 => new \stdClass(),
                ];

                $elements[0]->var = 'a';

                return [
                    '1 single item collection.',
                    $elements,
                    $elements,
                    static function (\stdClass $object) {
                        return $object->var;
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new \stdClass(),
                    1 => new \stdClass(),
                    2 => new \stdClass(),
                    3 => new \stdClass(),
                    4 => new \stdClass(),
                    5 => new \stdClass(),
                ];

                $elements[0]->var = 'a';
                $elements[1]->var = 'b';
                $elements[2]->var = 'c';
                $elements[3]->var = 'b';
                $elements[4]->var = 'd';
                $elements[5]->var = 'b';

                return [
                    'Ascending, use first encountered.',
                    [
                        0 => $elements[0],
                        1 => $elements[1],
                        2 => $elements[2],
                        4 => $elements[4],
                    ],
                    $elements,
                    static function (\stdClass $object) {
                        return $object->var;
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new \stdClass(),
                    1 => new \stdClass(),
                    2 => new \stdClass(),
                    3 => new \stdClass(),
                    4 => new \stdClass(),
                    5 => new \stdClass(),
                ];

                $elements[0]->var = 'a';
                $elements[1]->var = 'b';
                $elements[2]->var = 'c';
                $elements[3]->var = 'b';
                $elements[4]->var = 'd';
                $elements[5]->var = 'b';

                return [
                    'Ascending, use last encountered.',
                    [
                        0 => $elements[0],
                        2 => $elements[2],
                        4 => $elements[4],
                        5 => $elements[5],
                    ],
                    $elements,
                    static function (\stdClass $object) {
                        return $object->var;
                    },
                    false,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new \stdClass(),
                    1 => new \stdClass(),
                    2 => new \stdClass(),
                    3 => new \stdClass(),
                    4 => new \stdClass(),
                    5 => new \stdClass(),
                ];

                $elements[0]->var = 'd';
                $elements[1]->var = 'b';
                $elements[2]->var = 'c';
                $elements[3]->var = 'b';
                $elements[4]->var = 'a';
                $elements[5]->var = 'b';

                return [
                    'Descending, use first encountered.',
                    [
                        0 => $elements[0],
                        1 => $elements[1],
                        2 => $elements[2],
                        4 => $elements[4],
                    ],
                    $elements,
                    static function (\stdClass $object) {
                        return $object->var;
                    },
                    true,
                ];
            })(),
            (static function (): array {
                $elements = [
                    0 => new \stdClass(),
                    1 => new \stdClass(),
                    2 => new \stdClass(),
                    3 => new \stdClass(),
                    4 => new \stdClass(),
                    5 => new \stdClass(),
                ];

                $elements[0]->var = 'd';
                $elements[1]->var = 'b';
                $elements[2]->var = 'c';
                $elements[3]->var = 'b';
                $elements[4]->var = 'a';
                $elements[5]->var = 'b';

                return [
                    'Descending, use last encountered.',
                    [
                        0 => $elements[0],
                        2 => $elements[2],
                        4 => $elements[4],
                        5 => $elements[5],
                    ],
                    $elements,
                    static function (\stdClass $object) {
                        return $object->var;
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
                new stdClassCollection([0 => new \stdClass()]),
                new stdClassCollection([0 => new \stdClass()]),
                function (
                    stdClassCollection $collectionA,
                    stdClassCollection $collectionB,
                    stdClassCollection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(2, $collectionC, $message);
                    $this->assertSame($collectionA->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->first(), $collectionC->last(), $message);
                },
            ],
            [
                'Same name string keys. Will override.',
                new stdClassCollection(['foo' => new \stdClass()]),
                new stdClassCollection(['foo' => new \stdClass()]),
                function (
                    stdClassCollection $collectionA,
                    stdClassCollection $collectionB,
                    stdClassCollection $collectionC,
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
        return stdClassCollection::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getSingleElement()
    {
        return new \stdClass();
    }

    /**
     * {@inheritDoc}
     */
    protected function getMultipleElements(): array
    {
        return [
            new \stdClass(),
            'foo' => new \stdClass(),
            42 => new \stdClass(),
            new \stdClass(),
        ];
    }
}
