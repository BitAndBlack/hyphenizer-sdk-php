<?php

use BitAndBlack\Hyphenizer\Sdk\Util\Path;
use Rector\CodeQuality\Rector\Identical\SimplifyBoolIdenticalTrueRector;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;

return RectorConfig::configure()
    ->withParallel()
    ->withPaths([
        __DIR__,
    ])
    ->withSkip([
        (new Path())->getLibraryFolder(),
        __DIR__ . DIRECTORY_SEPARATOR . 'vendor',
        PreferPHPUnitThisCallRector::class,
        SimplifyBoolIdenticalTrueRector::class,
    ])
    ->withPhpSets()
    ->withImportNames()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        naming: true,
    )
    ->withComposerBased(
        phpunit: true,
    )
;
