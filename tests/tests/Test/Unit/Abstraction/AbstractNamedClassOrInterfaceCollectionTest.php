<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Abstraction;

use DateTime;
use DateTimeImmutable;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Caster;
use Eboreum\Collections\Collection;
use Eboreum\Collections\Exception\RuntimeException;
use Eboreum\Collections\Exception\UnacceptableCollectionException;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

use function basename;
use function implode;
use function preg_quote;
use function sprintf;

#[CoversClass(AbstractNamedClassOrInterfaceCollection::class)]
class AbstractNamedClassOrInterfaceCollectionTest extends TestCase
{
    public function testConstructorThrowsExceptionWhenHandledClassNameIsNotAnExistingClass(): void
    {
        try {
            new class ([]) extends AbstractNamedClassOrInterfaceCollection // @phpstan-ignore-line
            {
                public static function getHandledClassName(): string
                {
                    /**
                     * We use "phpstan-ignore-line", because it is intentional that this class does not exist. That is
                     * exactly what we test for.
                     */
                    return 'IDontExist6f27c77df211460d95103a19491ac2dc'; // @phpstan-ignore-line
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
                        'Failure in \\\\%s-\>__construct\(\$elements = %s\) inside',
                        ' \(object\) \\\\%s@anonymous\/in\/.+\/%s:+\d+ \{',
                        "\n",
                        '    \\\\%s\-\>\$elements = \(uninitialized\)',
                        "\n",
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(AbstractNamedClassOrInterfaceCollection::class, '/'),
                    preg_quote(Caster::getInstance()->castTyped([]), '/'),
                    preg_quote(AbstractNamedClassOrInterfaceCollection::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Collection \\\\%s@anonymous\/in\/.+\/%s:\d+ has handled class ',
                        '\\\\IDontExist6f27c77df211460d95103a19491ac2dc, but said handled class does not exist',
                        '$',
                        '/',
                    ]),
                    preg_quote(AbstractNamedClassOrInterfaceCollection::class, '/'),
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
            0 => new stdClass(),
            1 => new DateTime('2021-01-01T00:00:00+00:00'),
            2 => new stdClass(),
            'foo' => new DateTimeImmutable('2021-01-01T00:00:00+00:00'),
        ];

        try {
            new class ($elements) extends AbstractNamedClassOrInterfaceCollection // @phpstan-ignore-line
            {
                public static function getHandledClassName(): string
                {
                    return 'stdClass';
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
                        'Failure in \\\\%s-\>__construct\(\$elements = %s\)',
                        ' inside \(object\) \\\\%s@anonymous\/in\/.+\/%s:+\d+ \{',
                        "\n",
                        '    \\\\%s\-\>\$elements = \(uninitialized\)',
                        "\n",
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(AbstractNamedClassOrInterfaceCollection::class, '/'),
                    preg_quote(Caster::getInstance()->castTyped($elements), '/'),
                    preg_quote(AbstractNamedClassOrInterfaceCollection::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>__construct\(',
                            '\$elements = %s',
                        '\) inside \(object\) \\\\%s@anonymous\/in\/.+\/%s:+\d+ \{',
                        "\n",
                        '    \\\\%s\-\>\$elements = \(uninitialized\)',
                        "\n",
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(Collection::class, '/'),
                    preg_quote(Caster::getInstance()->castTyped($elements), '/'),
                    preg_quote(AbstractNamedClassOrInterfaceCollection::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(Collection::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, $currentException::class);
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
            new stdClass(),
            new stdClass(),
        ];

        $collectionA = new class ($elementsA) extends AbstractNamedClassOrInterfaceCollection
        {
            public static function getHandledClassName(): string
            {
                return 'stdClass';
            }
        };

        $elementsB = [
            new DateTimeImmutable('2021-01-01T00:00:00+00:00'),
            new DateTimeImmutable('2021-01-01T00:00:00+00:00'),
        ];

        $collectionB = new class ($elementsB) extends AbstractNamedClassOrInterfaceCollection
        {
            public static function getHandledClassName(): string
            {
                return 'DateTimeImmutable';
            }
        };

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
            $this->assertSame(UnacceptableCollectionException::class, $currentException::class);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }
}
