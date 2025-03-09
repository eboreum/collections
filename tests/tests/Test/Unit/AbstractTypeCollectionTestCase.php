<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use Eboreum\Collections\Caster;
use Eboreum\Collections\Collection;
use Eboreum\Collections\Exception\InvalidArgumentException;
use Eboreum\Collections\Exception\RuntimeException;
use Exception;
use ReflectionObject;

use function implode;
use function preg_quote;
use function sprintf;

/**
 * @template T of mixed
 * @template TCollection of Collection<mixed>
 * @extends AbstractCollectionTestCase<T, TCollection>
 */
abstract class AbstractTypeCollectionTestCase extends AbstractCollectionTestCase
{
    public function testWithAddedThrowsExceptionWhenArgumentElementIsNotAccepted(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);

        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        try {
            $collection->withAdded(null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>withAdded\(',
                            '\$element = \(null\) null',
                        '\) inside \(object\) \\\\%s \{',
                            '\\\\%s\-\>\$elements = \(array\(4\)\) \[.+, \.\.\. and 1 more element\] \(sample\)',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote($handledCollectionClassName, '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(InvalidArgumentException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Argument \$element is not accepted by \\\\%s\.',
                        ' Found: \(null\) null',
                        '$',
                        '/',
                    ]),
                    preg_quote($handledCollectionClassName, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithAddedMultipleThrowsExceptionWhenArgumentElementsContainsInvalidValues(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);

        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        try {
            $collection->withAddedMultiple([null]);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>withAddedMultiple\(',
                            '\$elements = \(array\(1\)\) \[',
                                '\(int\) 0 \=\> \(null\) null',
                            '\]',
                        '\) inside \(object\) \\\\%s \{',
                            '\\\\%s\-\>\$elements = \(array\(4\)\) \[.+, \.\.\. and 1 more element\] \(sample\)',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote($handledCollectionClassName, '/'),
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
                    'In argument \$elements, 1\/1 elements are invalid, including\: \[',
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

    public function testWithMergedThrowsExceptionWhenArgumentCollectionBIsNotASubclassOfCollectionA(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collectionA = new $handledCollectionClassName();
        $collectionB = new Collection();

        $this->assertInstanceOf(Collection::class, $collectionA);

        try {
            $collectionA->withMerged($collectionB);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s\-\>withMerged\(',
                            '\$collection = \(object\) \\\\%s \{.+\}',
                        '\) inside \(object\) \\\\%s \{',
                            '\\\\%s\-\>\$elements = \(array\(0\)\) \[\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Collection::class, '/'),
                    preg_quote($handledCollectionClassName, '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Argument \$collection must be an instance of \\\\%s, but it is not\.',
                        ' Found\: \(object\) \\\\%s \{\$elements = \(array\(0\)\) \[\]\}',
                        '$',
                        '/',
                    ]),
                    preg_quote($handledCollectionClassName, '/'),
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

    public function testWithMergedThrowsExceptionWhenArgumentCollectionBIsASubclassOfCollectionAButElementIsNotAcceptedByCollectionA(): void // phpcs:ignore
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collectionA = new $handledCollectionClassName();
        $collectionB = new $handledCollectionClassName();

        $reflectionObjectCurrent = new ReflectionObject($collectionB);
        $reflectionProperty = null;

        do {
            if ($reflectionObjectCurrent->hasProperty('elements')) {
                $reflectionProperty = $reflectionObjectCurrent->getProperty('elements');

                break;
            }

            $reflectionObjectCurrent = $reflectionObjectCurrent->getParentClass();
        } while ($reflectionObjectCurrent);

        if (!$reflectionProperty) {
            throw new RuntimeException(sprintf(
                'Somehow, a \ReflectionProperty could not be produced from $handledCollectionClassName = %s',
                Caster::getInstance()->castTyped($handledCollectionClassName),
            ));
        }

        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($collectionB, [null]);

        $this->assertInstanceOf(Collection::class, $collectionA);
        $this->assertInstanceOf(Collection::class, $collectionB);

        try {
            $collectionA->withMerged($collectionB);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s\-\>withMerged\(',
                            '\$collection = \(object\) \\\\%s \{.+\}',
                        '\) inside \(object\) \\\\%s \{',
                            '\\\\%s\-\>\$elements = \(array\(0\)\) \[\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote($handledCollectionClassName, '/'),
                    preg_quote($handledCollectionClassName, '/'),
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
                    'Argument \$collection cannot be merged into the current collection, because 1\/1 elements',
                    ' are invalid, including\: \[0 \=\> \(null\) null\]',
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

    public function testWithRemovedElementThrowsExceptionWhenArgumentElementIsNotAcceptedbyTheCollection(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->assertInstanceOf(Collection::class, $collection);

        try {
            $collection->withRemovedElement(null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s\-\>withRemovedElement\(',
                            '\$element = \(null\) null',
                        '\) inside \(object\) \\\\%s \{',
                            '\\\\%s\-\>\$elements = \(array\(0\)\) \[\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote($handledCollectionClassName, '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(InvalidArgumentException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Argument \$element is not accepted by \\\\%s\.',
                        ' Found\: \(null\) null',
                        '$',
                        '/',
                    ]),
                    preg_quote($handledCollectionClassName, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithSetThrowsExceptionWhenArgumentElementIsNotAcceptedbyTheCollection(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->assertInstanceOf(Collection::class, $collection);

        try {
            $collection->withSet(0, null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s\-\>withSet\(',
                            '\$key = \(int\) 0',
                            ', \$element = \(null\) null',
                        '\) inside \(object\) \\\\%s \{',
                            '\\\\%s\-\>\$elements = \(array\(0\)\) \[\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote($handledCollectionClassName, '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Argument \$element is not accepted by \\\\%s\. Found\: \(null\) null',
                        '$',
                        '/',
                    ]),
                    preg_quote($handledCollectionClassName, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }
}
