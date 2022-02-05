<?php

declare(strict_types=1);

namespace Eboreum\Collections\Attribute\Method;

use Attribute;

/**
 * Denotes that the referenced method is subject to being overridden.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Overridden
{
}
