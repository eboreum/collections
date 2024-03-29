<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use Closure;
use Eboreum\Collections\Collection;
use Eboreum\Collections\Contract\CollectionInterface\ToReindexedDuplicateKeyBehaviorEnum;
use Eboreum\Collections\Exception\InvalidArgumentException;
use Eboreum\Collections\Exception\RuntimeException;
use Exception;
use stdClass;

class CollectionTest extends AbstractCollectionTestCase
{
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
            new class ($elements) extends Collection
            {
                /**
                 * {@inheritDoc}
                 */
                public static function isElementAccepted($element): bool
                {
                    return is_string($element);
                }
            };
        } catch (Exception $e) { // @phpstan-ignore-line
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            public static function isElementAccepted($element): bool
            {
                return false;
            }
        };

        try {
            $collectionA->contains(null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(InvalidArgumentException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Argument \$element is not accepted by \\\\%s@anonymous\/in\/.+\/%s\:\d+\.',
                        ' Found\: \(null\) null',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
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

    public function testCurrentReturnsNullWhenThereAreNoElementsInCollection(): void
    {
        $collection = new Collection();

        $this->assertNull($collection->current());
    }

    /**
     * @dataProvider dataProvider_testEachWorks
     * @param array<int, string> $expected
     * @param array<int, mixed> $elements
     */
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
            $collection->toReindexed(static function ($element) {
                return $element;
            });
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            $collection->toReindexed(static function ($element) {
                return $element;
            });
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            public static function isElementAccepted($element): bool
            {
                return null !== $element;
            }
        };

        try {
            $collection->withAdded(null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>withAdded\(',
                            '\$element = \(null\) null',
                        '\) inside \(object\) \\\\%s@anonymous\/in\/.+\/%s\:+\d+ \{',
                            '\\\\%s\-\>\$elements = \(array\(3\)\) \[.+\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(InvalidArgumentException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Argument \$element is not accepted by \\\\%s@anonymous\/in\/.+\/%s\:\d+\.',
                        ' Found\: \(null\) null',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
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
            public static function isElementAccepted($element): bool
            {
                return null !== $element;
            }
        };

        try {
            $collection->withAddedMultiple([null, 3.1415]);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>withAddedMultiple\(',
                            '\$elements = \(array\(2\)\) \[.+\]',
                        '\) inside \(object\) \\\\%s@anonymous\/in\/.+\/%s\:+\d+ \{',
                            '\\\\%s\-\>\$elements = \(array\(3\)\) \[.+\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'In argument \$elements, 1\/2 elements are invalid, including\: \[',
                        '0 \=\> \(null\) null',
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

    public function testWithFilteredHandlesExceptionGracefullyWhenAnExceptionIsThrownInsideTheClosure(): void
    {
        $collection = new Collection([null, true, 42, 'foo' => 'bar']);

        try {
            $collection->withFiltered(static function ($v): void {
                throw new Exception('fail');
            });
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame('Exception', get_class($currentException));
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
            public static function isElementAccepted($element): bool
            {
                return null !== $element;
            }
        };

        $collectionB = new Collection([null]);

        try {
            $collectionA->withMerged($collectionB); // @phpstan-ignore-line
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s\-\>withMerged\(',
                            '\$collection = \(object\) \\\\%s \{.+\}',
                        '\) inside \(object\) \\\\%s@anonymous\/in\/.+\/%s\:\d+ \{',
                            '\\\\%s\-\>\$elements = \(array\(3\)\) \[.+\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Argument \$collection must be an instance of \\\\%s@anonymous\/in\/.+\/%s\:\d+',
                        ', but it is not\. Found\: \(object\) \\\\%s \{',
                            '\$elements \= \(array\(1\)\) \[\(int\) 0 \=\> \(null\) null\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithRemovedThrowsExceptionWhenArgumentKeyIsInvalid(): void
    {
        $collection = new Collection([null, true, 42, 'foo' => 'bar']);

        try {
            $collection->withRemoved(null); // @phpstan-ignore-line
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(InvalidArgumentException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Argument \$key must be int or string, but it is not\.',
                    ' Found\: \(null\) null',
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

    public function testWithRemovedElementThrowsExceptionWhenArgumentElementIsNotAcceptedByCollection(): void
    {
        $collection = new class ([true, 42, 'foo' => 'bar']) extends Collection
        {
            /**
             * {@inheritDoc}
             *
             * @override
             */
            public static function isElementAccepted($element): bool
            {
                return null !== $element;
            }
        };

        try {
            $collection->withRemovedElement(null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s\-\>withRemovedElement\(',
                            '\$element = \(null\) null',
                        '\) inside \(object\) \\\\%s@anonymous\/in\/.+\/%s\:\d+ \{',
                            '\\\\%s\-\>\$elements = \(array\(3\)\) \[.+\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(InvalidArgumentException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Argument \$element is not accepted by \\\\%s@anonymous\/in\/.+\/%s\:\d+\.',
                        ' Found\: \(null\) null',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
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

    public function testWithSetElementThrowsExceptionWhenArgumentKeyIsInvalid(): void
    {
        $collection = new Collection([true, 42, 'foo' => 'bar']);

        try {
            $collection->withSet(null, null); // @phpstan-ignore-line
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s\-\>withSet\(',
                            '\$key = \(null\) null',
                            ', \$element = \(null\) null',
                        '\) inside \(object\) \\\\%s \{',
                            '\$elements = \(array\(3\)\) \[.+\]',
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Argument \$key must be int or string, but it is not\.',
                    ' Found\: \(null\) null',
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

    public function testWithSetElementThrowsExceptionWhenArgumentElementIsNotAcceptedByCollection(): void
    {
        $collection = new class ([true, 42, 'foo' => 'bar']) extends Collection
        {
            /**
             * {@inheritDoc}
             *
             * @override
             */
            public static function isElementAccepted($element): bool
            {
                return null !== $element;
            }
        };

        try {
            $collection->withSet(0, null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s\-\>withSet\(',
                            '\$key = \(int\) 0',
                            ', \$element = \(null\) null',
                        '\) inside \(object\) \\\\%s@anonymous\/in\/.+\/%s\:\d+ \{',
                            '\\\\%s\-\>\$elements = \(array\(3\)\) \[.+\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Argument \$element is not accepted by \\\\%s@anonymous\/in\/.+\/%s\:\d+\.',
                        ' Found\: \(null\) null',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
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
            public static function isElementAccepted($element): bool
            {
                return false;
            }
        };

        try {
            $collection::assertIsElementAccepted(null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(InvalidArgumentException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Argument \$element is not accepted by \\\\%s@anonymous\/in\/.+\/%s\:\d+\.',
                        ' Found\: \(null\) null',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
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
            public static function isElementAccepted($element): bool
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
            public static function isElementAccepted($element): bool
            {
                return false;
            }
        };

        $this->assertFalse($collection::isElementAccepted(null));
        $this->assertFalse($collection::isElementAccepted(true));
        $this->assertFalse($collection::isElementAccepted(42));
        $this->assertFalse($collection::isElementAccepted('bar'));
    }

    /**
     * @return array<int, array{array<int, string>, array<int|string, mixed>, Closure}>
     */
    public function dataProvider_testEachWorks(): array
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
                static function ($v, $k, object $carry): void {
                    $carry->results[] = sprintf( // @phpstan-ignore-line
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
                static function ($v, $k, object $carry) {
                    if (2 === $k) {
                        return false;
                    }

                    $carry->results[] = sprintf( // @phpstan-ignore-line
                        '%s:%s',
                        gettype($k),
                        gettype($v),
                    );
                },
            ],
        ];
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
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame($exception, $currentException);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    /**
     * @dataProvider dataProvider_testEveryWorks
     * @param array<int, string> $expected
     * @param array<int|string, mixed> $elements
     */
    public function testEveryWorks(array $expected, array $elements, Closure $callback): void
    {
        $collection = new Collection($elements);

        $carry = new stdClass();
        $carry->results = [];

        $collection->every($callback, $carry);

        $this->assertSame($expected, $carry->results);
    }

    /**
     * @return array<int, array{array<int, string>, array<int|string, mixed>, Closure}>
     */
    public function dataProvider_testEveryWorks(): array
    {
        return [
            [
                [
                    'integer:NULL',
                    'integer:boolean',
                ],
                [null, true, 42, 'foo' => 'bar'],
                static function ($v, $k, object $carry) {
                    if (2 === $k) {
                        return false;
                    }

                    $carry->results[] = sprintf( // @phpstan-ignore-line
                        '%s:%s',
                        gettype($k),
                        gettype($v),
                    );
                },
            ],
            [
                ['integer:NULL'],
                [null],
                static function ($v, $k, object $carry) {
                    $carry->results[] = sprintf( // @phpstan-ignore-line
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
                static function ($v, $k, object $carry) {
                    $carry->results[] = sprintf( // @phpstan-ignore-line
                        '%s:%s',
                        gettype($k),
                        gettype($v),
                    );

                    return null;
                },
            ],
        ];
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
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
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
                static function () { // @phpstan-ignore-line This is specifically what we are testing for
                    return 42;
                }
            );
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>every\(',
                            '\$callback = \(object\) \\\\Closure\(\)',
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
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

        $this->assertNull($collection->find(static function () {
            return false;
        }));
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

    public function testFindThrowsExceptionWhenArgumentCallbackDoesNotReturnABooleanWhenCalled(): void
    {
        $collection = new Collection([null]);

        try {
            $collection->find(
                static function () { // @phpstan-ignore-line This is specifically what we are testing for
                    return 42;
                }
            );
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>find\(',
                            '\$callback = \(object\) \\\\Closure\(\)',
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
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

    public function testFirstReturnsNullWhenThereAreNoElementsInCollection(): void
    {
        $collection = new Collection();

        $this->assertNull($collection->first());
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
            public static function isElementAccepted($element): bool
            {
                return null !== $element;
            }
        };

        try {
            $collection->indexOf(null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(InvalidArgumentException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Argument \$element is not accepted by \\\\%s@anonymous\/in\/.+\/%s\:\\d+\.',
                        ' Found\: \(null\) null',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
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
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
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
                static function () { // @phpstan-ignore-line This is specifically what we are testing for
                    return null;
                }
            );
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>maxByCallback\(',
                            '\$callback = \(object\) \\\\Closure\(\)',
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
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
                static function () { // @phpstan-ignore-line This is specifically what we are testing for
                    return null;
                }
            );
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>minByCallback\(',
                            '\$callback = \(object\) \\\\Closure\(\)',
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
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

        $this->assertNull($collection->last());
    }

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
            [
                '1 single item collection.',
                [$this->createSingleElement()],
                [$this->createSingleElement()],
                static function (): string {
                    return '';
                },
                true,
            ],
            [
                'Integer item collection, ascending, use first encountered.',
                [0 => 1, 1 => 2, 3 => 3, 5 => 4],
                [1, 2, 1, 3, 1, 4],
                static function (int $value): string {
                    return strval($value);
                },
                true,
            ],
            [
                'Integer item collection, ascending, use last encountered.',
                [1 => 2, 3 => 3, 4 => 1, 5 => 4],
                [1, 2, 1, 3, 1, 4],
                static function (int $value): string {
                    return strval($value);
                },
                false,
            ],
            [
                'Integer item collection, descending, use first encountered.',
                [0 => 4, 1 => 1, 2 => 3, 4 => 2],
                [4, 1, 3, 1, 2, 1],
                static function (int $value): string {
                    return strval($value);
                },
                true,
            ],
            [
                'Integer item collection, descending, use last encountered.',
                [0 => 4, 2 => 3, 4 => 2, 5 => 1],
                [4, 1, 3, 1, 2, 1],
                static function (int $value): string {
                    return strval($value);
                },
                false,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @return array<int, array{string, Collection<mixed>, Collection<mixed>, Closure: void}>
     */
    public function dataProvider_testWithMergedWorks(): array
    {
        // @phpstan-ignore-next-line Returned values are 100% correct, but phpstan still reports an error. False positive?
        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                new Collection([0 => 3.1415, 1 => null]),
                new Collection([0 => 2.7182, 1 => 42]),
                function (
                    Collection $collectionA,
                    Collection $collectionB,
                    Collection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(4, $collectionC, $message);
                    $this->assertSame(
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
                new Collection(['foo' => 3.1415, 1 => null]),
                new Collection(['foo' => 2.7182, 1 => 42]),
                function (
                    Collection $collectionA,
                    Collection $collectionB,
                    Collection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(3, $collectionC, $message);
                    $this->assertSame(
                        [
                            'foo' => 2.7182,
                            0 => null,
                            1 => 42
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
    protected function createMultipleElements(): array
    {
        return [
            42,
            'foo' => 3.1415,
            42 => null,
            true,
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function createSingleElement()
    {
        return 42;
    }

    /**
     * {@inheritDoc}
     */
    protected function getHandledCollectionClassName(): string
    {
        return Collection::class;
    }
}
