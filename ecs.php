<?php

use BitAndBlack\Hyphenizer\Sdk\Util\Path;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return ECSConfig::configure()
    ->withParallel()
    ->withPaths([
        __DIR__,
    ])
    ->withSkip([
        (new Path())->getLibraryFolder(),
        __DIR__ . DIRECTORY_SEPARATOR . 'vendor',
    ])
    ->withSets([
        SetList::PSR_12,
        SetList::ARRAY,
        SetList::CLEAN_CODE,
    ])
    ->withConfiguredRule(YodaStyleFixer::class, [
        'always_move_variable' => true,
    ])
;
