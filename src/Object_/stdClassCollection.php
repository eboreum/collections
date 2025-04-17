<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;
use stdClass;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of stdClass, exclusively.
 *
 * @template T of stdClass
 * @implements CollectionInterface<T>
 * @extends AbstractNamedClassOrInterfaceCollection<T>
 */
class stdClassCollection extends AbstractNamedClassOrInterfaceCollection implements CollectionInterface // phpcs:ignore
{
    public static function getHandledClassName(): string
    {
        return stdClass::class;
    }

    /**
     * {@inheritDoc}
     *
     * @param array<int|string, T> $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    /**
     * {@inheritDoc}
     *
     * @param T $element
     */
    public function contains($element): bool
    {
        return parent::contains($element);
    }

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

    public function first(): ?stdClass
    {
        return parent::first();
    }

    public function get(int|string $key): ?stdClass
    {
        return parent::get($key);
    }

    /**
     * {@inheritDoc}
     *
     * @param T $element
     */
    public function indexOf($element): int|string|null
    {
        return parent::indexOf($element);
    }

    public function last(): ?stdClass
    {
        return parent::last();
    }

    public function maxByCallback(Closure $callback): ?stdClass
    {
        return parent::maxByCallback($callback);
    }

    public function minByCallback(Closure $callback): ?stdClass
    {
        return parent::minByCallback($callback);
    }

    public function next(): ?stdClass
    {
        return parent::next();
    }
}
