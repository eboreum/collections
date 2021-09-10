<?php

declare(strict_types = 1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Collection;
use Eboreum\Collections\Exception\InvalidArgumentException;
use Eboreum\Collections\Exception\RuntimeException;
use Eboreum\Collections\Object_\DirectoryCollection;
use PHPUnit\Framework\TestCase;

class DirectoryCollectionTest extends AbstractNamedClassOrInterfaceCollectionTestCase
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
                    0 => dir(__DIR__),
                ];

                return [
                    "1 single item collection.",
                    $elements,
                    $elements,
                    function(\Directory $object){
                        return $object->path;
                    },
                    true,
                ];
            })(),
            (function(){
                $elements = [
                    0 => dir(dirname(__DIR__, 1)),
                    1 => dir(__DIR__),
                    2 => dir(dirname(__DIR__, 2)),
                    3 => dir(__DIR__),
                    4 => dir(dirname(__DIR__, 3)),
                    5 => dir(__DIR__),
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
                    function(\Directory $object){
                        return $object->path;
                    },
                    true,
                ];
            })(),
            (function(){
                $elements = [
                    0 => dir(dirname(__DIR__, 1)),
                    1 => dir(__DIR__),
                    2 => dir(dirname(__DIR__, 2)),
                    3 => dir(__DIR__),
                    4 => dir(dirname(__DIR__, 3)),
                    5 => dir(__DIR__),
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
                    function(\Directory $object){
                        return $object->path;
                    },
                    false,
                ];
            })(),
            (function(){
                $elements = [
                    0 => dir(dirname(__DIR__, 3)),
                    1 => dir(__DIR__),
                    2 => dir(dirname(__DIR__, 2)),
                    3 => dir(__DIR__),
                    4 => dir(dirname(__DIR__, 1)),
                    5 => dir(__DIR__),
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
                    function(\Directory $object){
                        return $object->path;
                    },
                    true,
                ];
            })(),
            (function(){
                $elements = [
                    0 => dir(dirname(__DIR__, 3)),
                    1 => dir(__DIR__),
                    2 => dir(dirname(__DIR__, 2)),
                    3 => dir(__DIR__),
                    4 => dir(dirname(__DIR__, 1)),
                    5 => dir(__DIR__),
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
                    function(\Directory $object){
                        return $object->path;
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
                (function(){
                    $directory = dir(__DIR__);

                    assert($directory instanceof \Directory);

                    return new DirectoryCollection([0 => $directory]);
                })(),
                (function(){
                    $directory = dir(dirname(__DIR__));

                    assert($directory instanceof \Directory);

                    return new DirectoryCollection([0 => $directory]);
                })(),
                function(
                    DirectoryCollection $collectionA,
                    DirectoryCollection $collectionB,
                    DirectoryCollection $collectionC,
                    string $message
                ){
                    $this->assertCount(2, $collectionC, $message);
                    $this->assertSame($collectionA->first(), $collectionC->first(), $message);
                    $this->assertSame($collectionB->first(), $collectionC->last(), $message);
                },
            ],
            [
                "Same name string keys. Will override.",
                (function(){
                    $directory = dir(__DIR__);

                    assert($directory instanceof \Directory);

                    return new DirectoryCollection(["foo" => $directory]);
                })(),
                (function(){
                    $directory = dir(dirname(__DIR__));

                    assert($directory instanceof \Directory);

                    return new DirectoryCollection(["foo" => $directory]);
                })(),
                function(
                    DirectoryCollection $collectionA,
                    DirectoryCollection $collectionB,
                    DirectoryCollection $collectionC,
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
       return DirectoryCollection::class;
   }

   /**
    * {@inheritDoc}
    */
   protected function getSingleElement()
   {
       return dir(__DIR__);
   }

   /**
    * {@inheritDoc}
    */
   protected function getMultipleElements(): array
   {
       return [
            dir(__DIR__),
            "foo" => dir(dirname(__DIR__)),
            42 => dir(__DIR__),
            dir(dirname(__DIR__)),
       ];
   }
}
