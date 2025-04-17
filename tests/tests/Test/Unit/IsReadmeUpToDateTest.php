<?php

declare(strict_types=1);

namespace Eboreum\Collections\Test\Unit;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

use function dirname;
use function file_get_contents;
use function is_file;
use function ob_end_clean;
use function ob_get_contents;
use function ob_start;

/**
 * Did we leave remember to update the contents of README.md?
 */
#[CoversNothing]
class IsReadmeUpToDateTest extends TestCase
{
    public function testCompareContents(): void
    {
        $readmeFilePath = dirname(TEST_ROOT_PATH) . '/README.md';

        $this->assertTrue(is_file($readmeFilePath), 'README.md does not exist!');

        $actualContents = file_get_contents($readmeFilePath);

        ob_start();
        include dirname(TEST_ROOT_PATH) . '/script/make-readme.php';
        $producedContents = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(
            $actualContents === $producedContents,
            'README.md is not up–to-date. Please run: php script/make-readme.php',
        );
    }
}
