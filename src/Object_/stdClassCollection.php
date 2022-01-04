<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Contract\GeneratedCollectionInterface;
use stdClass;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of stdClass, exclusively.
 *
 * @template T3 of stdClass
 * @extends AbstractNamedClassOrInterfaceCollection<T3>
 */
class stdClassCollection extends AbstractNamedClassOrInterfaceCollection implements GeneratedCollectionInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getHandledClassName(): string
    {
        return stdClass::class;
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, T3> $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    /**
     * {@inheritDoc}
     *
     * @param T3 $element
     */
    public function contains($element): bool
    {
        return parent::contains($element);
    }

    /**
     * {@inheritDoc}
     */
    public function current(): ?stdClass
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find($key): ?stdClass
    {
        return parent::find($key);
    }

    /**
     * {@inheritDoc}
     */
    public function first(): ?stdClass
    {
        return parent::first();
    }

    /**
     * {@inheritDoc}
     *
     * @param T3 $element
     */
    public function indexOf($element)
    {
        return parent::indexOf($element);
    }

    /**
     * {@inheritDoc}
     */
    public function last(): ?stdClass
    {
        return parent::last();
    }

    /**
     * {@inheritDoc}
     */
    public function maxByCallback(Closure $callback): ?stdClass
    {
        return parent::maxByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function minByCallback(Closure $callback): ?stdClass
    {
        return parent::minByCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): ?stdClass
    {
        return parent::next();
    }
}
