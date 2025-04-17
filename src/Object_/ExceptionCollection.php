<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;
use Exception;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of Exception, exclusively.
 *
 * @template T of Exception
 * @implements CollectionInterface<T>
 * @extends AbstractNamedClassOrInterfaceCollection<T>
 */
class ExceptionCollection extends AbstractNamedClassOrInterfaceCollection implements CollectionInterface
{
    public static function getHandledClassName(): string
    {
        return Exception::class;
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

    public function current(): ?Exception
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find($key): ?Exception
    {
        return parent::find($key);
    }

    public function first(): ?Exception
    {
        return parent::first();
    }

    public function get(int|string $key): ?Exception
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

    public function last(): ?Exception
    {
        return parent::last();
    }

    public function maxByCallback(Closure $callback): ?Exception
    {
        return parent::maxByCallback($callback);
    }

    public function minByCallback(Closure $callback): ?Exception
    {
        return parent::minByCallback($callback);
    }

    public function next(): ?Exception
    {
        return parent::next();
    }
}
