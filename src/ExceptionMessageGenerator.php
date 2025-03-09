<?php

declare(strict_types=1);

namespace Eboreum\Collections;

use Eboreum\Exceptional\ExceptionMessageGenerator as EboreumExceptionMessageGenerator;

class ExceptionMessageGenerator extends EboreumExceptionMessageGenerator
{
    private static ?self $instance = null;

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = self::create();
        }

        return self::$instance;
    }

    public static function create(): static
    {
        $caster = Caster::getInstance();

        return new ExceptionMessageGenerator($caster);
    }
}
