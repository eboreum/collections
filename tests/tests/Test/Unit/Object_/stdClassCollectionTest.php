<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Eboreum\Collections\Object_\stdClassCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use stdClass;
use Test\Unit\Eboreum\Collections\AbstractCollectionTestCase;

/**
 * @template T of stdClass
 * @template TCollection of stdClassCollection<T>
 * @extends AbstractNamedClassOrInterfaceCollectionTestCase<T, TCollection>
 */
#[CoversClass(stdClassCollection::class)]
class stdClassCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase // phpcs:ignore
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
                static function () {
                    return '';
                },
                true,
            ],
            [
                '1 single item collection.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new stdClass(),
                    ];

                    $elements[0]->var = 'a';

                    return [
                        $elements,
                        $elements,
                    ];
                },
                static function (stdClass $object) {
                    return $object->var;
                },
                true,
            ],
            [
                'Ascending, use first encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new stdClass(),
                        1 => new stdClass(),
                        2 => new stdClass(),
                        3 => new stdClass(),
                        4 => new stdClass(),
                        5 => new stdClass(),
                    ];

                    $elements[0]->var = 'a';
                    $elements[1]->var = 'b';
                    $elements[2]->var = 'c';
                    $elements[3]->var = 'b';
                    $elements[4]->var = 'd';
                    $elements[5]->var = 'b';

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
                static function (stdClass $object) {
                    return $object->var;
                },
                true,
            ],
            [
                'Ascending, use last encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new stdClass(),
                        1 => new stdClass(),
                        2 => new stdClass(),
                        3 => new stdClass(),
                        4 => new stdClass(),
                        5 => new stdClass(),
                    ];

                    $elements[0]->var = 'a';
                    $elements[1]->var = 'b';
                    $elements[2]->var = 'c';
                    $elements[3]->var = 'b';
                    $elements[4]->var = 'd';
                    $elements[5]->var = 'b';

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
                static function (stdClass $object) {
                    return $object->var;
                },
                false,
            ],
            [
                'Descending, use first encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new stdClass(),
                        1 => new stdClass(),
                        2 => new stdClass(),
                        3 => new stdClass(),
                        4 => new stdClass(),
                        5 => new stdClass(),
                    ];

                    $elements[0]->var = 'd';
                    $elements[1]->var = 'b';
                    $elements[2]->var = 'c';
                    $elements[3]->var = 'b';
                    $elements[4]->var = 'a';
                    $elements[5]->var = 'b';

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
                static function (stdClass $object) {
                    return $object->var;
                },
                true,
            ],
            [
                'Descending, use last encountered.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = [
                        0 => new stdClass(),
                        1 => new stdClass(),
                        2 => new stdClass(),
                        3 => new stdClass(),
                        4 => new stdClass(),
                        5 => new stdClass(),
                    ];

                    $elements[0]->var = 'd';
                    $elements[1]->var = 'b';
                    $elements[2]->var = 'c';
                    $elements[3]->var = 'b';
                    $elements[4]->var = 'a';
                    $elements[5]->var = 'b';

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
                static function (stdClass $object) {
                    return $object->var;
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
        $a0 = new stdClassCollection([0 => new stdClass()]);

        /** @var TCollection<T> $b0 */
        $b0 = new stdClassCollection([0 => new stdClass()]);

        /** @var TCollection<T> $aAssociative */
        $aAssociative = new stdClassCollection(['foo' => new stdClass()]);

        /** @var TCollection<T> $bAssociative */
        $bAssociative = new stdClassCollection(['foo' => new stdClass()]);

        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                $a0,
                $b0,
                static function (
                    self $self,
                    stdClassCollection $collectionA,
                    stdClassCollection $collectionB,
                    stdClassCollection $collectionC,
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
                    stdClassCollection $collectionA,
                    stdClassCollection $collectionB,
                    stdClassCollection $collectionC,
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
     * @return array<stdClass>
     */
    protected static function createMultipleElements(AbstractCollectionTestCase $self): array
    {
        return [
            0 => new stdClass(),
            'foo' => new stdClass(),
            42 => new stdClass(),
            43 => new stdClass(),
        ];
    }

    protected static function createSingleElement(AbstractCollectionTestCase $self): stdClass
    {
        return new stdClass();
    }

    /**
     * @return class-string<TCollection<T>>
     */
    protected static function getHandledCollectionClassName(): string
    {
        return stdClassCollection::class;
    }
}
