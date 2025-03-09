<?php

declare(strict_types=1);

namespace Eboreum\Collections\Exception;

/**
 * Use when a userland closure is being used incorrectly and the return value is invalid for the given context.
 */
class InvalidClosureReturnValueException extends RuntimeException
{
}
