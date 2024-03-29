<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Eboreum\Collections\Collection;
use Eboreum\Collections\Exception\InvalidArgumentException;
use Eboreum\Collections\Exception\RuntimeException;
use Exception;
use Test\Unit\Eboreum\Collections\AbstractTypeCollectionTestCase;

abstract class AbstractNamedClassOrInterfaceCollectionTestCase extends AbstractTypeCollectionTestCase
{
    /**
     * @override
     */
    public function testWithAddedThrowsExceptionWhenArgumentElementIsNotAccepted(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $handledClassName = $handledCollectionClassName::getHandledClassName();
        $elements = $this->createMultipleElements();

        $collection = new $handledCollectionClassName($elements);

        assert(is_a($collection, Collection::class)); // Make phpstan happy

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
                        'Expects argument \$element to be an object, instance of \\\\%s, but it is not\.',
                        ' Found: \(null\) null',
                        '$',
                        '/',
                    ]),
                    preg_quote($handledClassName, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    /**
     * @override
     */
    public function testWithRemovedElementThrowsExceptionWhenArgumentElementIsNotAcceptedbyTheCollection(): void
    {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $handledClassName = $handledCollectionClassName::getHandledClassName();
        $collection = new $handledCollectionClassName();

        assert(is_a($collection, Collection::class)); // Make phpstan happy

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
                        'Expects argument \$element to be an object, instance of \\\\%s, but it is not\.',
                        ' Found\: \(null\) null',
                        '$',
                        '/',
                    ]),
                    preg_quote($handledClassName, '/'),
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
