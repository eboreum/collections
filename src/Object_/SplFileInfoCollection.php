<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\Contract\CollectionInterface;
use SplFileInfo;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of SplFileInfo, exclusively.
 *
 * @template T of SplFileInfo
 * @implements CollectionInterface<T>
 * @extends AbstractNamedClassOrInterfaceCollection<T>
 */
class SplFileInfoCollection extends AbstractNamedClassOrInterfaceCollection implements CollectionInterface
{
    public static function getHandledClassName(): string
    {
        return SplFileInfo::class;
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

    public function current(): ?SplFileInfo
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find($key): ?SplFileInfo
    {
        return parent::find($key);
    }

    public function first(): ?SplFileInfo
    {
        return parent::first();
    }

    public function get(int|string $key): ?SplFileInfo
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

    public function last(): ?SplFileInfo
    {
        return parent::last();
    }

    public function maxByCallback(Closure $callback): ?SplFileInfo
    {
        return parent::maxByCallback($callback);
    }

    public function minByCallback(Closure $callback): ?SplFileInfo
    {
        return parent::minByCallback($callback);
    }

    public function next(): ?SplFileInfo
    {
        return parent::next();
    }
}
