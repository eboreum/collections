<?php
// phpcs:ignoreFile

declare(strict_types=1);

use Eboreum\Collections\Caster;

require dirname(__DIR__) . '/script/bootstrap.php';

define('TEST_ROOT_PATH', __DIR__);

(static function (): void {
    // The following code allows for dynamic memory limit overrides, but always run with a certain minimum limit.

    $annotationToBytes = static function (string $annotation): int {
        preg_match('/^(\d+)([MG])?$/D', $annotation, $match);

        if (false === array_key_exists(1, $match)) {
            throw new Exception(
                sprintf(
                    'Argument $annotation = %s is invalid',
                    Caster::getInstance()->castTyped($annotation),
                ),
            );
        }

        return (int) match ($match[2] ?? null) {
            'M' => ((int) $match[1]) * pow(1024, 2),
            'G' => ((int) $match[1]) * pow(1024, 3),
            null => (int) $match[1],
            default => throw new Exception(
                sprintf(
                    'Argument $annotation = %s is invalid',
                    Caster::getInstance()->castTyped($annotation),
                )
            ),
        };
    };

    $currentMemoryLimit = $annotationToBytes(ini_get('memory_limit'));
    $minimumMemoryLimit = $annotationToBytes('1G');
    $targetMemoryLimit = max($currentMemoryLimit, $minimumMemoryLimit);

    ini_set('memory_limit', $targetMemoryLimit);
})();
