<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use Closure;
use Eboreum\Collections\Caster;
use Eboreum\Collections\Collection;
use Eboreum\Collections\Contract\CollectionInterface\ToReindexedDuplicateKeyBehaviorEnum;
use Eboreum\Collections\Exception\RuntimeException;
use Eboreum\Collections\Exception\UnacceptableCollectionException;
use Eboreum\Collections\Exception\UnacceptableElementException;
use Eboreum\Collections\IntegerCollection;
use Eboreum\Exceptional\ExceptionMessageGenerator;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionMethod;
use ReflectionObject;
use stdClass;

use function assert;
use function basename;
use function gettype;
use function implode;
use function is_int;
use function is_object;
use function is_string;
use function preg_quote;
use function sprintf;
use function strval;

/**
 * @template T
 * @template TCollection of Collection<T>
 * @extends AbstractCollectionTestCase<T, TCollection>
 */
#[CoversClass(Collection::class)]
class CollectionTest extends AbstractCollectionTestCase
{
    /**
     * @return array<int, array{array<int, string>, array<int|string, mixed>, Closure}>
     */
    public static function providerTestEachWorks(): array
    {
        return [
            [
                [
                    'integer:NULL',
                    'integer:boolean',
                    'integer:integer',
                    'string:string',
                ],
                [null, true, 42, 'foo' => 'bar'],
                static function ($v, $k, stdClass $carry): void {
                    $carry->results[] = sprintf(
                        '%s:%s',
                        gettype($k),
                        gettype($v),
                    );
                },
            ],
            [
                [
                    'integer:NULL',
                    'integer:boolean',
                    'string:string',
                ],
                [null, true, 42, 'foo' => 'bar'],
                static function ($v, $k, stdClass $carry) {
                    if (2 === $k) {
                        return false;
                    }

                    $carry->results[] = sprintf(
                        '%s:%s',
                        gettype($k),
                        gettype($v),
                    );
                },
            ],
        ];
    }

    /**
     * @return array<int, array{array<int, string>, array<int|string, mixed>, Closure}>
     */
    public static function providerTestEveryWorks(): array
    {
        return [
            [
                [
                    'integer:NULL',
                    'integer:boolean',
                ],
                [null, true, 42, 'foo' => 'bar'],
                static function ($v, $k, stdClass $carry) {
                    if (2 === $k) {
                        return false;
                    }

                    $carry->results[] = sprintf(
                        '%s:%s',
                        gettype($k),
                        gettype($v),
                    );
                },
            ],
            [
                ['integer:NULL'],
                [null],
                static function ($v, $k, stdClass $carry) {
                    $carry->results[] = sprintf(
                        '%s:%s',
                        gettype($k),
                        gettype($v),
                    );

                    return true;
                },
            ],
            [
                ['integer:NULL'],
                [null],
                static function ($v, $k, stdClass $carry) {
                    $carry->results[] = sprintf(
                        '%s:%s',
                        gettype($k),
                        gettype($v),
                    );

                    return null;
                },
            ],
        ];
    }

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
                static function (AbstractCollectionTestCase $self): array {
                    /** @var array<int, T> $expected */
                    $expected = [self::createSingleElement($self)];

                    return [
                        $expected,
                        $expected,
                    ];
                },
                static function (): string {
                    return '';
                },
                true,
            ],
            [
                'Integer item collection, ascending, use first encountered.',
                static function (): array {
                    /** @var array<int, T> $expected */
                    $expected = [0 => 1, 1 => 2, 3 => 3, 5 => 4];

                    /** @var array<int, T> $elements */
                    $elements = [1, 2, 1, 3, 1, 4];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (mixed $value): string {
                    assert(is_int($value));

                    return strval($value);
                },
                true,
            ],
            [
                'Integer item collection, ascending, use last encountered.',
                static function (): array {
                    /** @var array<int, T> $expected */
                    $expected = [1 => 2, 3 => 3, 4 => 1, 5 => 4];

                    /** @var array<int, T> $elements */
                    $elements = [1, 2, 1, 3, 1, 4];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (mixed $value): string {
                    assert(is_int($value));

                    return strval($value);
                },
                false,
            ],
            [
                'Integer item collection, descending, use first encountered.',
                static function (): array {
                    /** @var array<int, T> $expected */
                    $expected = [0 => 4, 1 => 1, 2 => 3, 4 => 2];

                    /** @var array<int, T> $elements */
                    $elements = [4, 1, 3, 1, 2, 1];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (mixed $value): string {
                    assert(is_int($value));

                    return strval($value);
                },
                true,
            ],
            [
                'Integer item collection, descending, use last encountered.',
                static function (): array {
                    /** @var array<int, T> $expected */
                    $expected = [0 => 4, 2 => 3, 4 => 2, 5 => 1];

                    /** @var array<int, T> $elements */
                    $elements = [4, 1, 3, 1, 2, 1];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (mixed $value): string {
                    assert(is_int($value));

                    return strval($value);
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
     *     Collection<mixed>,
     *     Collection<mixed>,
     *     Closure(self<T, TCollection<T>>, TCollection<T>, TCollection<T>, TCollection<T>, string): void,
     *   },
     * >
     */
    public static function providerTestWithMergedWorks(): array
    {
        /** @var TCollection<T> $a0 */
        $a0 =  new Collection([0 => 3.1415, 1 => null]);

        /** @var TCollection<T> $b0 */
        $b0 = new Collection([0 => 2.7182, 1 => 42]);

        /** @var TCollection<T> $aAssociative */
        $aAssociative = new Collection(['foo' => 3.1415, 1 => null]);

        /** @var TCollection<T> $bAssociative */
        $bAssociative = new Collection(['foo' => 2.7182, 1 => 42]);

        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                $a0,
                $b0,
                static function (
                    self $self,
                    Collection $collectionA,
                    Collection $collectionB,
                    Collection $collectionC,
                    string $message
                ): void {
                    $self->assertCount(4, $collectionC, $message);
                    $self->assertSame(
                        [
                            0 => 3.1415,
                            1 => null,
                            2 => 2.7182,
                            3 => 42,
                        ],
                        $collectionC->toArray(),
                        $message,
                    );
                },
            ],
            [
                'Same name string keys. Will override.',
                $aAssociative,
                $bAssociative,
                static function (
                    self $self,
                    Collection $collectionA,
                    Collection $collectionB,
                    Collection $collectionC,
                    string $message
                ): void {
                    $self->assertCount(3, $collectionC, $message);
                    $self->assertSame(
                        [
                            'foo' => 2.7182,
                            0 => null,
                            1 => 42,
                        ],
                        $collectionC->toArray(),
                        $message,
                    );
                },
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected static function createMultipleElements(AbstractCollectionTestCase $self): array
    {
        /** @var array{0: T, foo: T, 42: T, 43: T} $elements */
        $elements = [
            0 => 42,
            'foo' => 3.1415,
            42 => null,
            43 => true,
        ];

        return $elements;
    }

    protected static function createSingleElement(AbstractCollectionTestCase $self): int
    {
        return 42;
    }

    protected static function getHandledCollectionClassName(): string
    {
        return Collection::class;
    }

    public function testConstructThrowsExceptionWhenSomeElementsAreNotAccepted(): void
    {
        $elements = [
            'foo',
            42,
            'bar',
            3.14,
            'baz',
        ];

        try {
            new class ($elements) extends Collection // @phpstan-ignore-line
            {
                public static function isElementAccepted(mixed $element): bool
                {
                    return is_string($element);
                }
            };
        } catch (Exception $e) { // @phpstan-ignore-line
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s\-\>__construct\(',
                            '\$elements = \(array\(5\)\) \[.+\] \(sample\)',
                        '\) inside \(object\) \\\\%s@anonymous\/in\/.+\/%s\:\d+ \{.+\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'In argument \$elements, 2\/5 elements are invalid, including\: \[',
                            '1 \=\> \(int\) 42',
                            ', 3 \=\> \(float\) 3\.14',
                        '\]',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testContainsThrowsExceptionWhenArgumentElementIsNotAcceptedByCollection(): void
    {
        $collectionA = new class extends Collection
        {
            /**
             * {@inheritDoc}
             *
             * @override
             */
            public static function isElementAccepted(mixed $element): bool
            {
                return false;
            }
        };

        try {
            $collectionA->contains(null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(UnacceptableElementException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'Argument $element = %s is not accepted by %s',
                    Caster::getInstance()->castTyped(null),
                    Caster::makeNormalizedClassName(new ReflectionObject($collectionA)),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testCurrentReturnsNullWhenThereAreNoElementsInCollection(): void
    {
        $collection = new Collection();

        $this->assertNull($collection->current()); // @phpstan-ignore-line
    }

    /**
     * @param array<int, string> $expected
     * @param array<int, mixed> $elements
     */
    #[DataProvider('providerTestEachWorks')]
    public function testEachWorks(array $expected, array $elements, Closure $callback): void
    {
        $collection = new Collection($elements);

        $carry = new stdClass();
        $carry->results = [];

        $collection->each($callback, $carry);

        $this->assertSame($expected, $carry->results);
    }

    public function testToReindexedWorks(): void
    {
        $elements = [
            0 => 'lorem',
            'foo' => 0,
            42 => '0',
        ];

        $collectionA = new Collection($elements);

        $collectionB = $collectionA->toReindexed(
            static function (string|int $element, string|int $key): string|int {
                return $key;
            },
            ToReindexedDuplicateKeyBehaviorEnum::use_first_element,
        );

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elements, $collectionA->toArray());
        $this->assertSame($elements, $collectionB->toArray());

        $collectionC = $collectionA->toReindexed(
            static function (string|int $element): string {
                return (string)$element;
            },
            ToReindexedDuplicateKeyBehaviorEnum::use_first_element,
        );

        $this->assertNotSame($collectionA, $collectionC);
        $this->assertSame($elements, $collectionA->toArray());
        $this->assertSame(
            [
                'lorem' => 'lorem',
                '0' => 0,
            ],
            $collectionC->toArray(),
        );

        $collectionD = $collectionA->toReindexed(
            static function (string|int $element, string|int $key): string {
                return (string)$element;
            },
            ToReindexedDuplicateKeyBehaviorEnum::use_last_element,
        );

        $this->assertNotSame($collectionA, $collectionD);
        $this->assertSame($elements, $collectionA->toArray());
        $this->assertSame(
            [
                'lorem' => 'lorem',
                '0' => '0',
            ],
            $collectionD->toArray(),
        );
    }

    public function testToReindexedThrowsExceptionWhenClosureReturnContains2Of4InvalidValues(): void
    {
        $collection = new Collection([
            0 => 'lorem',
            'foo' => null,
            42 => '0',
            'bar' => true,
        ]);

        try {
            $collection->toReindexed(
                // @phpstan-ignore-next-line
                static function ($element) {
                    return $element;
                },
            );
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in (\\\\%s)->toReindexed\(',
                            '\$closure = \(object\) \\\\Closure\(\$element\)',
                            ', \$duplicateKeyBehavior = \(enum\) \\\\%s \{.+\}',
                        '\) inside \(object\) \1 \{.+\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(ToReindexedDuplicateKeyBehaviorEnum::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'For 2\/4 elements, the \$closure argument did not produce an int or string\.',
                    ' Errors given: \[',
                        '"foo" => \(null\) null: Resulting key is: \(null\) null',
                        ', "bar" => \(bool\) true: Resulting key is: \(bool\) true',
                    '\]',
                    '$',
                    '/',
                ]),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testToReindexedThrowsExceptionWhenClosureReturnContains1Of1InvalidValues(): void
    {
        $collection = new Collection([0 => null]);

        try {
            $collection->toReindexed(
                // @phpstan-ignore-next-line
                static function ($element) {
                    return $element;
                },
            );
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in (\\\\%s)->toReindexed\(',
                            '\$closure = \(object\) \\\\Closure\(\$element\)',
                            ', \$duplicateKeyBehavior = \(enum\) \\\\%s \{.+\}',
                        '\) inside \(object\) \1 \{.+\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(ToReindexedDuplicateKeyBehaviorEnum::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'For 1\/1 element, the \$closure argument did not produce an int or string\.',
                    ' Errors given: \[',
                        '0 => \(null\) null: Resulting key is: \(null\) null',
                    '\]',
                    '$',
                    '/',
                ]),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testToReindexedThrowsExceptionWhenClosureReturnsDuplicateKeysAndThisIsNotAllowed(): void
    {
        $collection = new Collection([
            0 => 1,
            'foo' => 1,
            2 => 0,
            3 => 1,
            'bar' => 2,
            5 => 3,
            6 => 2,
        ]);

        try {
            $collection->toReindexed(static function (int $element): int {
                return $element;
            });
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in (\\\\%s)->toReindexed\(',
                            '\$closure = \(object\) \\\\Closure\(int \$element\): int',
                            ', \$duplicateKeyBehavior = \(enum\) \\\\%s \{.+\}',
                        '\) inside \(object\) \1 \{.+\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(ToReindexedDuplicateKeyBehaviorEnum::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'For 5\/7 elements, the \$closure argument produced a duplicate key, which is not allowed:',
                    ' Resulting key 1 was produced from the 3 indexes: \[0, "foo", 3\]\.',
                    ' Resulting key 2 was produced from the 2 indexes: \["bar", 6\]',
                    '$',
                    '/',
                ]),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithAddedThrowsExceptionWhenElementIsNotAcceptedByCollection(): void
    {
        $elements = [true, 42, 'foo' => 'bar'];
        $collection = new class ($elements) extends Collection
        {
            /**
             * {@inheritDoc}
             *
             * @override
             */
            public static function isElementAccepted(mixed $element): bool
            {
                return null !== $element;
            }
        };

        try {
            $collection->withAdded(null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(UnacceptableElementException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'Argument $element = %s cannot be added to the current collection, %s',
                    Caster::getInstance()->castTyped(null),
                    Caster::getInstance()->castTyped($collection),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(UnacceptableElementException::class, $currentException::class);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithAddedHandlesExceptionGracefully(): void
    {
        $elements = [true, 42, 'foo' => 'bar'];
        $collection = new class ($elements) extends Collection
        {
            public function __clone()
            {
                throw new Exception('FAIL');
            }
        };

        try {
            $collection->withAdded(null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertSame(
                ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                    $collection,
                    new ReflectionMethod($collection, 'withAdded'),
                    [null],
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(Exception::class, $currentException::class);
            $this->assertSame('FAIL', $currentException->getMessage());

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithAddedMultipleThrowsExceptionWhenElementIsNotAcceptedByCollection(): void
    {
        $elements = [true, 42, 'foo' => 'bar'];
        $collection = new class ($elements) extends Collection
        {
            /**
             * {@inheritDoc}
             *
             * @override
             */
            public static function isElementAccepted(mixed $element): bool
            {
                return null !== $element;
            }
        };

        $elements = [null, 3.1415];

        try {
            $collection->withAddedMultiple($elements);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(UnacceptableElementException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'Argument $elements = %s cannot be added to the current collection, %s',
                    Caster::getInstance()->castTyped($elements),
                    Caster::getInstance()->castTyped($collection),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(UnacceptableElementException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'In argument $elements = %s, 1/2 elements are invalid, including: [0 => %s]',
                    Caster::getInstance()->castTyped($elements),
                    Caster::getInstance()->castTyped(null),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithAddedMultipleHandlesExceptionGracefully(): void
    {
        $elements = [true, 42, 'foo' => 'bar'];
        $collection = new class ($elements) extends Collection
        {
            public function __clone()
            {
                throw new Exception('FAIL');
            }
        };

        $elements = [null, 3.1415];

        try {
            $collection->withAddedMultiple($elements);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertSame(
                ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                    $collection,
                    new ReflectionMethod($collection, 'withAddedMultiple'),
                    [$elements],
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(Exception::class, $currentException::class);
            $this->assertSame('FAIL', $currentException->getMessage());

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithFilteredHandlesExceptionGracefullyWhenAnExceptionIsThrownInsideTheClosure(): void
    {
        $collection = new Collection([null, true, 42, 'foo' => 'bar']);

        try {
            $collection->withFiltered(static function ($v): void {
                throw new Exception('fail');
            });
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>withFiltered\(',
                            '\$callback = \(object\) \\\\Closure\(\$v\): void',
                        '\) inside \(object\) \\\\%s \{',
                            '\$elements = \(array\(4\)\) \[.+\] \(sample\)',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame('Exception', $currentException::class);
            $this->assertSame('fail', $currentException->getMessage());

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithMergedThrowsExceptionWhenArgumentCollectionBIsNotASubclassOfCollectionA(): void
    {
        $collectionA = new class ([true, 42, 'foo' => 'bar']) extends Collection
        {
            /**
             * {@inheritDoc}
             *
             * @override
             */
            public static function isElementAccepted(mixed $element): bool
            {
                return null !== $element;
            }
        };

        $collectionB = new Collection([null]);

        try {
            $collectionA->withMerged($collectionB); // @phpstan-ignore-line
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(UnacceptableCollectionException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'The current collection, %s, cannot be merged with argument $collection = %s',
                    Caster::getInstance()->castTyped($collectionA),
                    Caster::getInstance()->castTyped($collectionB),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(UnacceptableCollectionException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'Argument $collection = %s must be an instance of %s, but it is not',
                    Caster::getInstance()->castTyped($collectionB),
                    Caster::makeNormalizedClassName(new ReflectionObject($collectionA)),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithMergedThrowsExceptionWhenArgumentCollectionContainsInvalidElements(): void
    {
        $collectionA = new IntegerCollection();
        $collectionB = $this->createMock(IntegerCollection::class);

        $collectionB
            ->expects($this->once())
            ->method('toArray')
            ->willReturn([true]);

        $collectionB
            ->expects($this->once())
            ->method('count')
            ->willReturn(7);

        try {
            $collectionA->withMerged($collectionB);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(UnacceptableCollectionException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'The current collection, %s, cannot be merged with argument $collection = %s',
                    Caster::getInstance()->castTyped($collectionA),
                    Caster::getInstance()->castTyped($collectionB),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(UnacceptableElementException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    '1/7 elements are invalid, including: [0 => %s]',
                    Caster::getInstance()->castTyped(true),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithMergedHandlesExceptionGracefully(): void
    {
        $collectionA = new IntegerCollection();
        $collectionB = $this->createMock(IntegerCollection::class);
        $exception = $this->createMock(Exception::class);

        $collectionB
            ->expects($this->once())
            ->method('toArray')
            ->willThrowException($exception);

        try {
            $collectionA->withMerged($collectionB);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertSame(
                ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                    $collectionA,
                    new ReflectionMethod($collectionA, 'withMerged'),
                    [$collectionB],
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame($exception, $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithRemovedElementThrowsExceptionWhenArgumentElementIsNotAcceptedByCollection(): void
    {
        $collection = new class ([true, 42, 'foo' => 'bar']) extends Collection
        {
            /**
             * {@inheritDoc}
             *
             * @override
             */
            public static function isElementAccepted(mixed $element): bool
            {
                return null !== $element;
            }
        };

        try {
            $collection->withRemovedElement(null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(UnacceptableElementException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'Argument $element = %s cannot be removed from the current collection, %s',
                    Caster::getInstance()->castTyped(null),
                    Caster::getInstance()->castTyped($collection),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(UnacceptableElementException::class, $currentException::class);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithRemovedElementHandlesExceptionGracefully(): void
    {
        $collection = new class ([true, 42, 'foo' => 'bar']) extends Collection
        {
            public function __clone()
            {
                throw new Exception('FAIL');
            }
        };

        try {
            $collection->withRemovedElement(null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertSame(
                ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                    $collection,
                    new ReflectionMethod($collection, 'withRemovedElement'),
                    [null],
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(Exception::class, $currentException::class);
            $this->assertSame('FAIL', $currentException->getMessage());

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithSetElementThrowsExceptionWhenArgumentElementIsNotAcceptedByCollection(): void
    {
        $collection = new class ([true, 42, 'foo' => 'bar']) extends Collection
        {
            /**
             * {@inheritDoc}
             *
             * @override
             */
            public static function isElementAccepted(mixed $element): bool
            {
                return null !== $element;
            }
        };

        try {
            $collection->withSet(0, null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(UnacceptableElementException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'Argument $element = %s (with $key = %s) cannot be set on the current collection, %s',
                    Caster::getInstance()->castTyped(null),
                    Caster::getInstance()->castTyped(0),
                    Caster::getInstance()->castTyped($collection),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(UnacceptableElementException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'Argument $element = %s is not accepted by %s',
                    Caster::getInstance()->castTyped(null),
                    Caster::makeNormalizedClassName(new ReflectionObject($collection)),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithSetElementHandlesExceptionGracefully(): void
    {
        $collection = new class ([true, 42, 'foo' => 'bar']) extends Collection
        {
            public function __clone()
            {
                throw new Exception('FAIL');
            }
        };

        try {
            $collection->withSet(0, null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertSame(
                ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                    $collection,
                    new ReflectionMethod($collection, 'withSet'),
                    [0, null],
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(Exception::class, $currentException::class);
            $this->assertSame('FAIL', $currentException->getMessage());

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testAssertIsElementAcceptedWorks(): void
    {
        Collection::assertIsElementAccepted(null);
        Collection::assertIsElementAccepted(true);
        Collection::assertIsElementAccepted(42);
        Collection::assertIsElementAccepted('bar');

        $collection = new class ([]) extends Collection
        {
            /**
             * {@inheritDoc}
             *
             * @override
             */
            public static function isElementAccepted(mixed $element): bool
            {
                return false;
            }
        };

        try {
            $collection::assertIsElementAccepted(null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(UnacceptableElementException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'Argument $element = %s is not accepted by %s',
                    Caster::getInstance()->castTyped(null),
                    Caster::makeNormalizedClassName(new ReflectionObject($collection)),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testMakeInvalidsWorks(): void
    {
        $this->assertSame([], Collection::makeInvalids([null]));
        $this->assertSame([], Collection::makeInvalids([null, true]));
        $this->assertSame([], Collection::makeInvalids([null, true, 42]));
        $this->assertSame([], Collection::makeInvalids([null, true, 42, 'foo' => 'bar']));

        $collection = new class ([]) extends Collection
        {
            /**
             * {@inheritDoc}
             *
             * @override
             */
            public static function isElementAccepted(mixed $element): bool
            {
                return false;
            }
        };

        $this->assertSame(
            [
                0 => '0 => (null) null',
                1 => '1 => (bool) true',
                2 => '2 => (int) 42',
                3 => '"foo" => (string(3)) "bar"',
            ],
            $collection::makeInvalids([null, true, 42, 'foo' => 'bar']),
        );
    }

    public function testIsElementAcceptedWorks(): void
    {
        $this->assertTrue(Collection::isElementAccepted(null));
        $this->assertTrue(Collection::isElementAccepted(true));
        $this->assertTrue(Collection::isElementAccepted(42));
        $this->assertTrue(Collection::isElementAccepted('bar'));

        $collection = new class ([]) extends Collection
        {
            /**
             * {@inheritDoc}
             *
             * @override
             */
            public static function isElementAccepted(mixed $element): bool
            {
                return false;
            }
        };

        $this->assertFalse($collection::isElementAccepted(null));
        $this->assertFalse($collection::isElementAccepted(true));
        $this->assertFalse($collection::isElementAccepted(42));
        $this->assertFalse($collection::isElementAccepted('bar'));
    }

    public function testEachHandlesExceptionGracefullyWhenAFailureHappensInsideTheCallback(): void
    {
        $collection = new Collection([null]);
        $exception = new Exception('foo');

        try {
            $collection->each(static function () use ($exception): void {
                throw $exception;
            });
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>each\(',
                            '\$callback = \(object\) \\\\Closure\(\): void',
                            ', \$carry = \(null\) null',
                        '\) inside \(object\) \\\\%s \{',
                            '\$elements = \(array\(1\)\) \[.+\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    'Failure when calling \$callback\(\(null\) null, \(int\) 0, \(null\) null\)',
                    '$',
                    '/',
                ]),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame($exception, $currentException);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    /**
     * @param array<int, string> $expected
     * @param array<int|string, mixed> $elements
     */
    #[DataProvider('providerTestEveryWorks')]
    public function testEveryWorks(array $expected, array $elements, Closure $callback): void
    {
        $collection = new Collection($elements);

        $carry = new stdClass();
        $carry->results = [];

        $collection->every($callback, $carry);

        $this->assertSame($expected, $carry->results);
    }

    public function testEveryHandlesExceptionGracefullyWhenAFailureHappensInsideTheCallback(): void
    {
        $collection = new Collection([null]);
        $exception = new Exception('foo');

        try {
            $collection->every(static function () use ($exception): void {
                throw $exception;
            });
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>every\(',
                            '\$callback = \(object\) \\\\Closure\(\): void',
                            ', \$carry = \(null\) null',
                        '\) inside \(object\) \\\\%s \{',
                            '\$elements = \(array\(1\)\) \[.+\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    'Failure when calling \$callback\(\(null\) null, \(int\) 0, \(null\) null\)',
                    '$',
                    '/',
                ]),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame($exception, $currentException);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testEveryThrowsExceptionWhenReturnValueOfArgumentCallbackIsInvalid(): void
    {
        $collection = new Collection([null]);

        try {
            $collection->every(
                // @phpstan-ignore-next-line
                static function (): int {
                    return 42;
                }
            );
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>every\(',
                            '\$callback = \(object\) \\\\Closure\(\)(: *[^,\)]+)?',
                            ', \$carry = \(null\) null',
                        '\) inside \(object\) \\\\%s \{',
                            '\$elements = \(array\(1\)\) \[.+\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    'Call \$callback\(\(null\) null, \(int\) 0, \(null\) null\) must return void, `null`, `false`',
                    ', or `true`, but it did not\. Found return value\: \(int\) 42',
                    '$',
                    '/',
                ]),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testFindReturnsNullWhenThereAreNoElementsInCollection(): void
    {
        $collection = new Collection();

        $this->assertNull( // @phpstan-ignore-line
            $collection->find(
                static function () {
                    return false;
                },
            ),
        );
    }

    public function testFindWorksWithTypeHintedValueAndKey(): void
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertSame(
            'baz',
            $collection->find(static function (string $v, int|string $k): bool {
                return 2 === $k;
            }),
        );
    }

    public function testFirstReturnsNullWhenThereAreNoElementsInCollection(): void
    {
        $collection = new Collection();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertNull($collection->first()); // @phpstan-ignore-line
    }

    public function testFindOrFailThrowsExceptionWhenArgumentCallbackDoesNotReturnABooleanWhenCalled(): void
    {
        $collection = new Collection([null]);

        try {
            $collection->findOrFail(
                static function (): int {
                    return 42;
                }
            );
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>findOrFail\(',
                            '\$callback = \(object\) \\\\Closure\(\)(: *[^,\)]+)?',
                        '\) inside \(object\) \\\\%s \{',
                            '\$elements = \(array\(1\)\) \[.+\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Call \$callback\(\(null\) null, \(int\) 0\) did not return a boolean, which it must\.',
                    ' Found return value\: \(int\) 42',
                    '$',
                    '/',
                ]),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testIndexOfThrowsExceptionWhenArgumentElementIsNotAcceptedByCollection(): void
    {
        $collection = new class extends Collection
        {
            /**
             * {@inheritDoc}
             *
             * @override
             */
            public static function isElementAccepted(mixed $element): bool
            {
                return null !== $element;
            }
        };

        try {
            $collection->indexOf(null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(UnacceptableElementException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'Argument $element = %s is not accepted by %s',
                    Caster::getInstance()->castTyped(null),
                    Caster::makeNormalizedClassName(new ReflectionObject($collection)),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testMaxByCallbackHandlesExceptionGracefullyWhenAFailureOccursInsideTheCallback(): void
    {
        $collection = new Collection([null]);
        $exception = new Exception();

        try {
            $collection->maxByCallback(static function () use ($exception): Exception {
                throw $exception;
            });
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>maxByCallback\(',
                            '\$callback = \(object\) \\\\Closure\(\): Exception',
                        '\) inside \(object\) \\\\%s \{',
                            '\$elements = \(array\(1\)\) \[.+\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Failure when calling \$callback\(\(null\) null, \(int\) 0\)',
                    '$',
                    '/',
                ]),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame($exception, $currentException);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testMaxByCallbackThrowsExceptionWhenCallbackDoesNotReturnAnInteger(): void
    {
        $collection = new Collection([null]);

        try {
            $collection->maxByCallback(
                // @phpstan-ignore-next-line
                static function (): null {
                    return null;
                }
            );
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>maxByCallback\(',
                            '\$callback = \(object\) \\\\Closure\(\)(: *[^,\)]+)?',
                        '\) inside \(object\) \\\\%s \{',
                            '\$elements = \(array\(1\)\) \[.+\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Call \$callback\(\(null\) null, \(int\) 0\) must return int, but it did not\.',
                    ' Found return value\: \(null\) null',
                    '$',
                    '/',
                ]),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testMinByCallbackHandlesExceptionGracefullyWhenAFailureOccursInsideTheCallback(): void
    {
        $collection = new Collection([null]);
        $exception = new Exception();

        try {
            $collection->minByCallback(static function () use ($exception): Exception {
                throw $exception;
            });
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>minByCallback\(',
                            '\$callback = \(object\) \\\\Closure\(\): Exception',
                        '\) inside \(object\) \\\\%s \{',
                            '\$elements = \(array\(1\)\) \[.+\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Failure when calling \$callback\(\(null\) null, \(int\) 0\)',
                    '$',
                    '/',
                ]),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame($exception, $currentException);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testMinByCallbackThrowsExceptionWhenCallbackDoesNotReturnAnInteger(): void
    {
        $collection = new Collection([null]);

        try {
            $collection->minByCallback(
                // @phpstan-ignore-next-line
                static function (): null {
                    return null;
                }
            );
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>minByCallback\(',
                            '\$callback = \(object\) \\\\Closure\(\)(: *[^,\)]+)?',
                        '\) inside \(object\) \\\\%s \{',
                            '\$elements = \(array\(1\)\) \[.+\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Call \$callback\(\(null\) null, \(int\) 0\) must return int, but it did not\.',
                    ' Found return value\: \(null\) null',
                    '$',
                    '/',
                ]),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testLastReturnsNullWhenThereAreNoElementsInCollection(): void
    {
        $collection = new Collection();

        $this->assertNull($collection->last()); // @phpstan-ignore-line
    }
}
