<?php

declare(strict_types=1);

$finder = new PhpCsFixer\Finder()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

return new PhpCsFixer\Config()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS2.0' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'declare_strict_types' => true,
        'native_function_invocation' => false,
        'php_unit_method_casing' => ['case' => 'camel_case'],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => false,
            'import_functions' => false,
        ],
    ])
    ->setFinder($finder);
