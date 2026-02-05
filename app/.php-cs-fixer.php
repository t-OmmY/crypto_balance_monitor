<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/app',
        __DIR__.'/config',
        __DIR__.'/routes',
        __DIR__.'/database',
        __DIR__.'/tests',
    ])
    ->exclude('storage')
    ->exclude('bootstrap/cache');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
        'no_unused_imports' => true,
        'strict_param' => true,
        'declare_strict_types' => true,
        'blank_line_after_opening_tag' => false
    ])
    ->setFinder($finder);
