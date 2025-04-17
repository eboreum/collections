<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use Eboreum\Collections\Caster;
use Eboreum\Collections\Collection;
use Eboreum\Collections\Exception\RuntimeException;
use Eboreum\Collections\Exception\UnacceptableCollectionException;
use Eboreum\Collections\Exception\UnacceptableElementException;
use Exception;
use ReflectionObject;

use function sprintf;

/**
 * @template T of mixed
 * @template TCollection of Collection<mixed>
 * @extends AbstractCollectionTestCase<T, TCollection>
 */
abstract class AbstractTypeCollectionTestCase extends AbstractCollectionTestCase
{
    public function testWithAddedThrowsExceptionWhenArgumentElementIsNotAccepted(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);

        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        try {
            $collection->withAdded(null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(UnacceptableElementException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'Argument $element = %s cannot be added to the current collection, %s',
                    Caster::getInstance()->castTyped(null),
                    Caster::getInstance()->castTyped($collection),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(UnacceptableElementException::class, $currentException::class);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithAddedMultipleThrowsExceptionWhenArgumentElementsContainsInvalidValues(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $elements = static::createMultipleElements($this);

        $collection = new $handledCollectionClassName($elements);

        $this->assertInstanceOf(Collection::class, $collection);

        try {
            $collection->withAddedMultiple([null]);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(UnacceptableElementException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'Argument $elements = %s cannot be added to the current collection, %s',
                    Caster::getInstance()->castTyped([null]),
                    Caster::getInstance()->castTyped($collection),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(UnacceptableElementException::class, $currentException::class);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithMergedThrowsExceptionWhenArgumentCollectionBIsNotASubclassOfCollectionA(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collectionA = new $handledCollectionClassName();
        $collectionB = new Collection();

        $this->assertInstanceOf(Collection::class, $collectionA);

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

    public function testWithMergedThrowsExceptionWhenArgumentCollectionBIsASubclassOfCollectionAButElementIsNotAcceptedByCollectionA(): void // phpcs:ignore
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collectionA = new $handledCollectionClassName();
        $collectionB = new $handledCollectionClassName();

        $reflectionObjectCurrent = new ReflectionObject($collectionB);
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

        $this->assertInstanceOf(Collection::class, $collectionA);
        $this->assertInstanceOf(Collection::class, $collectionB);

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
            $this->assertSame(UnacceptableElementException::class, $currentException::class);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithRemovedElementThrowsExceptionWhenArgumentElementIsNotAcceptedbyTheCollection(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->assertInstanceOf(Collection::class, $collection);

        try {
            $collection->withRemovedElement(null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(UnacceptableElementException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'Argument $element = %s cannot be removed from the current collection, %s',
                    Caster::getInstance()->castTyped(null),
                    Caster::getInstance()->castTyped($collection),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(UnacceptableElementException::class, $currentException::class);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithSetThrowsExceptionWhenArgumentElementIsNotAcceptedbyTheCollection(): void
    {
        $handledCollectionClassName = static::getHandledCollectionClassName();
        $collection = new $handledCollectionClassName();

        $this->assertInstanceOf(Collection::class, $collection);

        try {
            $collection->withSet(0, null);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(UnacceptableElementException::class, $currentException::class);
            $this->assertSame(
                sprintf(
                    'Argument $element = %s (with $key = %s) cannot be set on the current collection, %s',
                    Caster::getInstance()->castTyped(null),
                    Caster::getInstance()->castTyped(0),
                    Caster::getInstance()->castTyped($collection),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(UnacceptableElementException::class, $currentException::class);

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }
}
