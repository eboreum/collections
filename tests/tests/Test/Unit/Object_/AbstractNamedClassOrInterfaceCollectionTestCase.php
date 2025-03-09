<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Collection;
use Eboreum\Collections\Exception\InvalidArgumentException;
use Eboreum\Collections\Exception\RuntimeException;
use Exception;
use Test\Unit\Eboreum\Collections\AbstractTypeCollectionTestCase;

use function implode;
use function preg_quote;
use function sprintf;

/**
 * @template T of object
 * @template TCollection of Collection<mixed>
 * @extends AbstractTypeCollectionTestCase<T, TCollection>
 */
abstract class AbstractNamedClassOrInterfaceCollectionTestCase extends AbstractTypeCollectionTestCase
{
    /**
     * @override
     */
    public function testWithAddedThrowsExceptionWhenArgumentElementIsNotAccepted(): void
    {
        /** @var class-string<AbstractNamedClassOrInterfaceCollection<T>> $handledCollectionClassName */
        $handledCollectionClassName = static::getHandledCollectionClassName();

        /** @var class-string<T> $handledClassName */
        $handledClassName = $handledCollectionClassName::getHandledClassName();

        $elements = static::createMultipleElements($this);

        /** @var TCollection<T> $collection */
        $collection = new $handledCollectionClassName($elements);

        try {
            $collection->withAdded(null); // @phpstan-ignore-line
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
        /** @var class-string<AbstractNamedClassOrInterfaceCollection<T>> $handledCollectionClassName */
        $handledCollectionClassName = static::getHandledCollectionClassName();

        /** @var class-string<T> $handledClassName */
        $handledClassName = $handledCollectionClassName::getHandledClassName();

        /** @var TCollection<T> $collection */
        $collection = new $handledCollectionClassName();

        try {
            $collection->withRemovedElement(null); // @phpstan-ignore-line
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
