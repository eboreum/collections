<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use Closure;
use Eboreum\Collections\Collection;
use Eboreum\Collections\StringCollection;

class StringCollectionTest extends AbstractTypeCollectionTestCase
{
    /**
     * @dataProvider dataProvider_testToUniqueByCallbackWorks
     *
     * @param array<int, string> $expected
     * @param array<int, string> $elements
     */
    public function testToUniqueWorks(
        string $message,
        array $expected,
        array $elements,
        Closure $callback,
        bool $isUsingFirstEncounteredElement
    ): void {
        $handledCollectionClassName = $this->getHandledCollectionClassName();
        $collectionA = new $handledCollectionClassName($elements);

        assert(is_object($collectionA)); // Make phpstan happy
        assert($handledCollectionClassName === get_class($collectionA)); // Make phpstan happy
        assert(is_a($collectionA, Collection::class)); // Make phpstan happy
        assert(method_exists($collectionA, 'toUnique')); // Make phpstan happy

        $collectionB = $collectionA->toUnique($isUsingFirstEncounteredElement);

        assert(is_object($collectionB)); // Make phpstan happy
        assert($handledCollectionClassName === get_class($collectionB)); // Make phpstan happy
        assert(is_a($collectionB, Collection::class)); // Make phpstan happy

        $this->assertNotSame($collectionA, $collectionB);
        $this->assertSame($elements, $collectionA->toArray());
        $this->assertSame($expected, $collectionB->toArray());
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
                ['foo'],
                ['foo'],
                static function (): string {
                    return '';
                },
                true,
            ],
            [
                'Ascending, use first encountered.',
                [0 => 'a', 1 => 'b', 3 => 'c', 5 => 'd'],
                ['a', 'b', 'a', 'c', 'a', 'd'],
                static function (string $value): string {
                    return strval($value);
                },
                true,
            ],
            [
                'Ascending, use last encountered.',
                [1 => 'b', 3 => 'c', 4 => 'a', 5 => 'd'],
                ['a', 'b', 'a', 'c', 'a', 'd'],
                static function (string $value): string {
                    return strval($value);
                },
                false,
            ],
            [
                'Descending, use first encountered.',
                [0 => 'd', 1 => 'a', 2 => 'c', 4 => 'b'],
                ['d', 'a', 'c', 'a', 'b', 'a'],
                static function (string $value): string {
                    return strval($value);
                },
                true,
            ],
            [
                'Descending, use last encountered.',
                [0 => 'd', 2 => 'c', 4 => 'b', 5 => 'a'],
                ['d', 'a', 'c', 'a', 'b', 'a'],
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
     * @return array<array{string, StringCollection<string>, StringCollection<string>, Closure: void}>
     */
    public function dataProvider_testWithMergedWorks()
    {
        // @phpstan-ignore-next-line Returned values are 100% correct, but phpstan still reports an error. False positive?
        return [
            [
                'Integer keys. 0 in both, means #2 is appended as key 1.',
                new StringCollection([0 => 'foo']),
                new StringCollection([0 => 'bar']),
                function (
                    StringCollection $collectionA,
                    StringCollection $collectionB,
                    StringCollection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(2, $collectionC, $message);
                    $this->assertSame([0 => 'foo', 1 => 'bar'], $collectionC->toArray(), $message);
                },
            ],
            [
                'Same name string keys. Will override.',
                new StringCollection(['foo' => 'foo']),
                new StringCollection(['foo' => 'bar']),
                function (
                    StringCollection $collectionA,
                    StringCollection $collectionB,
                    StringCollection $collectionC,
                    string $message
                ): void {
                    $this->assertCount(1, $collectionC, $message);
                    $this->assertSame(['foo' => 'bar'], $collectionC->toArray(), $message);
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
            'foo',
            'foo' => 'bar',
            42 => 'baz',
            'bim',
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function createSingleElement()
    {
        return 'foo';
    }

    /**
     * {@inheritDoc}
     */
    protected function getHandledCollectionClassName(): string
    {
        return StringCollection::class;
    }
}
