<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use Eboreum\Collections\Caster;
use Eboreum\Collections\Collection;
use Eboreum\Collections\Exception\InvalidArgumentException;
use Eboreum\Collections\Exception\RuntimeException;

abstract class AbstractTypeCollectionTestCase extends AbstractCollectionTestCase
{
    public function testWithAddedThrowsExceptionWhenArgumentElementIsNotAccepted(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();

        $collection = new $handledCollectionClassName($elements);

        assert(is_a($collection, Collection::class)); // Make phpstan happy

        try {
            $collection->withAdded(null);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(InvalidArgumentException::class, get_class($currentException));
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
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $elements = $this->createMultipleElements();

        $collection = new $handledCollectionClassName($elements);

        assert(is_a($collection, Collection::class)); // Make phpstan happy

        try {
            $collection->withAddedMultiple([null]);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $collectionA = new $handledCollectionClassName();
        $collectionB = new Collection();

        assert(is_a($collectionA, Collection::class)); // Make phpstan happy

        try {
            $collectionA->withMerged($collectionB);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
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

    public function testWithMergedThrowsExceptionWhenArgumentCollectionBIsASubclassOfCollectionAButElementIsNotAcceptedByCollectionA(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $collectionA = new $handledCollectionClassName();
        $collectionB = new $handledCollectionClassName();

        $reflectionObjectCurrent = new \ReflectionObject($collectionB);
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

        assert(is_a($collectionA, Collection::class)); // Make phpstan happy
        assert(is_a($collectionB, Collection::class)); // Make phpstan happy

        try {
            $collectionA->withMerged($collectionB);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        assert(is_a($collection, Collection::class)); // Make phpstan happy

        try {
            $collection->withRemovedElement(null);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(InvalidArgumentException::class, get_class($currentException));
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
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        assert(is_a($collection, Collection::class)); // Make phpstan happy

        try {
            $collection->withSet(0, null);
        } catch (\Exception $e) {
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
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
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
