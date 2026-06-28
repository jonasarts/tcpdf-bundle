<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withRootFiles()
    // PHP 8.4 language-level migration set.
    ->withPhpSets(php84: true)
    // Generic quality / dead-code / coding-style sets.
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
    )
    // Framework sets are applied automatically based on installed composer
    // versions. Symfony & PHPUnit rules activate once the optional extension
    // packages (rector/rector-symfony, rector/rector-phpunit) are present;
    // harmless no-op otherwise.
    ->withComposerBased(
        phpunit: true,
        symfony: true,
    );
