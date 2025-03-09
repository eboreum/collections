<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use Closure;
use Collator;
use Eboreum\Collections\StringCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

use function strval;

/**
 * @template T of string
 * @template TCollection of StringCollection<T>
 * @extends AbstractTypeCollectionTestCase<T, TCollection>
 */
#[CoversClass(StringCollection::class)]
class StringCollectionTest extends AbstractTypeCollectionTestCase
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
                static function (): string {
                    return '';
                },
                true,
            ],
            [
                '1 single item collection.',
                static function (): array {
                    /** @var array<T> $elements */
                    $elements = ['foo'];

                    return [
                        $elements,
                        $elements,
                    ];
                },
                static function (): string {
                    return '';
                },
                true,
            ],
            [
                'Ascending, use first encountered.',
                static function (): array {
                    /** @var array<T> $expected */
                    $expected = [0 => 'a', 1 => 'b', 3 => 'c', 5 => 'd'];

                    /** @var array<T> $elements */
                    $elements = ['a', 'b', 'a', 'c', 'a', 'd'];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (string $value): string {
                    return strval($value);
                },
                true,
            ],
            [
                'Ascending, use last encountered.',
                static function (): array {
                    /** @var array<T> $expected */
                    $expected = [1 => 'b', 3 => 'c', 4 => 'a', 5 => 'd'];

                    /** @var array<T> $elements */
                    $elements = ['a', 'b', 'a', 'c', 'a', 'd'];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (string $value): string {
                    return strval($value);
                },
                false,
            ],
            [
                'Descending, use first encountered.',
                static function (): array {
                    /** @var array<T> $expected */
                    $expected = [0 => 'd', 1 => 'a', 2 => 'c', 4 => 'b'];

                    /** @var array<T> $elements */
                    $elements = ['d', 'a', 'c', 'a', 'b', 'a'];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (string $value): string {
                    return strval($value);
                },
                true,
            ],
            [
                'Descending, use last encountered.',
                static function (): array {
                    /** @var array<T> $expected */
                    $expected = [0 => 'd', 2 => 'c', 4 => 'b', 5 => 'a'];

                    /** @var array<T> $elements */
                    $elements = ['d', 'a', 'c', 'a', 'b', 'a'];

                    return [
                        $expected,
                        $elements,
                    ];
                },
                static function (string $value): string {
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
     *     TCollection<T>,
     *     TCollection<T>,
     *     Closure(self<T, TCollection>, TCollection<T>, TCollection<T>, TCollection<T>, string): void,
     *   },
     * >
     */
    public static function providerTestWithMergedWorks(): array
    {
        /** @var TCollection<T> $foo0 */
        $foo0 = new StringCollection([0 => 'foo']);

        /** @var TCollection<T> $bar0 */
        $bar0 = new StringCollection([0 => 'bar']);

        /** @var TCollection<T> $fooAssociative */
        $fooAssociative = new StringCollection(['foo' => 'foo']);

        /** @var TCollection<T> $barAssociative */
        $barAssociative = new StringCollection(['foo' => 'bar']);

        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                $foo0,
                $bar0,
                static function (
                    self $self,
                    StringCollection $collectionA,
                    StringCollection $collectionB,
                    StringCollection $collectionC,
                    string $message
                ): void {
                    $self->assertCount(2, $collectionC, $message);
                    $self->assertSame([0 => 'foo', 1 => 'bar'], $collectionC->toArray(), $message);
                },
            ],
            [
                'Same name string keys. Will override.',
                $fooAssociative,
                $barAssociative,
                static function (
                    self $self,
                    StringCollection $collectionA,
                    StringCollection $collectionB,
                    StringCollection $collectionC,
                    string $message
                ): void {
                    $self->assertCount(1, $collectionC, $message);
                    $self->assertSame(['foo' => 'bar'], $collectionC->toArray(), $message);
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
            0 => 'foo',
            'foo' => 'bar',
            42 => 'baz',
            43 => 'bim',
        ];

        return $elements;
    }

    protected static function createSingleElement(AbstractCollectionTestCase $self): string
    {
        return 'foo';
    }

    protected static function getHandledCollectionClassName(): string
    {
        return StringCollection::class;
    }

    public function testToSortedByCollatorWorks(): void
    {
        $collator = $this->createMock(Collator::class);

        $collectionA = new StringCollection(['2', '1']);

        $collator
            ->expects($this->once())
            ->method('compare')
            ->with('2', '1')
            ->willReturn(1);

        $collectionB = $collectionA->toSortedByCollator($collator);

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame(['2', '1'], $collectionA->toArray());
        $this->assertSame([1 => '1', 0 => '2'], $collectionB->toArray());
    }


    #[DataProvider('providerTestToUniqueByCallbackWorks')]
    public function testToUniqueWorks(
        string $message,
        Closure $elementsFactory,
        Closure $callback,
        bool $isUsingFirstEncounteredElement,
    ): void {
        [$expected, $elements] = $elementsFactory($this);

        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collectionA = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(StringCollection::class, $collectionA);

        $collectionB = $collectionA->toUnique($isUsingFirstEncounteredElement);

        $this->assertInstanceOf(StringCollection::class, $collectionB);
        $this->assertNotSame($collectionA, $collectionB, $message);
        $this->assertSame($elements, $collectionA->toArray(), $message);
        $this->assertSame($expected, $collectionB->toArray(), $message);
    }
}
