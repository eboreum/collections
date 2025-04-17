<?php

declare(strict_types=1);

namespace Eboreum\Collections\Abstraction;

use Eboreum\Collections\Caster;
use Eboreum\Collections\Collection;
use Eboreum\Collections\Contract\ObjectCollectionInterface;
use Eboreum\Collections\Exception\RuntimeException;
use Eboreum\Collections\Exception\UnacceptableElementException;
use Eboreum\Collections\ExceptionMessageGenerator;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

use function assert;
use function class_exists;
use function func_get_args;
use function interface_exists;
use function is_a;
use function is_object;
use function sprintf;

/**
 * {@inheritDoc}
 *
 * @template T of object
 * @extends Collection<T>
 */
abstract class AbstractNamedClassOrInterfaceCollection extends Collection implements ObjectCollectionInterface
{
    public static function assertIsElementAccepted(mixed $element): void
    {
        if (false === static::isElementAccepted($element)) {
            $handledClassName = static::getHandledClassName();

            assert(class_exists($handledClassName) || interface_exists($handledClassName));

            throw new UnacceptableElementException(
                sprintf(
                    'Expects argument $element = %s to be an object, instance of %s, but it is not',
                    Caster::getInstance()->castTyped($element),
                    Caster::makeNormalizedClassName(new ReflectionClass($handledClassName)),
                ),
            );
        }
    }

    public static function isElementAccepted(mixed $element): bool
    {
        return (
            is_object($element)
            && is_a($element, static::getHandledClassName())
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, T> $elements
     */
    public function __construct(array $elements = [])
    {
        try {
            if (
                false === class_exists(static::getHandledClassName())
                && false === interface_exists(static::getHandledClassName())
            ) {
                throw new RuntimeException(sprintf(
                    'Collection %s has handled class \\%s, but said handled class does not exist',
                    Caster::makeNormalizedClassName(new ReflectionClass(static::class)),
                    static::getHandledClassName(),
                ));
            }

            parent::__construct($elements);
        } catch (Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new ReflectionMethod(self::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }
    }
}
