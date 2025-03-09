<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections\Object_;

use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Caster;
use Eboreum\Collections\Collection;
use Eboreum\Collections\Exception\UnacceptableElementException;
use Exception;
use Test\Unit\Eboreum\Collections\AbstractTypeCollectionTestCase;

use function sprintf;

/**
 * @template T of object
 * @template TCollection of Collection<T>
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
}
