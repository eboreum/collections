<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use DateTime;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of DateTime, exclusively.
 *
 * @template T of DateTime
 * @extends AbstractDateTimeCollection<T>
 */
class DateTimeCollection extends AbstractDateTimeCollection
{
    public static function getHandledClassName(): string
    {
        return DateTime::class;
    }

    public function current(): ?DateTime
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find($key): ?DateTime
    {
        return parent::find($key);
    }

    public function first(): ?DateTime
    {
        return parent::first();
    }

    public function get(int|string $key): ?DateTime
    {
        return parent::get($key);
    }

    public function last(): ?DateTime
    {
        return parent::last();
    }

    public function max(): ?DateTime
    {
        if (!$this->elements) {
            return null;
        }

        return $this->maxByCallback(static function (DateTime $dateTime): int {
            return $dateTime->getTimestamp();
        });
    }

    public function maxByCallback(Closure $callback): ?DateTime
    {
        return parent::maxByCallback($callback);
    }

    public function min(): ?DateTime
    {
        if (!$this->elements) {
            return null;
        }

        return $this->minByCallback(static function (DateTime $dateTime): int {
            return $dateTime->getTimestamp();
        });
    }

    public function minByCallback(Closure $callback): ?DateTime
    {
        return parent::minByCallback($callback);
    }

    public function next(): ?DateTime
    {
        return parent::next();
    }
}
