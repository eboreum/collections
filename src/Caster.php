<?php

declare(strict_types=1);

namespace Eboreum\Collections;

use Eboreum\Caster\Caster as EboreumCaster;
use Eboreum\Caster\CharacterEncoding;
use Eboreum\Caster\Collection\Formatter\ObjectFormatterCollection;
use Eboreum\Caster\Contract\CharacterEncodingInterface;
use Eboreum\Caster\Contract\Formatter\ObjectFormatterInterface;
use Eboreum\Caster\Formatter\Object_\ClosureFormatter;
use Eboreum\Caster\Formatter\Object_\DateTimeInterfaceFormatter;
use Eboreum\Caster\Formatter\Object_\DebugIdentifierAttributeInterfaceFormatter;
use Eboreum\Caster\Formatter\Object_\DirectoryFormatter;
use Eboreum\Caster\Formatter\Object_\TextuallyIdentifiableInterfaceFormatter;
use Eboreum\Caster\Formatter\Object_\ThrowableFormatter;

class Caster extends EboreumCaster
{
    private static ?Caster $instance = null;

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = self::create();
        }

        return self::$instance;
    }

    public static function create(?CharacterEncodingInterface $characterEncoding = null): static
    {
        if (null === $characterEncoding) {
            $characterEncoding = CharacterEncoding::getInstance();
        }

        $caster = new static($characterEncoding);

        /** @var array<ObjectFormatterInterface> $formatters */
        $formatters = [
            new TextuallyIdentifiableInterfaceFormatter(),
            new DebugIdentifierAttributeInterfaceFormatter(),
            new ClosureFormatter(),
            new DirectoryFormatter(),
            new DateTimeInterfaceFormatter(),
            new ThrowableFormatter(),
        ];

        $caster = $caster->withCustomObjectFormatterCollection(new ObjectFormatterCollection($formatters));
        $caster = $caster->withIsConvertingASCIIControlCharactersToHexAnnotationInStrings(true);
        $caster = $caster->withIsWrapping(true);

        return $caster;
    }
}
