<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Collections;

use Eboreum\Collections\Caster;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

use function count;

#[CoversClass(Caster::class)]
class CasterTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testCreateInstanceWorks(): void
    {
        $casterA = Caster::create();
        $casterB = Caster::create();

        $this->assertNotSame($casterA, $casterB);
        $this->assertSame(Caster::class, $casterA::class);
        $this->assertSame(Caster::class, $casterB::class);
        $this->assertGreaterThan(0, count($casterA->getCustomObjectFormatterCollection()));
        $this->assertGreaterThan(0, count($casterB->getCustomObjectFormatterCollection()));
    }

    #[RunInSeparateProcess]
    public function testGetInstanceWorks(): void
    {
        $casterA = Caster::getInstance();
        $casterB = Caster::getInstance();

        $this->assertSame($casterA, $casterB);
        $this->assertSame(Caster::class, $casterA::class);
        $this->assertGreaterThan(0, count($casterA->getCustomObjectFormatterCollection()));
    }
}
