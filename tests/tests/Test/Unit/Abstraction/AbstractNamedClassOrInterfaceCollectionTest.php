<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Abstraction;

use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Collection;
use Eboreum\Collections\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class AbstractNamedClassOrInterfaceCollectionTest extends TestCase
{
    public function testConstructorThrowsExceptionWhenHandledClassNameIsNotAnExistingClass(): void
    {
        try {
            new class ([]) extends AbstractNamedClassOrInterfaceCollection
            {
                /**
                 * {@inheritDoc}
                 */
                public static function getHandledClassName(): string
                {
                    /**
                     * We use "phpstan-ignore-line", because it is intentional that this class does not exist. That is
                     * exactly what we test for.
                     */
                    return 'IDontExist6f27c77df211460d95103a19491ac2dc'; // @phpstan-ignore-line
                }
            };
        } catch (\Exception $e) { // @phpstan-ignore-line
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>__construct\(',
                            '\$elements = \(array\(0\)\) \[\]',
                        '\) inside \(object\) class@anonymous\/in\/.+\/%s:+\d+ \{',
                            '\\\\%s\-\>\$elements = \(uninitialized\)',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(AbstractNamedClassOrInterfaceCollection::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Collection class@anonymous\/in\/.+\/%s:\d+ has handled class ',
                        '\\\\IDontExist6f27c77df211460d95103a19491ac2dc, but said handled class does not exist',
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

    public function testConstructorThrowsExceptionWhenArgumentElementContainsInvalidValues(): void
    {
        $elements = [
            new \stdClass(),
            new \DateTime('2021-01-01T00:00:00+00:00'),
            new \stdClass(),
            'foo' => new \DateTimeImmutable('2021-01-01T00:00:00+00:00'),
        ];

        try {
            new class ($elements) extends AbstractNamedClassOrInterfaceCollection
            {
                /**
                 * {@inheritDoc}
                 */
                public static function getHandledClassName(): string
                {
                    return 'stdClass';
                }
            };
        } catch (\Exception $e) { // @phpstan-ignore-line
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>__construct\(',
                            '\$elements = \(array\(4\)\) \[',
                                '\(int\) 0 => \(object\) \\\\stdClass',
                                ', \(int\) 1 => \(object\) \\\\DateTime \("2021\-01\-01T00\:00\:00\+00\:00"\)',
                                ', \(int\) 2 => \(object\) \\\\stdClass',
                                ', \.\.\. and 1 more element',
                            '\] \(sample\)',
                        '\) inside \(object\) class@anonymous\/in\/.+\/%s:+\d+ \{',
                            '\\\\%s\-\>\$elements = \(uninitialized\)',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(AbstractNamedClassOrInterfaceCollection::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'In argument \$elements, 2\/4 elements are invalid, including: \[',
                            '1 \=\> \(object\) \\\\DateTime \("2021\-01\-01T00\:00\:00\+00\:00"\)',
                            ', "foo" \=\> \(object\) \\\\DateTimeImmutable \("2021\-01\-01T00\:00\:00\+00\:00"\)',
                        '\]',
                        '$',
                        '/',
                    ]),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithMergedThrowsExceptionWhenArgumentCollectionDoesNotHaveASuitableHandledClassName(): void
    {
        $elementsA = [
            new \stdClass(),
            new \stdClass(),
        ];

        $collectionA = new class ($elementsA) extends AbstractNamedClassOrInterfaceCollection
        {
            /**
             * {@inheritDoc}
             */
            public static function getHandledClassName(): string
            {
                return 'stdClass';
            }
        };

        $elementsB = [
            new \DateTimeImmutable('2021-01-01T00:00:00+00:00'),
            new \DateTimeImmutable('2021-01-01T00:00:00+00:00'),
        ];

        $collectionB = new class ($elementsB) extends AbstractNamedClassOrInterfaceCollection
        {
            /**
             * {@inheritDoc}
             */
            public static function getHandledClassName(): string
            {
                return 'DateTimeImmutable';
            }
        };

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
                        'Failure in \\\\%s-\>withMerged\(',
                            '\$collection = \(object\) class@anonymous\/in\/.+\/%s\:\d+ \{.+\}',
                        '\) inside \(object\) class@anonymous\/in\/.+\/%s\:+\d+ \{',
                            '\\\\%s\-\>\$elements = \(array\(2\)\) \[.+\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
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
                        'Argument \$collection must be an instance of class@anonymous\/in\/.+\/%s\:\d+',
                        ', but it is not\.',
                        ' Found\: \(object\) class@anonymous\/in\/.+\/%s\:\d+ \{',
                            '\\\\%s\-\>\$elements = \(array\(2\)\) \[.+\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
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
}
