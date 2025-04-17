<?php

declare(strict_types=1);

namespace Eboreum\Collections\Object_;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Eboreum\Collections\Contract\CollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\MaximumableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\MinimumableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\SortableCollectionInterface;
use Eboreum\Collections\Contract\CollectionInterface\UniqueableCollectionInterface;

/**
 * {@inheritDoc}
 *
 * @template T of DateTime|DateTimeImmutable|DateTimeInterface
 * @extends CollectionInterface<T>
 * @extends MaximumableCollectionInterface<T>
 * @extends MinimumableCollectionInterface<T>
 * @extends SortableCollectionInterface<T>
 * @extends UniqueableCollectionInterface<T>
 */
interface DateTimeCollectionInterface extends
    CollectionInterface,
    MaximumableCollectionInterface,
    MinimumableCollectionInterface,
    SortableCollectionInterface,
    UniqueableCollectionInterface
{
}
