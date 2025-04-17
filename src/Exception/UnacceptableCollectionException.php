<?php

declare(strict_types=1);

namespace Eboreum\Collections\Exception;

/**
 * Use when a collection class is incompatible with a different collection class. E.g. when merging or intersecting.
 */
class UnacceptableCollectionException extends RuntimeException
{
}
