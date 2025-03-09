<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection;
use Eboreum\Collections\IntegerCollection;

use function sprintf;

/**
 * {@inheritDoc}
 *
 * A collection which contains instances of DateTime|DateTimeImmutable|DateTimeInterface, exclusively.
 *
 * @template T of DateTime|DateTimeImmutable|DateTimeInterface
 * @implements DateTimeCollectionInterface<T>
 * @extends AbstractNamedClassOrInterfaceCollection<T>
 */
abstract class AbstractDateTimeCollection extends AbstractNamedClassOrInterfaceCollection implements
    DateTimeCollectionInterface
{
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

    /**
     * {@inheritDoc}
     *
     * @param T $element
     */
    public function indexOf($element): int|string|null
    {
        return parent::indexOf($element);
    }

    /**
     * Converts all instances of DateTime to their equivalent Unix Epoch time in microseconds.
     *
     * @return IntegerCollection<int>
     */
    public function toMicrosecondTimestampIntegerCollection(): IntegerCollection
    {
        /** @var array<int> $elements */
        $elements = $this->map(
            static function (DateTimeInterface $dateTime): int {
                return (int) sprintf(
                    '%d%06d',
                    $dateTime->getTimestamp(),
                    $dateTime->format('u'),
                );
            },
        );

        return new IntegerCollection($elements);
    }

    public function toSorted(bool $isAscending = true): static
    {
        $direction = ($isAscending ? 1 : -1);

        return $this->toSortedByCallback(
            static function (DateTimeInterface $a, DateTimeInterface $b) use ($direction): int {
                return ($a->getTimestamp() - $b->getTimestamp()) * $direction;
            }
        );
    }

    /**
     * Converts all instances of DateTime to their equivalent Unix Epoch time in seconds.
     *
     * @return IntegerCollection<int>
     */
    public function toTimestampIntegerCollection(): IntegerCollection
    {
        /** @var array<int> $elements */
        $elements = $this->map(
            static function (DateTimeInterface $dateTime): int {
                return $dateTime->getTimestamp();
            },
        );

        return new IntegerCollection($elements);
    }

    public function toUnique(bool $isUsingFirstEncounteredElement = true): static
    {
        return $this->toUniqueByCallback(
            static function (DateTimeInterface $element) {
                return (string) $element->getTimestamp();
            },
            $isUsingFirstEncounteredElement,
        );
    }
}
