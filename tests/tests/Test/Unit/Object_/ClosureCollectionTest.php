<?php

declare(strict_types = 1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Collection;
use Eboreum\Collections\Exception\InvalidArgumentException;
use Eboreum\Collections\Exception\RuntimeException;
use Eboreum\Collections\Object_\ClosureCollection;
use Test\Unit\Eboreum\Collections\AbstractTypeCollectionTestCase;

class ClosureCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
{
    /**
     * {@inheritDoc}
     */
    public function dataProvider_testToUniqueByCallbackWorks(): array
    {
        return [
            [
                "Empty collection.",
                [],
                [],
                function(){
                    return "";
                },
                true,
            ],
            (function(){
                $elements = [
                    0 => function(){
                        return "";
                    },
                ];

                return [
                    "1 single item collection.",
                    $elements,
                    $elements,
                    function(\Closure $closure){
                        return $closure();
                    },
                    true,
                ];
            })(),
            (function(){
                $elements = [
                    0 => function(){
                        return "a";
                    },
                    1 => function(){
                        return "b";
                    },
                    2 => function(){
                        return "c";
                    },
                    3 => function(){
                        return "b";
                    },
                    4 => function(){
                        return "d";
                    },
                    5 => function(){
                        return "b";
                    },
                ];

                return [
                    "Ascending, use first encountered.",
                    [
                        0 => $elements[0],
                        1 => $elements[1],
                        2 => $elements[2],
                        4 => $elements[4],
                    ],
                    $elements,
                    function(\Closure $closure){
                        return $closure();
                    },
                    true,
                ];
            })(),
            (function(){
                $elements = [
                    0 => function(){
                        return "a";
                    },
                    1 => function(){
                        return "b";
                    },
                    2 => function(){
                        return "c";
                    },
                    3 => function(){
                        return "b";
                    },
                    4 => function(){
                        return "d";
                    },
                    5 => function(){
                        return "b";
                    },
                ];

                return [
                    "Ascending, use last encountered.",
                    [
                        0 => $elements[0],
                        2 => $elements[2],
                        4 => $elements[4],
                        5 => $elements[5],
                    ],
                    $elements,
                    function(\Closure $closure){
                        return $closure();
                    },
                    false,
                ];
            })(),
            (function(){
                $elements = [
                    0 => function(){
                        return "d";
                    },
                    1 => function(){
                        return "b";
                    },
                    2 => function(){
                        return "c";
                    },
                    3 => function(){
                        return "b";
                    },
                    4 => function(){
                        return "a";
                    },
                    5 => function(){
                        return "b";
                    },
                ];

                return [
                    "Descending, use first encountered.",
                    [
                        0 => $elements[0],
                        1 => $elements[1],
                        2 => $elements[2],
                        4 => $elements[4],
                    ],
                    $elements,
                    function(\Closure $closure){
                        return $closure();
                    },
                    true,
                ];
            })(),
            (function(){
                $elements = [
                    0 => function(){
                        return "d";
                    },
                    1 => function(){
                        return "b";
                    },
                    2 => function(){
                        return "c";
                    },
                    3 => function(){
                        return "b";
                    },
                    4 => function(){
                        return "a";
                    },
                    5 => function(){
                        return "b";
                    },
                ];

                return [
                    "Descending, use last encountered.",
                    [
                        0 => $elements[0],
                        2 => $elements[2],
                        4 => $elements[4],
                        5 => $elements[5],
                    ],
                    $elements,
                    function(\Closure $closure){
                        return $closure();
                    },
                    false,
                ];
            })(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function dataProvider_testWithMergedWorks(): array
    {
        return [
            [
                "Integer keys. 0 in both, means #2 is appended as key 1.",
                new ClosureCollection([0 => function(){}]),
                new ClosureCollection([0 => function(){}]),
                function(
                    ClosureCollection $collectionA,
                    ClosureCollection $collectionB,
                    ClosureCollection $collectionC,
                    string $message
                ){
                    $this->assertCount(2, $collectionC, $message);
                    $this->assertSame([0, 1], $collectionC->getKeys(), $message);
                    $this->assertSame($collectionA->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->first(), $collectionC->last(), $message);
                },
            ],
            [
                "Same name string keys. Will override.",
                new ClosureCollection(["foo" => function(){}]),
                new ClosureCollection(["foo" => function(){}]),
                function(
                    ClosureCollection $collectionA,
                    ClosureCollection $collectionB,
                    ClosureCollection $collectionC,
                    string $message
                ){
                    $this->assertCount(1, $collectionC, $message);
                    $this->assertSame(["foo"], $collectionC->getKeys(), $message);
                    $this->assertNotSame($collectionA->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->last(), $collectionC->last(), $message);
                },
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getHandledCollectionClassName(): string
    {
        return ClosureCollection::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getSingleElement()
    {
        return function(){};
    }

    /**
     * {@inheritDoc}
     */
    protected function getMultipleElements(): array
    {
        return [
            function(){},
            "foo" => function(){},
            42 => function(){},
            function(){},
        ];
    }
}
