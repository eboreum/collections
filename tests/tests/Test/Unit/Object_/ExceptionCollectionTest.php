<?php

declare(strict_types = 1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Collection;
use Eboreum\Collections\Exception\InvalidArgumentException;
use Eboreum\Collections\Exception\RuntimeException;
use Eboreum\Collections\Object_\ExceptionCollection;
use Test\Unit\Eboreum\Collections\AbstractTypeCollectionTestCase;

class ExceptionCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
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
                    0 => new \Exception("foo"),
                ];

                return [
                    "1 single item collection.",
                    $elements,
                    $elements,
                    function(\Exception $object){
                        return $object->getMessage();
                    },
                    true,
                ];
            })(),
            (function(){
                $elements = [
                    0 => new \Exception("A"),
                    1 => new \Exception("B"),
                    2 => new \Exception("C"),
                    3 => new \Exception("B"),
                    4 => new \Exception("D"),
                    5 => new \Exception("B"),
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
                    function(\Exception $object){
                        return $object->getMessage();
                    },
                    true,
                ];
            })(),
            (function(){
                $elements = [
                    0 => new \Exception("A"),
                    1 => new \Exception("B"),
                    2 => new \Exception("C"),
                    3 => new \Exception("B"),
                    4 => new \Exception("D"),
                    5 => new \Exception("B"),
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
                    function(\Exception $object){
                        return $object->getMessage();
                    },
                    false,
                ];
            })(),
            (function(){
                $elements = [
                    0 => new \Exception("D"),
                    1 => new \Exception("B"),
                    2 => new \Exception("C"),
                    3 => new \Exception("B"),
                    4 => new \Exception("A"),
                    5 => new \Exception("B"),
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
                    function(\Exception $object){
                        return $object->getMessage();
                    },
                    true,
                ];
            })(),
            (function(){
                $elements = [
                    0 => new \Exception("D"),
                    1 => new \Exception("B"),
                    2 => new \Exception("C"),
                    3 => new \Exception("B"),
                    4 => new \Exception("A"),
                    5 => new \Exception("B"),
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
                    function(\Exception $object){
                        return $object->getMessage();
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
                new ExceptionCollection([0 => new \Exception]),
                new ExceptionCollection([0 => new \Exception]),
                function(
                    ExceptionCollection $collectionA,
                    ExceptionCollection $collectionB,
                    ExceptionCollection $collectionC,
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
                new ExceptionCollection(["foo" => new \Exception]),
                new ExceptionCollection(["foo" => new \Exception]),
                function(
                    ExceptionCollection $collectionA,
                    ExceptionCollection $collectionB,
                    ExceptionCollection $collectionC,
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
        return ExceptionCollection::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getSingleElement()
    {
        return new \Exception;
    }

    /**
     * {@inheritDoc}
     */
    protected function getMultipleElements(): array
    {
        return [
            new \Exception,
            "foo" => new \Exception,
            42 => new \Exception,
            new \Exception,
        ];
    }
}
