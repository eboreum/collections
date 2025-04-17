<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use DateTimeInterface;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of DateTimeInterface, exclusively.
 *
 * @template T of DateTimeInterface
 * @extends AbstractDateTimeCollection<T>
 */
class DateTimeInterfaceCollection extends AbstractDateTimeCollection
{
    public static function getHandledClassName(): string
    {
        return DateTimeInterface::class;
    }

    public function current(): ?DateTimeInterface
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find($key): ?DateTimeInterface
    {
        return parent::find($key);
    }

    public function first(): ?DateTimeInterface
    {
        return parent::first();
    }

    public function get(int|string $key): ?DateTimeInterface
    {
        return parent::get($key);
    }

    public function last(): ?DateTimeInterface
    {
        return parent::last();
    }

    public function max(): ?DateTimeInterface
    {
        if (!$this->elements) {
            return null;
        }

        return $this->maxByCallback(static function (DateTimeInterface $dateTime): int {
            return $dateTime->getTimestamp();
        });
    }

    public function maxByCallback(Closure $callback): ?DateTimeInterface
    {
        return parent::maxByCallback($callback);
    }

    public function min(): ?DateTimeInterface
    {
        if (!$this->elements) {
            return null;
        }

        return $this->minByCallback(static function (DateTimeInterface $dateTime): int {
            return $dateTime->getTimestamp();
        });
    }

    public function minByCallback(Closure $callback): ?DateTimeInterface
    {
        return parent::minByCallback($callback);
    }

    public function next(): ?DateTimeInterface
    {
        return parent::next();
    }
}
