<?php

declare(strict_types=1);

namespace Eboreum\Collections;

use Eboreum\Caster\Caster as EboreumCaster;
use Eboreum\Caster\CharacterEncoding;
use Eboreum\Caster\Collection\Formatter\ObjectFormatterCollection;
use Eboreum\Caster\Contract\CharacterEncodingInterface;
use Eboreum\Caster\Formatter\Object_\ClosureFormatter;
use Eboreum\Caster\Formatter\Object_\DateTimeInterfaceFormatter;
use Eboreum\Caster\Formatter\Object_\DebugIdentifierAttributeInterfaceFormatter;
use Eboreum\Caster\Formatter\Object_\DirectoryFormatter;
use Eboreum\Caster\Formatter\Object_\TextuallyIdentifiableInterfaceFormatter;
use Eboreum\Caster\Formatter\Object_\ThrowableFormatter;

/**
 * {@inheritDoc}
 */
class Caster extends EboreumCaster
{
    private static ?Caster $instance = null;

    /**
     * {@inheritDoc}
     */
    public static function create(?CharacterEncodingInterface $characterEncoding = null): static
    {
        if (null === $characterEncoding) {
            $characterEncoding = CharacterEncoding::getInstance();
        }

        $caster = new static($characterEncoding);

        $caster = $caster->withCustomObjectFormatterCollection(new ObjectFormatterCollection([
            new TextuallyIdentifiableInterfaceFormatter(),
            new DebugIdentifierAttributeInterfaceFormatter(),
            new ClosureFormatter(),
            new DirectoryFormatter(),
            new DateTimeInterfaceFormatter(),
            new ThrowableFormatter(),
        ]));

        return $caster;
    }

    public static function getInstance(): Caster
    {
        if (null === self::$instance) {
            self::$instance = self::create();
        }

        return self::$instance;
    }
}
