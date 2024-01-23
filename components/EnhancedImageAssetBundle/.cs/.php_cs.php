<?php
/**
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 */

declare(strict_types=1);
$finder = PhpCsFixer\Finder::create()->in('src');
$finder->notPath('/legacy_files/');
$finder->notPath('/ezpublish_legacy/');
$finder->notPath('/ezpublish_legacy_unused/');
$finder->notPath('/MigrationVersions/');

return PhpCsFixer\Config::create()
    ->setRules(
        [
            '@Symfony'                    => true,
            'binary_operator_spaces'      => [
                'align_equals'       => true,
                'align_double_arrow' => true,
            ],
            'array_syntax'                => ['syntax' => 'short'],
            'pre_increment'               => false,
            'ordered_imports'             => true,
            'phpdoc_order'                => true,
            'linebreak_after_opening_tag' => true,
            'phpdoc_no_package'           => false,
            'phpdoc_inline_tag'           => false,
            'cast_spaces'                 => false,
            'no_superfluous_phpdoc_tags'  => true,
        ]
    )
    ->setFinder($finder);
