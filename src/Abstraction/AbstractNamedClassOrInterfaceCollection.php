<?php

declare(strict_types=1);

namespace Eboreum\Collections\Abstraction;

use Eboreum\Collections\Caster;
use Eboreum\Collections\Collection;
use Eboreum\Collections\Contract\ObjectCollectionInterface;
use Eboreum\Collections\Exception\InvalidArgumentException;
use Eboreum\Collections\Exception\RuntimeException;
use Eboreum\Exceptional\ExceptionMessageGenerator;

/**
 * {@inheritDoc}
 *
 * @template T2 of object
 * @extends Collection<T2>
 */
abstract class AbstractNamedClassOrInterfaceCollection extends Collection implements ObjectCollectionInterface
{
    /**
     * {@inheritDoc}
     *
     * @param array<int|string, T2> $elements
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
                    Caster::makeNormalizedClassName(new \ReflectionClass(static::class)),
                    static::getHandledClassName(),
                ));
            }

            parent::__construct($elements);
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod(self::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function assertIsElementAccepted($element): void
    {
        if (false === static::isElementAccepted($element)) {
            $handledClassName = static::getHandledClassName();

            assert(class_exists($handledClassName) || interface_exists($handledClassName));

            throw new InvalidArgumentException(sprintf(
                'Expects argument $element to be an object, instance of %s, but it is not. Found: %s',
                Caster::makeNormalizedClassName(new \ReflectionClass($handledClassName)),
                Caster::getInstance()->castTyped($element),
            ));
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function isElementAccepted($element): bool
    {
        return (
            is_object($element)
            && is_a($element, static::getHandledClassName())
        );
    }
}
