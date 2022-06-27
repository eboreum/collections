<?php

declare(strict_types=1);

namespace Eboreum\Collections\Contract\CollectionInterface;

enum ToReindexedDuplicateKeyBehaviorEnum
{
    /**
     * When the same key is produced more than once, an exception is thrown.
     */
    case throw_exception;

    /**
     * Will use the first element for a given, duplicate key. The rest, where this duplicate key is produced, will be
     * discarded.
     */
    case use_first_element;

    /**
     * Will use the last element for a given, duplicate key. All prior elements, where this duplicate key is produced,
     * will be discarded.
     */
    case use_last_element;
}
