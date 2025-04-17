<?php

declare(strict_types=1);

namespace Eboreum\Collections\Exception;

/**
 * Use when one or more elements — being are provided as arguments or being produced by logic — is not accepted in a
 * given collection class.
 */
class UnacceptableElementException extends RuntimeException
{
}
