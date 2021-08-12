<?php
$finder = PhpCsFixer\Finder::create()->in('src')->in('components');

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
    ]
)->setFinder($finder);
