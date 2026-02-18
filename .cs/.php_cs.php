<?php

$component = $_ENV['COMPONENT'] ?? null;

$finder = PhpCsFixer\Finder::create();

if($component){
    $finder->in('components/'.$component);
}else{
    $finder->in('src');
}

return (new PhpCsFixer\Config())->setRules(
    [
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'increment_style' => ['style' => 'pre'],
        'ordered_imports' => true,
        'phpdoc_order' => true,
        'linebreak_after_opening_tag' => true,
        'phpdoc_no_package' => false,
        'phpdoc_inline_tag_normalizer' => false,
        'cast_spaces' => false,
        'no_superfluous_phpdoc_tags' => true,
        'single_line_throw' => false,
        'trailing_comma_in_multiline' => false,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => false,
            'import_functions' => false,
        ],
        'operator_linebreak' => [
            'only_booleans' => true,
            'position' => 'end',
        ]
    ]
)->setFinder($finder);
