<?php

declare(strict_types=1);

use Eboreum\Collections\Contract\ObjectCollectionInterface;

require_once dirname(__DIR__) . '/bootstrap.php';

$srcFolderPath = dirname(__DIR__, 2) . '/src';
$count = 0;

$absoluteFilePaths = glob($srcFolderPath . '/Object_/*Collection.php');

assert(is_array($absoluteFilePaths));

foreach ($absoluteFilePaths as $filePathAbsolute) {
    $filePathRelative = substr($filePathAbsolute, strlen($srcFolderPath) + 1);
    $className = sprintf(
        'Eboreum\\Collections\\%s',
        preg_replace(
            '/\.php$/',
            '',
            str_replace('/', '\\', $filePathRelative),
        ),
    );

    if (false === class_exists($className)) {
        continue;
    }

    if (false === is_a($className, ObjectCollectionInterface::class, true)) {
        continue;
    }

    $reflectionClass = new ReflectionClass($className);

    if ($reflectionClass->isAbstract()) {
        continue;
    }

    $count++;

    echo sprintf(
        " - `\\%s`: A collection, which may and will only ever contain instances of `\\%s`.\n",
        $className,
        $className::getHandledClassName(),
    );
}

if (0 === $count) {
    throw new RuntimeException(sprintf(
        '0 instances of \\%s were processed',
        ObjectCollectionInterface::class,
    ));
}
