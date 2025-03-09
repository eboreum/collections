<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use Eboreum\Collections\Caster;
use Eboreum\Collections\ExceptionMessageGenerator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExceptionMessageGenerator::class)]
class ExceptionMessageGeneratorTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testCreateInstanceWorks(): void
    {
        $exceptionMessageGeneratorA = ExceptionMessageGenerator::create();
        $exceptionMessageGeneratorB = ExceptionMessageGenerator::create();

        $this->assertNotSame($exceptionMessageGeneratorA, $exceptionMessageGeneratorB);
        $this->assertSame(ExceptionMessageGenerator::class, $exceptionMessageGeneratorA::class);
        $this->assertSame(ExceptionMessageGenerator::class, $exceptionMessageGeneratorB::class);
        $this->assertSame(Caster::getInstance(), $exceptionMessageGeneratorB->getCaster());
    }

    #[RunInSeparateProcess]
    public function testGetInstanceWorks(): void
    {
        $exceptionMessageGeneratorA = ExceptionMessageGenerator::getInstance();
        $exceptionMessageGeneratorB = ExceptionMessageGenerator::getInstance();

        $this->assertSame($exceptionMessageGeneratorA, $exceptionMessageGeneratorB);
        $this->assertSame(ExceptionMessageGenerator::class, $exceptionMessageGeneratorA::class);
        $this->assertSame(Caster::getInstance(), $exceptionMessageGeneratorB->getCaster());
    }
}
