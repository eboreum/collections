<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use Closure;
use DateTimeImmutable;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of DateTimeImmutable, exclusively.
 *
 * @template T of DateTimeImmutable
 * @extends AbstractDateTimeCollection<T>
 */
class DateTimeImmutableCollection extends AbstractDateTimeCollection
{
    public static function getHandledClassName(): string
    {
        return DateTimeImmutable::class;
    }

    public function current(): ?DateTimeImmutable
    {
        return parent::current();
    }

    /**
     * {@inheritDoc}
     */
    public function find($key): ?DateTimeImmutable
    {
        return parent::find($key);
    }

    public function first(): ?DateTimeImmutable
    {
        return parent::first();
    }

    public function get(int|string $key): ?DateTimeImmutable
    {
        return parent::get($key);
    }

    public function last(): ?DateTimeImmutable
    {
        return parent::last();
    }

    public function max(): ?DateTimeImmutable
    {
        if (!$this->elements) {
            return null;
        }

        return $this->maxByCallback(static function (DateTimeImmutable $dateTimeImmutable): int {
            return $dateTimeImmutable->getTimestamp();
        });
    }

    public function maxByCallback(Closure $callback): ?DateTimeImmutable
    {
        return parent::maxByCallback($callback);
    }

    public function min(): ?DateTimeImmutable
    {
        if (!$this->elements) {
            return null;
        }

        return $this->minByCallback(static function (DateTimeImmutable $dateTimeImmutable): int {
            return $dateTimeImmutable->getTimestamp();
        });
    }

    public function minByCallback(Closure $callback): ?DateTimeImmutable
    {
        return parent::minByCallback($callback);
    }

    public function next(): ?DateTimeImmutable
    {
        return parent::next();
    }
}
